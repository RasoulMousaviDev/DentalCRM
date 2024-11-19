<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeadSource::insert([
            ['title' => 'پیامک - تماس'],
            ['title' => 'پیامک - عدد'],
            ['title' => 'وب'],
            ['title' => 'دوستان'],
            ['title' => 'ویزیت مجازی'],
            ['title' => 'بیلبورد'],
            ['title' => 'اینستاگرام'],
            ['title' => 'تلگرام'],
            ['title' => 'نامشخص'],
        ]);
    }
}
