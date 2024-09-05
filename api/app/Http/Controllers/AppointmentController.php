<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $patient = $request->get('patient');

        $calls = Appointment::where('patient_id', $patient)
            ->latest()->with('treatment:id,title')->paginate(10);

        return response()->json($this->paginate($calls));
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
