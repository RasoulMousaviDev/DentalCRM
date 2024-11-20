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
            ['name' => 'no-status', 'value' => 'بدون وضعیت', 'severity' => 'secondary'],
            ['name' => 'in-progress', 'value' => 'درحال پیگیری', 'severity' => 'info'],
            ['name' => 'appointment-set', 'value' => 'نوبت داده شد', 'severity' => 'warn'],
            ['name' => 'in-person-visit', 'value' => 'ویزیت حضوری شد', 'severity' => 'info'],
            ['name' => 'online-visit', 'value' => 'ویزیت آنلاین شد', 'severity' => 'info'],
            ['name' => 'deposit-paid', 'value' => 'بیعانه داد', 'severity' => 'secondary'],
            ['name' => 'under-treatment', 'value' => 'درحال درمان', 'severity' => 'warn'],
            ['name' => 'treatment-completed', 'value' => 'پایان درمان', 'severity' => 'success'],
            ['name' => 'not-needed', 'value' => 'نیاز ندارد', 'severity' => 'danger'],
            ['name' => 'successful', 'value' => 'موفق', 'severity' => 'success'],
            ['name' => 'unsuccessful', 'value' => 'ناموفق', 'severity' => 'danger'],
            ['name' => 'wrong-number', 'value' => 'شماره اشتباه است', 'severity' => 'help'],
            ['name' => 'waiting', 'value' => 'در انتظار', 'severity' => 'warn'],
            ['name' => 'paid', 'value' => 'پرداخت شده', 'severity' => 'success'],
            ['name' => 'refunded', 'value' => 'عودت داده شده', 'severity' => 'danger'],
            ['name' => 'lost', 'value' => 'از دست رفته', 'severity' => 'danger'],
            ['name' => 'canceled', 'value' => 'لغو شده', 'severity' => 'danger'],
            ['name' => 'completed', 'value' => 'انجام شد', 'severity' => 'success'],
            ['name' => 'valid', 'value' => 'معتبر', 'severity' => 'success'],
            ['name' => 'invalid', 'value' => 'نامعتبر', 'severity' => 'danger'],
        ]);
    }
}
