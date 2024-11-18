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
            ['name' => 'patients', 'title' => 'بیماران', 'route' => '/patients', 'icon' => 'users',],
            ['name' => 'appointments', 'title' => 'نوبت ها', 'route' => '/appointments', 'icon' => 'calendar-times',],
            ['name' => 'campains', 'title' => 'کمپین ها', 'route' => '/campains', 'icon' => 'megaphone',],
            ['name' => 'survays', 'title' => 'نظرسنجی ها', 'route' => '/survays', 'icon' => 'question',],
            ['name' => 'sms-templates', 'title' => 'قالب‌های پیامکی', 'route' => '/sms-templates', 'icon' => 'folder-open',],
            ['name' => 'treatment-palns', 'title' => 'طرحهای درمان', 'route' => '/treatment-plans', 'icon' => 'briefcase',],
            ['name' => 'treatments', 'title' => 'درمان ها', 'route' => '/treatments', 'icon' => 'plus',],
            ['name' => 'follow-ups', 'title' => 'وظیفه ها', 'route' => '/follow-ups', 'icon' => 'list-check',],
            ['name' => 'calls', 'title' => 'تماس ها', 'route' => '/calls', 'icon' => 'phone',],
        ]);
    }
}
