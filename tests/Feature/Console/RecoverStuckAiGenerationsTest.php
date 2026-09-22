<?php

use App\Models\AiArticleGeneration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('menandai generasi processing yang nyangkut sebagai failed', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $stuck = AiArticleGeneration::create([
        'user_id' => $user->id,
        'topic' => 'Nyangkut',
        'language' => 'id',
        'model' => 'm',
        'provider' => 'gemini',
        'status' => 'processing',
    ]);

    $fresh = AiArticleGeneration::create([
        'user_id' => $user->id,
        'topic' => 'Masih jalan',
        'language' => 'id',
        'model' => 'm',
        'provider' => 'gemini',
        'status' => 'processing',
    ]);

    // Bikin baris pertama seolah-olah sudah lama tidak tersentuh.
    DB::table('ai_article_generations')
        ->where('id', $stuck->id)
        ->update(['updated_at' => now()->subMinutes(90)]);

    $this->artisan('ai:recover-stuck --minutes=30')->assertSuccessful();

    expect($stuck->refresh()->status)->toBe('failed')
        ->and($stuck->error_message)->toContain('Dipulihkan otomatis')
        ->and($fresh->refresh()->status)->toBe('processing');
});
