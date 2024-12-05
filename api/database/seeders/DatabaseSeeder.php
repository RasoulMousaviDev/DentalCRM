<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            MenuSeeder::class,
            RoleMenuSeeder::class,
            ModelSeeder::class,
            StatusSeeder::class,
            ModelStatusSeeder::class,
            ProvinceSeeder::class,
            UserSeeder::class,
            LeadSourceSeeder::class,
            HolidaySeeder::class
        ]);
    }
}
