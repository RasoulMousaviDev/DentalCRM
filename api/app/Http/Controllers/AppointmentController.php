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

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'status'];

        $dateFields = ['due_date', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'on-site-consultant', 'reception', 'appointment'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $appointments = Appointment::with([
            'treatments:id,title',
            'patient:id,firstname,lastname',
            'patient.treatmentPlans.user:id,name',
            'status:id,name,value,severity',
        ]);

        if ($isAdmin) $appointments = $appointments->with('patient.user:id,name')->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
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

                $query->whereBetween($field, $value);
            });
        }

        $appointments->when($request->input('treatment'), function ($query, $treatment) {
            $query->whereHas('treatment', function (Builder $query) use ($treatment) {
                $query->where('id', $treatment);
            });
        });

        $appointments = $appointments->latest()->paginate($rows);

        $response = $this->paginate($appointments);

        $response['statuses'] = Appointment::model()->statuses;

        return response()->json($response);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $form = $request->only(['due_date', 'desc']);

        $form['status'] = Status::firstWhere('name', 'appointment-set')->id;

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

        $status = Status::firstWhere('name', 'appointment-set')->id;

        $patient->update(compact('status'));

        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $form = $request->only(['status', 'deposit']);

        $status = Status::find($form['status']);

        if ($appointment->patient->status > $status->id && !$status->name !== 'canceled')
            $appointment->patient()->update(['status' => $status->id]);

        if ($status->name === 'periodic-visit') {
            $form['status'] = Status::firstWhere('name', 'pending')->id;
            $form['due_date'] = Carbon::now()->addMonth(3)->toString();
            $form['desc'] = $status->value;
            Patient::find($appointment->patient)->followUps()->create($form);
        }

        $appointment->update($form);

        $appointment->refresh();

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
