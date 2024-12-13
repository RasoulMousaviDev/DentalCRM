<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardChartsRequest;
use App\Models\Appointment;
use App\Models\Call;
use App\Models\Deposit;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function charts(DashboardChartsRequest $request)
    {
        $date = $request->only('from', 'to');

        $period = collect($date)->values()->map(fn($d, $i) => Carbon::parse($d)
            ->setTimezone('Asia/Tehran')
            ->{$i ? 'endOfDay' : 'startOfDay'}()
            ->format('Y-m-d H:i:s'));

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'on-site-consultant'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $response = [];

        if ($user->role->name != 'on-site-consultant') {
            $patientCount = Patient::whereBetween('created_at', $period)->when(!$isAdmin, function ($query) use ($user) {
                $query->where('user', $user->id);
            })->count();

            $callCount = Call::whereBetween('created_at', $period)
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('patient', function (Builder $query) use ($user) {
                        $query->where('user', $user->id);
                    });
                })->count();

            $appointmentCount = Appointment::whereBetween('created_at', $period)
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('patient', function (Builder $query) use ($user) {
                        $query->where('user', $user->id);
                    });
                })->count();

            $followUpCount = FollowUp::whereBetween('created_at', $period)
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('patient', function (Builder $query) use ($user) {
                        $query->where('user', $user->id);
                    });
                })->count();

            $patientStatuses = Patient::select('status', DB::raw('COUNT(*) as count'))
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->where('user', $user->id);
                })
                ->whereBetween('created_at',  $period)
                ->groupBy('status')
                ->pluck('count', 'status');

            $callStatuses = Call::select('status', DB::raw('COUNT(*) as count'))
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('patient', function (Builder $query) use ($user) {
                        $query->where('user', $user->id);
                    });
                })
                ->whereBetween('created_at',  $period)
                ->groupBy('status')
                ->pluck('count', 'status');

            $patientGenders = Patient::select('gender', DB::raw('COUNT(*) as count'))
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->where('user', $user->id);
                })
                ->whereBetween('created_at',  $period)
                ->groupBy('gender')
                ->pluck('count', 'gender');

            $patientLeadSources = Patient::select('lead_source', DB::raw('COUNT(*) as count'))
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->where('user', $user->id);
                })
                ->whereBetween('created_at',  $period)
                ->groupBy('lead_source')
                ->pluck('count', 'lead_source');

            $patientTreatments = DB::table('patient_treatments')
                ->join('patients', 'patient_treatments.patient_id', '=', 'patients.id')
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->where('patients.user', $user->id);
                })
                ->whereBetween('patients.created_at', $period)
                ->groupBy('patient_treatments.treatment_id')
                ->select('patient_treatments.treatment_id', DB::raw('COUNT(patient_treatments.patient_id) as patient_count'))
                ->pluck('patient_count', 'treatment_id');

            $response = compact(
                'patientLeadSources',
                'patientTreatments',
                'appointmentCount',
                'patientStatuses',
                'patientGenders',
                'followUpCount',
                'patientCount',
                'callStatuses',
                'callCount',
            );
        }
        $appointmentsStatusCount = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('patient', function (Builder $query) use ($user) {
                    $query->where('user', $user->id);
                });
            })
            ->whereBetween('created_at', $period)
            ->groupBy('status')
            ->pluck('count', 'status');

        $statuses = [
            'appointment' => Appointment::model()->statuses,
            'patient' => Patient::model()->statuses,
            'call' => Call::model()->statuses,
        ];

        $response['appointmentsStatusCount'] = $appointmentsStatusCount;
        $response['statuses'] = $statuses;

        return response()->json($response);
    }
}
