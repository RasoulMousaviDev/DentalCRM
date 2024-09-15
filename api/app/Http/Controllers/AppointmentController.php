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

        $appointments = Appointment::latest()->with('treatment:id,title');
        if ($patient)
            $appointments = $appointments->where('patient', $patient);
        else
            $appointments = $appointments->with(['patient' => fn($q) => $q->without([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'status'
            ])->select('id','name')]);

        return response()->json($this->paginate($appointments->paginate(10)));
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
