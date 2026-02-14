<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Newsletter;
use App\Mail\WeeklyNewsletter;
use Carbon\Carbon;
use App\Models\User;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_weekly_newsletter_command()
    {
        Mail::fake();

        // Create a user for the article because Article needs user_id
        $user = User::factory()->create();

        // Create articles published within the last 7 days
        Article::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(2),
        ]);

        // Create a subscriber
        Newsletter::create([
            'email' => 'subscriber@example.com',
            'is_subscribed' => true,
        ]);

        // Run the command
        $this->artisan('newsletter:send-weekly')
            ->assertExitCode(0);

        // Assert that the email was queued
        Mail::assertQueued(WeeklyNewsletter::class, function ($mail) {
            return $mail->hasTo('subscriber@example.com');
        });
    }

    public function test_no_articles_does_not_send_newsletter()
    {
        Mail::fake();

        // Create a subscriber
        Newsletter::create([
            'email' => 'subscriber@example.com',
            'is_subscribed' => true,
        ]);

        // Run the command
        $this->artisan('newsletter:send-weekly')
            ->assertExitCode(0)
            ->expectsOutput('No articles published in the last week. Newsletter skipped.');

        // Assert that no email was queued
        Mail::assertNothingQueued();
    }
}
