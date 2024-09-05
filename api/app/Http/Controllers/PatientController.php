<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::latest()->paginate(10);

        return response()->json($this->paginate($patients));
    }

    public function store(StorePatientRequest $request)
    {
        $form = $request->only([
            'name',
            'national_code',
            'birthday',
            'gender',
            'province',
            'city',
            'lead_source',
            'status',
            'desc'
        ]);

        $mobiles = $request->get('mobiles');

        $patient = Patient::create($form);

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $patient = $patient->latest()->first();

        return response()->json($patient);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $form = $request->only($patient->fillable);

        $patient->mobiles()->delete();

        $mobiles = $request->get('mobiles');

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $patient->update($form);

        $patient->refresh();

        return response()->json($patient);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->mobiles()->delete();

        $patient->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
