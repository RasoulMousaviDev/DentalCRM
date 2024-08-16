<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Rasoul Mousavi',
            'email' => 'rasoulmousavidev@gmail.com',
            'phone' => '09102836620',
            'password' => Hash::make('123456789')
        ]);
    }
}
