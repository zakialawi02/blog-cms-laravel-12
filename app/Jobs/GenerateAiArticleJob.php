<?php

namespace App\Jobs;

use App\Models\AiArticleGeneration;
use App\Services\AiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateAiArticleJob implements ShouldQueue
{
    use Queueable;

    protected $generationId;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

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

        $generation->update(['status' => 'processing']);

        try {
            $result = $aiService->generateArticle(
                $generation->topic,
                $generation->language,
                $generation->model,
                $generation->provider
            );

            if ($result['success']) {
                $generation->update([
                    'status' => 'completed',
                    'result' => $result['data'],
                ]);

                // Parse the content
                $rawContent = $result['data'];
                $title = $generation->topic; // Default
                $slug = \Illuminate\Support\Str::slug($generation->topic) . '-' . \Illuminate\Support\Str::random(6);
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
                    $slug = \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(6);
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

                // Create Article
                \App\Models\Article::create([
                    'user_id' => $generation->user_id,
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'status' => 'pending',
                    'excerpt' => $metaDesc ?: \Illuminate\Support\Str::limit(strip_tags($content), 150),
                    'meta_title' => $title, // Assuming meta_title similar to title
                    'meta_desc' => $metaDesc,
                    'meta_keywords' => $metaKeywords,
                ]);
            } else {
                $generation->update([
                    'status' => 'failed',
                    'error_message' => $result['message'],
                ]);
            }
        } catch (\Exception $e) {
            $generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("AI Generation Job Failed: " . $e->getMessage());
        }
    }
}
