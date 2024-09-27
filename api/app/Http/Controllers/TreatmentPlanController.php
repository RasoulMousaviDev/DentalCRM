<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTreatmentPlanRequest;
use App\Http\Requests\StoreTreatmentPlanRequest;
use App\Http\Requests\UpdateTreatmentPlanRequest;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use Illuminate\Http\Request;

class TreatmentPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexTreatmentPlanRequest $request)
    {
        $patient = $request->get('patient');

        $treatmentPlans = TreatmentPlan::latest();

        if ($patient)
            $treatmentPlans->where('patient_id', $patient);
        else
            $treatmentPlans->with(['patient' => fn($q) => $q->without([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'status'
            ])->select('id', 'name')]);

        $treatmentPlans = $treatmentPlans->paginate(10);

        return response()->json($this->paginate($treatmentPlans));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentPlanRequest $request)
    {
        $form = $request->only(['payment_type', 'months', 'desc', 'deposit']);

        $patient = Patient::find($request->get('patient'));

        $patient->treatmentPlans()->create($form)->save();

        $treatmentPlan = $patient->treatmentPlans()->latest()->first();

        return response()->json($treatmentPlan);
    }

    public function update(UpdateTreatmentPlanRequest $request, TreatmentPlan $treatmentPlan)
    {
        $status = $request->get('status');

        if ($status == 'sent') {
            if ($treatmentPlan->status != 'editing')
                return response()->json(['message' => __('messages.you-cannot-deleted')], 400);
            if ($treatmentPlan->details()->count() < 1)
                return response()->json(['message' => __('messages.minimum-one-details-added')], 400);
            //send sms
            $treatmentPlan->update(['sent_at' => now()]);
        }

        $treatmentPlan->update(compact('status'));

        $treatmentPlan->refresh();

        return response()->json($treatmentPlan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentPlan $treatmentPlan)
    {
        if ($treatmentPlan->status == 'editing') {
            $treatmentPlan->details()->delete();

            $treatmentPlan->delete();

            return response()->json(['message' => __('messages.deleted-successfully')]);
        }

        return response()->json(['message' => __('messages.you-cannot-deleted')]);
    }
}
