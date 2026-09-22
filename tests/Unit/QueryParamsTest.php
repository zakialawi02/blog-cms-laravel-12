<?php

use App\Support\QueryParams;
use Illuminate\Http\Request;

uses(Tests\TestCase::class);

it('memakai default ketika kolom sort tidak dikenal', function () {
    $request = Request::create('/?sort=kolom_ngawur%3Bdrop');

    expect(QueryParams::sort($request, ['published_at', 'title'], 'published_at'))->toBe('published_at')
        ->and(QueryParams::sort(Request::create('/?sort=title'), ['published_at', 'title'], 'published_at'))->toBe('title');
});

it('arah sort selalu asc atau desc', function () {
    expect(QueryParams::direction(Request::create('/?direction=nope')))->toBe('desc')
        ->and(QueryParams::direction(Request::create('/?direction=ASC')))->toBe('asc')
        ->and(QueryParams::direction(Request::create('/')))->toBe('desc');
});

it('membatasi jumlah per halaman', function () {
    expect(QueryParams::perPage(Request::create('/?limit=100000'), 9, 100))->toBe(100)
        ->and(QueryParams::perPage(Request::create('/?limit=0'), 9, 100))->toBe(1)
        ->and(QueryParams::perPage(Request::create('/?limit=abc'), 9, 100))->toBe(9)
        ->and(QueryParams::perPage(Request::create('/'), 9, 100))->toBe(9)
        ->and(QueryParams::perPage(Request::create('/?limit=25'), 9, 100))->toBe(25);
});

it('limit opsional: null kalau tidak dikirim, dibatasi kalau dikirim', function () {
    expect(QueryParams::optionalLimit(Request::create('/')))->toBeNull()
        ->and(QueryParams::optionalLimit(Request::create('/?limit=abc')))->toBeNull()
        ->and(QueryParams::optionalLimit(Request::create('/?limit=3')))->toBe(3)
        ->and(QueryParams::optionalLimit(Request::create('/?limit=9999'), 100))->toBe(100);
});

it('mendukung nama parameter arah yang berbeda (mis. order)', function () {
    expect(QueryParams::direction(Request::create('/?order=asc'), 'desc', 'order'))->toBe('asc')
        ->and(QueryParams::direction(Request::create('/?order=nope'), 'desc', 'order'))->toBe('desc');
});
