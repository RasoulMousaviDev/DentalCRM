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
            ['value' => 'بدون وضعیت', 'severity' => 'secondary',],
            ['value' => 'درحال پیگیری', 'severity' => 'info',],
            ['value' => 'نوبت داده شد', 'severity' => 'warn',],
            ['value' => 'ویزیت حضوری شد', 'severity' => 'info',],
            ['value' => 'ویزیت مجازی شد', 'severity' => 'info',],
            ['value' => 'بیعانه داد', 'severity' => 'secondary',],
            ['value' => 'درحال درمان', 'severity' => 'warn',],
            ['value' => 'پایان درمات', 'severity' => 'success',],
            ['value' => 'نیاز ندارد', 'severity' => 'danger',],
            ['value' => 'شماره اشتباه است', 'severity' => 'danger',],
            ['value' => 'در انتظار', 'severity' => 'warn'],
            ['value' => 'عودت داده شده', 'severity' => 'danger'],
            ['value' => 'پرداخت شده', 'severity' => 'success'],
            ['value' => 'عودت داده شده', 'severity' => 'danger'],
            ['value' => 'در انتظار', 'severity' => 'warn'],
            ['value' => 'ویزیت مجازی شد', 'severity' => 'success'],
            ['value' => 'ویزیت حضوری شد', 'severity' => 'success'],
            ['value' => 'از دست رفته', 'severity' => 'danger'],
            ['value' => 'لغو شده', 'severity' => 'danger'],
        ]);
    }
}
