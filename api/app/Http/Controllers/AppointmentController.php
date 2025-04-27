<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient'];

        $dateFields = ['due_date', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'on-site-consultant', 'reception', 'appointment'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $appointments = Appointment::with([
            'treatments:id,title',
            'patient:id,firstname,lastname',
            'patient.mobiles',
            'patient.treatmentPlans.user:id,name',
            'status:id,name,value,severity',
        ]);

        if ($isAdmin) $appointments = $appointments->with('patient.user:id,name')
            ->when($request->input('phone_consultant'), function ($query, $user) {
                $query->whereHas('patient.user', function (Builder $query) use ($user) {
                    $query->where('name', 'like', "%{$user}%");
                });
            })->when($request->input('on_site_consultant'), function ($query, $user) {
                $query->whereHas('patient.treatmentPlans.user', function (Builder $query) use ($user) {
                    $query->where('name', 'like', "%{$user}%");
                });
            })->when($request->input('no_phone_consultant'), function ($query, $value) {
                if ($value) $query->whereHas('patient.user.roles', function (Builder $query) {
                    $role = Role::firstWhere('name', "phone-consultant");
                    $query->whereNot('id', $role->id);
                });
            })->when($request->input('no_treatment_plan'), function ($query, $value) {
                $query->whereHas('patient', function (Builder $query) use ($value) {
                    if ($value) $query->doesntHave('treatmentPlans');
                });
            });
        else $appointments = $appointments->whereHas('patient', function (Builder $query) use ($user) {
            $query->where('user', $user->id);
        });


        foreach ($searchableFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $query->whereHas('patient', function (Builder $query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            });
        }

        foreach ($filterableFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value)->when($field === 'due_date', function ($query) {
                    $query->orderBy('due_date');
                });
            });
        }

        $appointments->when($request->input('status'), function ($query, $value) {
            $query->whereIn('status', $value);
        });

        $appointments->when($request->input('treatment'), function ($query, $treatment) {
            $query->whereHas('treatment', function (Builder $query) use ($treatment) {
                $query->where('id', $treatment);
            });
        });

        $appointments->when($request->input('mobile'), function ($query, $mobile) {
            $query->whereHas('patient', function (Builder $query) use ($mobile) {
                $query->whereHas('mobiles', function (Builder $query) use ($mobile) {
                    $query->where('number', 'like', "%{$mobile}%");
                });
            });
        });

        $appointments = $appointments->orderBy('due_date', 'desc')->paginate($rows);

        $response = $this->paginate($appointments);

        $response['statuses'] = Appointment::model()->statuses;

        return response()->json($response);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $form = $request->only(['due_date', 'desc', 'status']);

        $patient = Patient::find($request->get('patient'));

        $appointment = $patient->appointments()->create($form);

        $treatments = $request->get('treatments');

        $appointment->treatments()->attach($treatments);

        $appointment = $patient->appointments()->with([
            'treatments:id,title',
            'patient:id,firstname,lastname,user',
            'patient.treatmentPlans.user:id,name',
            'status:id,name,value,severity',
            'patient.user:id,name',
        ])->latest()->first();

        $form = $request->only(['status']);

        $patient->update($form);

        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $form = $request->only(['status', 'deposit']);

        $appointment->update($form);

        $appointment->refresh();

        $appointment->load('patient');

        $status = Status::find($form['status']);

        if ($status->id > $appointment->toArray()['patient']['status'] && $status->name !== 'canceled')
            $appointment->patient()->update(['status' => $status->id]);

        if ($status->name === 'periodic-visit') {
            $form['status'] = Status::firstWhere('name', 'pending')->id;
            $form['due_date'] = Carbon::now()->addMonth(3)->toString();
            $form['desc'] = $status->value;
            Patient::find($appointment->patient->id)->followUps()->create($form);
        }

        $appointment->load([
            'treatments:id,title',
            'patient:id,firstname,lastname,user',
            'patient.treatmentPlans.user:id,name',
            'status:id,name,value,severity',
            'patient.user:id,name',
        ]);

        return response()->json($appointment);
    }
}
