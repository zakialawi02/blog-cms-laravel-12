<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Newsletter;
use App\Mail\WeeklyNewsletter;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly newsletter with latest 3 articles to subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        $articles = Article::where('status', 'published')
            ->whereBetween('published_at', [$startDate, $endDate])
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('No articles published in the last week. Newsletter skipped.');
            return;
        }

        $subscribers = Newsletter::where('is_subscribed', true)->get();

        if ($subscribers->isEmpty()) {
            $this->info('No subscribers found.');
            return;
        }

        $this->info("Found {$articles->count()} articles and {$subscribers->count()} subscribers. Sending newsletters...");

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)
                ->queue((new WeeklyNewsletter($articles, $subscriber))->onQueue('emails'));
        }

        $this->info('All newsletters have been queued.');
    }
}
