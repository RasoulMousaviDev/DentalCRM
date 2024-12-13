<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phoneConsultant = Role::firstWhere('name', 'phone-consultant');

        $phoneConsultantMenu = Menu::whereIn('name', [
            'dashboard',
            'patients',
            'calls',
            'follow-ups',
            'treatment-palns',
            'appointments',
        ])->get();

        foreach ($phoneConsultantMenu  as $menu) {
            DB::table('role_menu')->insert([
                [
                    'role_id' => $phoneConsultant->id,
                    'menu_id' => $menu->id
                ]
            ]);
        }

        $onSiteConsultant = Role::firstWhere('name', 'on-site-consultant');

        $onSiteConsultantMenu = Menu::whereIn('name', [
            'dashboard',
            'appointments',
            'treatment-palns',
        ])->get();

        foreach ($onSiteConsultantMenu  as $menu) {
            DB::table('role_menu')->insert([
                [
                    'role_id' => $onSiteConsultant->id,
                    'menu_id' => $menu->id
                ]
            ]);
        }

        $reception = Role::firstWhere('name', 'reception');

        $receptionMenu = Menu::firstWhere('name', 'appointments');

        DB::table('role_menu')->insert([
            [
                'role_id' => $reception->id,
                'menu_id' => $receptionMenu->id
            ]
        ]);

        $appointment = Role::firstWhere('name', 'appointment');

        $appointmentMenu = Menu::firstWhere('name', 'appointments');

        DB::table('role_menu')->insert([
            [
                'role_id' => $appointment->id,
                'menu_id' => $appointmentMenu->id
            ]
        ]);

        $marketer = Role::firstWhere('name', 'marketer');

        $marketerMenu = Menu::firstWhere('name', 'campains');

        DB::table('role_menu')->insert([
            [
                'role_id' => $marketer->id,
                'menu_id' => $marketerMenu->id
            ]
        ]);


        $admin = Role::firstWhere('name', 'admin');

        $superAdmin = Role::firstWhere('name', 'super-admin');

        $menus = Menu::whereIn('name', [
            'dashboard',
            'users',
            'consultants',
            'patients',
            'calls',
            'follow-ups',
            'appointments',
            'treatment-palns',
            'campains',
            'treatments',
            'sms-templates',
            'installment-plans',
            'survays',
            'survay-result',
        ])->get();

        foreach ($menus  as $menu) {
            DB::table('role_menu')->insert([
                [
                    'role_id' => $admin->id,
                    'menu_id' => $menu->id
                ]
            ]);
            DB::table('role_menu')->insert([
                [
                    'role_id' => $superAdmin->id,
                    'menu_id' => $menu->id
                ]
            ]);
        }
    }
}
