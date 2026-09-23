<?php

use Database\Seeders\WebSettingsSeeder;

/**
 * Regression test for Sentry issue BLOG-CMS-LARAVEL-3T.
 *
 * GET /blog/archive/2025/remixicon.ttf (bot crawling the site) hit the
 * `/blog/archive/{year}/{month}` route with a non-numeric "month" segment.
 * Because the controller declared `int $month`, PHP raised a TypeError
 * (500) before the controller's own numeric validation could answer 404.
 */

beforeEach(function () {
    $this->seed(WebSettingsSeeder::class);
});

it('returns 404 when the archive month is not numeric', function () {
    $this->get('/blog/archive/2025/remixicon.ttf')->assertNotFound();
});

it('returns 404 when the archive year is not numeric', function () {
    $this->get('/blog/archive/abcd')->assertNotFound();
    $this->get('/blog/archive/abcd/09')->assertNotFound();
});

it('returns 404 when the archive year is not four digits long', function () {
    $this->get('/blog/archive/25')->assertNotFound();
    $this->get('/blog/archive/25/09')->assertNotFound();
});

it('returns 404 when the archive month is outside 1-12', function () {
    $this->get('/blog/archive/2025/0')->assertNotFound();
    $this->get('/blog/archive/2025/13')->assertNotFound();
});

it('still serves a valid archive month', function () {
    $this->get('/blog/archive/2025/09')->assertOk();
});

it('still serves a valid archive year', function () {
    $this->get('/blog/archive/2025')->assertOk();
});
