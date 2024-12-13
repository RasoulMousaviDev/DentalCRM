<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Call;
use App\Models\FollowUp;
use App\Models\Model;
use App\Models\Patient;
use App\Models\Status;
use App\Models\TreatmentPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $patient = Model::firstWhere('name', Patient::class);

        $patientStatuses = Status::whereIn('name', [
            'no-status',
            'in-progress',
            'appointment-set',
            'in-person-visit',
            'online-visit',
            'deposit-paid',
            'refunded',
            'under-treatment',
            'treatment-completed',
            'periodic-visit',
            'not-needed'
        ])->get();


        foreach ($patientStatuses as $status) {
            DB::table('model_statuses')->insert([
                [
                    'model_id' => $patient->id,
                    'status_id' => $status->id
                ]
            ]);
        }

        $appointment = Model::firstWhere('name', Appointment::class);

        $appointmentStatuses = Status::whereIn('name', [
            'appointment-set',
            'in-person-visit',
            'online-visit',
            'deposit-paid',
            'refunded',
            'under-treatment',
            'treatment-completed',
            'periodic-visit',
            'canceled',
        ])->get();


        foreach ($appointmentStatuses  as $status) {
            DB::table('model_statuses')->insert([
                [
                    'model_id' => $appointment->id,
                    'status_id' => $status->id
                ]
            ]);
        }

        $call = Model::firstWhere('name', Call::class);

        $callStatuses = Status::whereIn('name', [
            'successful',
            'unsuccessful',
            'wrong-number',
        ])->get();


        foreach ($callStatuses  as $status) {
            DB::table('model_statuses')->insert([
                [
                    'model_id' => $call->id,
                    'status_id' => $status->id
                ]
            ]);
        }

        $followUp = Model::firstWhere('name', FollowUp::class);

        $followUpStatuses = Status::whereIn('name', [
            'pending',
            'done',
            'lost',
        ])->get();


        foreach ($followUpStatuses  as $status) {
            DB::table('model_statuses')->insert([
                [
                    'model_id' => $followUp->id,
                    'status_id' => $status->id
                ]
            ]);
        }


        $treaatmentPlan = Model::firstWhere('name', TreatmentPlan::class);

        $treaatmentPlanStatuses = Status::whereIn('name', [
            'valid',
            'invalid',
        ])->get();


        foreach ($treaatmentPlanStatuses  as $status) {
            DB::table('model_statuses')->insert([
                [
                    'model_id' => $treaatmentPlan->id,
                    'status_id' => $status->id
                ]
            ]);
        }
    }
}
