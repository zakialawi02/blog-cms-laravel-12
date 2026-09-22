<?php

namespace App\Jobs;

use App\Models\AiArticleGeneration;
use App\Models\Article;
use App\Services\AiService;
use App\Support\ErrorReporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GenerateAiArticleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    protected $generationId;

    /**
     * Jumlah percobaan sebelum job dianggap gagal.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Jeda antar percobaan (detik).
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Batas waktu satu percobaan. Worker generator WAJIB dijalankan
     * dengan --timeout lebih besar dari nilai ini (worker: 660),
     * dan config queue `retry_after` harus lebih besar lagi (900).
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Timeout dihitung sebagai kegagalan (bukan job zombie).
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct($generationId)
    {
        $this->generationId = $generationId;
    }

    /**
     * Execute the job.
     */
    public function handle(AiService $aiService): void
    {
        $generation = AiArticleGeneration::find($this->generationId);

        if (!$generation) {
            Log::error("AiArticleGeneration not found: {$this->generationId}");
            return;
        }

        // Idempotensi: job boleh dicoba ulang, tapi artikelnya tidak boleh dibuat dua kali.
        if ($generation->article_id !== null || $generation->status === 'completed') {
            Log::info('GenerateAiArticleJob dilewati — artikel sudah pernah dibuat', [
                'generation_id' => $generation->id,
                'article_id' => $generation->article_id,
            ]);
            return;
        }

        $generation->update(['status' => 'processing']);

        try {
            $result = $aiService->generateArticle(
                $generation->topic,
                $generation->language,
                $generation->model,
                $generation->provider
            );

            if (!($result['success'] ?? false)) {
                // Gagal dari sisi provider (bukan exception) — tidak perlu dicoba ulang.
                $generation->update([
                    'status' => 'failed',
                    'error_message' => Str::limit((string) ($result['message'] ?? 'Provider gagal tanpa pesan.'), 500),
                ]);

                return;
            }

            // Parse the content
            $rawContent = (string) $result['data'];
            $title = $generation->topic; // Default

            $content = $rawContent; // Default to raw if parsing fails
            $metaKeywords = '';
            $metaDesc = '';

            // Strip Markdown code blocks if present (e.g. ```html ... ```)
            if (preg_match('/^```(?:html|xml)?\s*(.*)\s*```$/s', $rawContent, $matches)) {
                $rawContent = trim($matches[1]);
            }

            // Parser for custom tags
            if (preg_match('/<AiTitle>(.*?)<\/AiTitle>/s', $rawContent, $match)) {
                $title = trim($match[1]);
            }

            if (preg_match('/<AiSEOKeyword>(.*?)<\/AiSEOKeyword>/s', $rawContent, $match)) {
                $metaKeywords = trim($match[1]);
            }

            if (preg_match('/<AiMetaDescription>(.*?)<\/AiMetaDescription>/s', $rawContent, $match)) {
                $metaDesc = trim($match[1]);
            }

            if (preg_match('/<AiMain>(.*?)<\/AiMain>/s', $rawContent, $match)) {
                $content = trim($match[1]);
            } else {
                // Fallback: remove known tags to get content
                $content = preg_replace('/<AiTitle>.*?<\/AiTitle>/s', '', $rawContent);
                $content = preg_replace('/<AiSEOKeyword>.*?<\/AiSEOKeyword>/s', '', $content);
                $content = preg_replace('/<AiMetaDescription>.*?<\/AiMetaDescription>/s', '', $content);
                $content = trim($content);
            }

            // Generate unique slug
            $slug = Str::slug($title);
            $originalSlug = $slug;
            $count = 1;
            while (Article::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Create Article
            $article = Article::create([
                'user_id' => $generation->user_id,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'status' => 'pending',
                'excerpt' => $metaDesc ?: Str::limit(strip_tags($content), 150),
                'meta_title' => $title, // Assuming meta_title similar to title
                'meta_desc' => $metaDesc,
                'meta_keywords' => $metaKeywords,
            ]);

            $generation->update([
                'status' => 'completed',
                'result' => $result['data'],
                'article_id' => $article->id,
            ]);
        } catch (Throwable $e) {
            // Jangan telan exception: biarkan queue mencoba ulang sesuai $tries,
            // lalu failed() yang menandai status akhirnya.
            Log::error('GenerateAiArticleJob gagal (akan dicoba ulang bila masih ada sisa percobaan)', [
                'generation_id' => $generation->id,
                'attempt' => $this->job?->attempts(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Dipanggil framework setelah percobaan terakhir gagal.
     */
    public function failed(?Throwable $e): void
    {
        AiArticleGeneration::whereKey($this->generationId)->update([
            'status' => 'failed',
            'error_message' => Str::limit((string) $e?->getMessage(), 500),
            'updated_at' => now(),
        ]);

        if ($e) {
            ErrorReporter::reference($e, 'GenerateAiArticleJob::failed');
        }
    }
}
