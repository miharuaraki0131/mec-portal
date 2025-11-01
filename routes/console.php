<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 古いファイルの自動削除（毎日午前2時）
Schedule::command('files:clean-old --days=90')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();
