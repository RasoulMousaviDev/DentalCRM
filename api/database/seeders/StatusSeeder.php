<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            // Patient statuses
            ['name' => 'no-status', 'value' => 'بدون وضعیت', 'severity' => 'secondary'],
            ['name' => 'in-progress', 'value' => 'درحال پیگیری', 'severity' => 'info'],
            ['name' => 'appointment-in-person', 'value' => 'نوبت حضوری دارد', 'severity' => 'warn'],
            ['name' => 'in-person-visit', 'value' => 'ویزیت حضوری شد', 'severity' => 'info'],
            ['name' => 'online-visit', 'value' => 'ویزیت آنلاین شد', 'severity' => 'info'],
            ['name' => 'deposit-paid', 'value' => 'بیعانه داده شد', 'severity' => 'success'],
            ['name' => 'refunded', 'value' => 'عودت داده شد', 'severity' => 'danger'],
            ['name' => 'under-treatment', 'value' => 'درحال درمان', 'severity' => 'warn'],
            ['name' => 'treatment-completed', 'value' => 'پایان درمان', 'severity' => 'success'],
            ['name' => 'periodic-visit', 'value' => 'ویزیت دوره ای', 'severity' => 'info'],
            ['name' => 'not-needed', 'value' => 'نیاز ندارد', 'severity' => 'danger'],
            ['name' => 'canceled', 'value' => 'لغو شده', 'severity' => 'danger'],
            // Call status
            ['name' => 'successful', 'value' => 'موفق', 'severity' => 'success'],
            ['name' => 'unsuccessful', 'value' => 'ناموفق', 'severity' => 'danger'],
            ['name' => 'wrong-number', 'value' => 'شماره اشتباه است', 'severity' => 'secondary'],
            // Follow up status
            ['name' => 'pending', 'value' => 'در انتظار', 'severity' => 'warn'],
            ['name' => 'done', 'value' => 'انجام شد', 'severity' => 'success'],
            ['name' => 'lost', 'value' => 'از دست رفته', 'severity' => 'danger'],
            // Treatment plan statuses
            ['name' => 'valid', 'value' => 'معتبر', 'severity' => 'success'],
            ['name' => 'invalid', 'value' => 'نامعتبر', 'severity' => 'danger'],
            // Patient new status
            ['name' => 'appointment-online', 'value' => 'نوبت آنلاین دارد', 'severity' => 'info'],
        ]);
    }
}
