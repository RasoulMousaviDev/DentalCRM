<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $rows = $request->input('rows', 10);

        $appointments = Appointment::with('treatments:id,title')
            ->with('patient:id,firstname,lastname')
            ->with('status:id,value,severity');

        $appointments = $appointments->when($request->input('patient'), function ($query, $patient) {
            $query->where('patient_id', $patient);
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

        $response = $this->paginate($appointments);

        $response['statuses'] = Appointment::model()->statuses;

        return response()->json($response);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $form = $request->only(['due_date', 'desc']);

        $patient = Patient::find($request->get('patient'));

        $appointment = $patient->appointments()->create($form);

        $treatments = $request->get('treatments');

        $appointment->treatments()->attach($treatments);

        $appointment = $patient->appointments()->with('treatments:id,title')->latest()->first();

        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $form = $request->only('status');

        $appointment->update($form);

        $appointment->refresh();

        if (in_array($form['status'], [4, 5, 6]))
            $appointment->patient()->update($form);

        if ($form['status'] == 15){
            $form['refund_date'] = Carbon::now()->toIso8601String();
            $appointment->deposits()->update($form);
        }

        $appointment->load('status');

        return response()->json($appointment);
    }
}
