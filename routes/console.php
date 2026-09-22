<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('newsletter:send-weekly')->weeklyOn(5, '08:00');

// Pemulihan generasi AI yang nyangkut di status "processing" (mis. worker mati di tengah job).
Schedule::command('ai:recover-stuck --minutes=30')->everyFifteenMinutes();
