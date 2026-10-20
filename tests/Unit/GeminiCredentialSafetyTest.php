<?php

use App\Services\AiService;
use Illuminate\Http\Client\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->geminiKey = 'gemini-regression-test-secret';
    config(['services.gemini.key' => $this->geminiKey]);

    $this->geminiLogs = new TestHandler();
    Log::swap(new Logger(new MonologLogger('gemini-test', [$this->geminiLogs])));
    Http::preventStrayRequests();
});

function geminiTestResult(AiService $service, string $type): array
{
    return $type === 'text'
        ? $service->generateArticle('Topik uji')
        : $service->generateImage('Gambar uji')->getData(true);
}

function geminiTestLogs(TestHandler $handler): string
{
    $formatter = new LineFormatter(null, null, true, true);

    return implode('', array_map(fn ($record) => $formatter->format($record), $handler->getRecords()));
}

it('mengirim credential Gemini melalui header tanpa memasukkannya ke URL', function (string $type) {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
        'candidates' => [['content' => ['parts' => [[
            'text' => '<AiTitle>Uji</AiTitle><AiMain><p>Isi.</p></AiMain>',
            'inlineData' => ['data' => 'aW1hZ2U=', 'mimeType' => 'image/png'],
        ]]]]],
    ])]);

    $result = geminiTestResult(new AiService(), $type);

    expect($result['success'])->toBeTrue();
    Http::assertSent(fn (Request $request) =>
        $request->hasHeader('x-goog-api-key', $this->geminiKey)
        && ! str_contains($request->url(), $this->geminiKey)
        && parse_url($request->url(), PHP_URL_QUERY) === null
    );
    Http::assertSentCount(1);
})->with(['text', 'image']);

it('tidak membocorkan credential yang dipantulkan dalam error provider ke log', function (string $type) {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
        'error' => ['message' => 'Invalid API key: '.$this->geminiKey],
    ], 403)]);

    $result = geminiTestResult(new AiService(), $type);
    $logs = geminiTestLogs($this->geminiLogs);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('ref:')->not->toContain($this->geminiKey)
        ->and($logs)->toContain('403', 'Gemini')->not->toContain($this->geminiKey);
})->with(['text', 'image']);

it('tidak mencatat respons mentah yang mengandung credential ketika konten kosong', function (string $type) {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
        'diagnostic' => $this->geminiKey,
    ])]);

    $result = geminiTestResult(new AiService(), $type);

    expect($result['success'])->toBeFalse()
        ->and(geminiTestLogs($this->geminiLogs))->toContain('empty')->not->toContain($this->geminiKey);
})->with(['text', 'image']);

it('menangani kegagalan koneksi Gemini tanpa credential di URL exception atau log', function (string $type) {
    Http::fake(function (Request $request) {
        throw new \Illuminate\Http\Client\ConnectionException('Connection failed for '.$request->url());
    });

    $result = geminiTestResult(new AiService(), $type);
    $logs = geminiTestLogs($this->geminiLogs);

    expect($result['success'])->toBeFalse()
        ->and($logs)->toContain('Connection')->not->toContain($this->geminiKey);
})->with(['text', 'image']);
