<?php
use Illuminate\Support\Facades\Schedule;
use App\Models\ActivityLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ตั้งเวลาให้รันอัตโนมัติวันละครั้ง เพื่อลบ Log ที่เก่ากว่า 90 วัน
Schedule::call(function () {
    ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
})->daily();