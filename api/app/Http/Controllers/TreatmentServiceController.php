<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentServiceRequest;
use App\Http\Requests\UpdateTreatmentServiceRequest;
use App\Models\Treatment;
use App\Models\TreatmentService;

class TreatmentServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Treatment $treatment)
    {
        $items = $treatment->services()->with('options')->get();

        return response()->json(['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentServiceRequest $request, Treatment $treatment)
    {
        $form = $request->only(['title', 'status']);

        $treatment->services()->create($form)->save();

        $service =  $treatment->services()->latest()->first();

        return response()->json($service);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreatmentServiceRequest $request, TreatmentService $service)
    {
        $form = $request->only($service->fillable);

        $service->update($form);

        $service->refresh();

        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentService $service)
    {
        $service->options()->delete();

        $service->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
