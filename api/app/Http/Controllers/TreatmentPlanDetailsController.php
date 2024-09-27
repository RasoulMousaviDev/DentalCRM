<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentPlanDetailsRequest;
use App\Models\Treatment;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanDetails;
use Illuminate\Http\Request;

class TreatmentPlanDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TreatmentPlan $treatmentPlan)
    {
        $details = $treatmentPlan->details()->with('treatment')->latest()->paginate(10);

        return response()->json($this->paginate($details));
    }

    public function store(StoreTreatmentPlanDetailsRequest $request, TreatmentPlan $treatmentPlan)
    {
        $tooth = $request->get('tooth');

        $treatments = $request->get('treatments');

        foreach ($treatments as $treatment) {
            $cost = Treatment::find($treatment)->cost;
            $treatmentPlan->details()->create(compact('tooth', 'treatment', 'cost'))->save();
        }
        $items = $treatmentPlan->details()->with('treatment')->latest()->limit(count($treatments))->get();

        $totalCost = $treatmentPlan->totalCost;

        return response()->json(compact('items', 'totalCost'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentPlan $treatmentPlan, TreatmentPlanDetails $details)
    {
        $details->delete();

        $totalCost = $treatmentPlan->totalCost;

        return response()->json(['totalCost' => $totalCost, 'message' => __('messages.deleted-successfully')]);
    }
}
