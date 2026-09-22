<?php

namespace App\Console\Commands;

use App\Mail\WeeklyNewsletter;
use App\Models\Article;
use App\Models\Newsletter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyNewsletter extends Command
{
    /**
     * --dry-run : tampilkan rencana tanpa mengantre email
     * --limit=N : batasi jumlah subscriber yang diproses (0 = semua)
     *
     * @var string
     */
    protected $signature = 'newsletter:send-weekly
                            {--dry-run : Tampilkan rencana pengiriman tanpa mengantre email}
                            {--limit=0 : Batasi jumlah subscriber yang diproses (0 = semua)}';

    /**
     * @var string
     */
    protected $description = 'Antre newsletter mingguan (3 artikel terbaru) untuk subscriber aktif';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(0, (int) $this->option('limit'));

        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        $articles = Article::where('status', 'published')
            ->whereBetween('published_at', [$startDate, $endDate])
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $randomPosts = Article::where('status', 'published')
            ->whereNotIn('id', $articles->pluck('id'))
            ->inRandomOrder()
            ->take(2)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('Tidak ada artikel terbit dalam 7 hari terakhir — newsletter dilewati.');
            $this->logRun($dryRun, 0, 'tidak ada artikel baru');

            return self::SUCCESS;
        }

        $subscribers = Newsletter::where('is_subscribed', true)
            ->when($limit > 0, fn ($query) => $query->limit($limit))
            ->get();

        if ($subscribers->isEmpty()) {
            $this->info('Tidak ada subscriber aktif.');
            $this->logRun($dryRun, 0, 'tidak ada subscriber');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s | artikel baru: %d | artikel acak: %d | subscriber: %d%s',
            $dryRun ? 'DRY-RUN (tidak ada email dikirim)' : 'KIRIM',
            $articles->count(),
            $randomPosts->count(),
            $subscribers->count(),
            $limit > 0 ? " (dibatasi --limit={$limit})" : ''
        ));

        foreach ($subscribers as $subscriber) {
            if ($dryRun) {
                $this->line('  - ' . $subscriber->email);
                continue;
            }

            Mail::to($subscriber->email)
                ->queue((new WeeklyNewsletter($articles, $randomPosts, $subscriber))->onQueue('emails'));
        }

        $this->info($dryRun ? 'Dry-run selesai — tidak ada email yang diantre.' : 'Semua newsletter sudah masuk antrean (queue: emails).');
        $this->logRun($dryRun, $subscribers->count(), $dryRun ? 'dry-run' : 'diantre');

        return self::SUCCESS;
    }

    private function logRun(bool $dryRun, int $count, string $note): void
    {
        Log::info('newsletter:send-weekly dijalankan', [
            'dry_run' => $dryRun,
            'subscriber_diproses' => $count,
            'keterangan' => $note,
        ]);
    }
}
