<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardChartsRequest;
use App\Models\Appointment;
use App\Models\Call;
use App\Models\Deposit;
use App\Models\FollowUp;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function charts(DashboardChartsRequest $request)
    {
        $date = $request->only('from', 'to');

        $period = collect($date)->values();

        $patientCount = Patient::whereBetween('created_at', $period)->count();

        $callCount = Call::whereBetween('created_at', $period)->count();

        $appointmentCount = Appointment::whereBetween('created_at', $period)->count();

        $followUpCount = FollowUp::whereBetween('created_at', $period)->count();

        $patientStatuses = Patient::without(['mobiles', 'city', 'province', 'leadSource', 'status'])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at',  $period)
            ->groupBy('status')
            ->pluck('count', 'status');

        $callStatuses = Call::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at',  $period)
            ->groupBy('status')
            ->pluck('count', 'status');

        $patientGenders = Patient::without(['mobiles', 'city', 'province', 'leadSource', 'status'])
            ->select('gender', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at',  $period)
            ->groupBy('gender')
            ->pluck('count', 'gender');

        $patientLeadSources = Patient::without(['mobiles', 'city', 'province', 'leadSource', 'status'])
            ->select('lead_source', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at',  $period)
            ->groupBy('lead_source')
            ->pluck('count', 'lead_source');

        $patientTreatments = DB::table('patient_treatments')
            ->join('patients', 'patient_treatments.patient_id', '=', 'patients.id')
            ->whereBetween('patients.created_at', $period)
            ->groupBy('patient_treatments.treatment_id')
            ->select('patient_treatments.treatment_id', DB::raw('COUNT(patient_treatments.patient_id) as patient_count'))
            ->pluck('patient_count', 'treatment_id');

        $appointmentsStatusCount = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', $period)
            ->groupBy('status')
            ->pluck('count', 'status');

        $depositsStatusCount = Deposit::select('status', DB::raw('COUNT(DISTINCT appointment) as count'))
            ->whereBetween('created_at', $period)
            ->groupBy('status')
            ->pluck('count', 'status');

        $receptionReport = $appointmentsStatusCount->merge($depositsStatusCount);

        return response()->json(compact(
            'patientLeadSources',
            'patientTreatments',
            'receptionReport',
            'appointmentCount',
            'patientStatuses',
            'patientGenders',
            'followUpCount',
            'patientCount',
            'callStatuses',
            'callCount',
        ));
    }
}
