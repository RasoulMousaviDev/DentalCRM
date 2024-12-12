<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Call;
use App\Models\Campain;
use App\Models\City;
use App\Models\FollowUp;
use App\Models\LeadSource;
use App\Models\Menu;
use App\Models\Model;
use App\Models\OTPCode;
use App\Models\Patient;
use App\Models\PatientMobile;
use App\Models\Permission;
use App\Models\Photo;
use App\Models\Province;
use App\Models\Role;
use App\Models\SMSTemplate;
use App\Models\Status;
use App\Models\Survay;
use App\Models\SurvayQuestion;
use App\Models\SurvayQuestionAnswer;
use App\Models\Treatment;
use App\Models\TreatmentPlan;
use App\Models\TreatmentService;
use App\Models\TreatmentServiceOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::insert([
            ['name' => Appointment::class],
            ['name' => Call::class],
            ['name' => Campain::class],
            ['name' => City::class],
            ['name' => FollowUp::class],
            ['name' => LeadSource::class],
            ['name' => Menu::class],
            ['name' => Model::class],
            ['name' => OTPCode::class],
            ['name' => Patient::class],
            ['name' => PatientMobile::class],
            ['name' => Permission::class],
            ['name' => Photo::class],
            ['name' => Province::class],
            ['name' => Role::class],
            ['name' => SMSTemplate::class],
            ['name' => Status::class],
            ['name' => Survay::class],
            ['name' => SurvayQuestion::class],
            ['name' => SurvayQuestionAnswer::class],
            ['name' => Treatment::class],
            ['name' => TreatmentPlan::class],
            ['name' => TreatmentService::class],
            ['name' => TreatmentServiceOption::class],
        ]);
    }
}
