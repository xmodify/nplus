<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\MainSetting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    MainSetting::updateOrCreate(
        ['name' => 'laravel_scheduler_last_run'],
        [
            'name_th' => 'เวลาที่ Scheduler รันล่าสุด',
            'value' => now()->toDateTimeString()
        ]
    );
})->everyMinute();
