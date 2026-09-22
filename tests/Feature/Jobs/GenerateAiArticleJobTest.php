<?php

use App\Jobs\GenerateAiArticleJob;
use App\Models\AiArticleGeneration;
use App\Models\Article;
use App\Models\User;
use App\Services\AiService;

function fakeAiService(array $result): AiService
{
    $mock = Mockery::mock(AiService::class);
    $mock->shouldReceive('generateArticle')->andReturn($result);

    return $mock;
}

function makeGeneration(User $user, array $attrs = []): AiArticleGeneration
{
    return AiArticleGeneration::create(array_merge([
        'user_id' => $user->id,
        'topic' => 'Topik Uji',
        'language' => 'id',
        'model' => 'gemini-3-flash-preview',
        'provider' => 'gemini',
        'status' => 'pending',
    ], $attrs));
}

it('membuat artikel dan menandai generasi selesai', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $generation = makeGeneration($user);

    (new GenerateAiArticleJob($generation->id))->handle(fakeAiService([
        'success' => true,
        'data' => '<AiTitle>Judul Hasil AI</AiTitle><AiMain><p>Isi artikel.</p></AiMain>',
    ]));

    $generation->refresh();

    expect($generation->status)->toBe('completed')
        ->and($generation->article_id)->not->toBeNull();

    $article = Article::findOrFail($generation->article_id);

    expect($article->title)->toBe('Judul Hasil AI')
        ->and($article->status)->toBe('pending')
        ->and($article->content)->toContain('Isi artikel.');
});

it('tidak membuat artikel kedua ketika job dijalankan ulang', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $generation = makeGeneration($user);

    $payload = [
        'success' => true,
        'data' => '<AiTitle>Judul Idempoten</AiTitle><AiMain><p>Isi.</p></AiMain>',
    ];

    (new GenerateAiArticleJob($generation->id))->handle(fakeAiService($payload));
    $afterFirstRun = Article::count();

    (new GenerateAiArticleJob($generation->id))->handle(fakeAiService($payload));

    expect(Article::count())->toBe($afterFirstRun)
        ->and($afterFirstRun)->toBe(1);
});

it('melempar exception supaya queue bisa mencoba ulang', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $generation = makeGeneration($user);

    $mock = Mockery::mock(AiService::class);
    $mock->shouldReceive('generateArticle')->andThrow(new RuntimeException('provider mati'));

    expect(fn () => (new GenerateAiArticleJob($generation->id))->handle($mock))
        ->toThrow(RuntimeException::class);

    expect($generation->refresh()->status)->toBe('processing');
});

it('menandai generasi gagal lewat failed()', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $generation = makeGeneration($user, ['status' => 'processing']);

    (new GenerateAiArticleJob($generation->id))->failed(new RuntimeException('habis percobaan'));

    $generation->refresh();

    expect($generation->status)->toBe('failed')
        ->and($generation->error_message)->toContain('habis percobaan');
});
