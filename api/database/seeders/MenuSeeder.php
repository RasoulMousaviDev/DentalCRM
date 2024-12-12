<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::insert([
            ['name' => 'dashboard', 'title' => 'داشبورد', 'route' => '/', 'icon' => 'table',],
            ['name' => 'users', 'title' => 'کاربران', 'route' => '/users', 'icon' => 'users',],
            ['name' => 'consultants', 'title' => 'مشاوران', 'route' => '/consultants', 'icon' => 'users',],
            ['name' => 'patients', 'title' => 'بیماران', 'route' => '/patients', 'icon' => 'users',],
            ['name' => 'calls', 'title' => 'تماس‌ها', 'route' => '/calls', 'icon' => 'phone',],
            ['name' => 'follow-ups', 'title' => 'وظیفه‌ها', 'route' => '/follow-ups', 'icon' => 'list-check',],
            ['name' => 'appointments', 'title' => 'نوبت‌ها', 'route' => '/appointments', 'icon' => 'calendar-times',],
            ['name' => 'treatment-palns', 'title' => 'طرح‌های درمان', 'route' => '/treatment-plans', 'icon' => 'briefcase',],
            ['name' => 'campains', 'title' => 'کمپین‌ها', 'route' => '/campains', 'icon' => 'megaphone',],
            ['name' => 'treatments', 'title' => 'درمان‌ها', 'route' => '/treatments', 'icon' => 'plus',],
            ['name' => 'sms-templates', 'title' => 'قالب‌های پیامکی', 'route' => '/sms-templates', 'icon' => 'folder-open',],
            ['name' => 'installment-plans', 'title' => 'طرح‌های اقساط', 'route' => '/installment-plans', 'icon' => 'credit-card',],
            ['name' => 'survays', 'title' => 'نظرسنجی‌ها', 'route' => '/survays', 'icon' => 'question',],
            ['name' => 'survay-result', 'title' => 'نتایج نظرسنجی', 'route' => '/survay-result', 'icon' => 'question-circle',],
        ]);
    }
}
