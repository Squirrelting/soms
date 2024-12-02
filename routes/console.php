<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::command('backup:clean')->daily()->at('01:00');
// Schedule::command('backup:run --db-only')->daily()->at('20:13');
Schedule::command('backup:run --only-db')->everyMinute();


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
