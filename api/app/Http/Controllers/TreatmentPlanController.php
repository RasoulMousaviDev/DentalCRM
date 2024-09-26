<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentPlanRequest;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use Illuminate\Http\Request;

class TreatmentPlanController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $treatmentPlans = TreatmentPlan::latest()->paginate(10);

        return response()->json($this->paginate($treatmentPlans));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentPlanRequest $request)
    {
        $form = $request->only(['payment_type', 'months', 'desc']);

        $patient = Patient::find($request->get('patient'));

        $patient->treatmentPlans()->create($form)->save();

        $treatmentPlan = $patient->treatmentPlans()->latest()->first();

        return response()->json($treatmentPlan);
    }
}
