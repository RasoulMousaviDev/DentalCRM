<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ["name" => "super-admin", "title" => "مدیر کل"],
            ["name" => "admin", "title" => "مدیر"],
            ["name" => "phone-consultant", "title" => "مشاور نلفنی"],
            ["name" => "on-site-consultant", "title" => "مشاور حضوری"],
            ["name" => "reception", "title" => "پذیرش"],
            ["name" => "appointment", "title" => "نوبت دهی"],
            ["name" => "marketer", "title" => "بازاریاب"]
        ]);
    }
}
