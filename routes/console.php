<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

$password = env('DB_PASSWORD');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// //job queue default seperti email, media library, rutin
// Schedule::command('queue:work --stop-when-empty --queue=default')
//     ->everyMinute();

// // job whatsapp
// Schedule::command('queue:work --stop-when-empty --queue=whatsapp')
//     ->withoutOverlapping()
//     ->everyMinute();
// // no use
// // ->sendOutputTo(storage_path() . '/logs/whatsapp.log')
// // --stop-when-empty

// php artisan queue:work --queue=default,whatsapp --sleep=1 --tries=3
