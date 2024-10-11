<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $rows = $request->input('rows', 10);

        $patient = $request->get('patient');

        $appointments = Appointment::with('treatment:id,title');
        if ($patient)
            $appointments = $appointments->where('patient', $patient);
        else
            $appointments = $appointments->with(['patient' => fn($q) => $q->without([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'status'
            ])->select('id', 'firstname', 'lastname')]);


        $appointments = $appointments->when($request->input('query'), function ($query, $value) {
            $query->whereHas('patient', function (Builder $query) use ($value) {
                $query->whereAny(['firstname', 'lastname'], 'like', "%{$value}%");
            })->orWhereHas('treatment', function (Builder $query) use ($value) {
                $query->where('title', 'like', "%{$value}%");
            })->orWhereAny(['due_date', 'desc'], 'like', "%{$value}%");
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('due_date'), function ($query, $due_date) {
            $query->where('due_date', 'like', "%{$due_date}%");
        })->when($request->input('treatment'), function ($query, $treatment) {
            $query->whereHas('treatment', function (Builder $query) use ($treatment) {
                $query->where('id', $treatment);
            });
        })->when($request->input('status'), function ($query, $status) {
            $query->where('status', $status);
        });



        $appointments = $appointments->latest()->paginate($rows);

        return response()->json($this->paginate($appointments));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $form = $request->only(['treatment', 'due_date',  'desc']);

        $patient = Patient::find($request->get('patient'));

        $patient->appointments()->create($form)->save();

        $appointment = $patient->appointments()->with('treatment:id,title')->latest()->first();

        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $form = $request->only(['status']);

        $appointment->update($form);

        $appointment->refresh();

        return response()->json($appointment);
    }
}
