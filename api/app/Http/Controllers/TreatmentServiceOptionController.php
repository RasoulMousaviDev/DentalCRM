<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentServiceOptionRequest;
use App\Http\Requests\UpdateTreatmentServiceOptionRequest;
use App\Models\TreatmentService;
use App\Models\TreatmentServiceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentServiceOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TreatmentService $service)
    {
        $items = $service->options()->get();

        return response()->json(['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentServiceOptionRequest $request, TreatmentService $service)
    {
        $form = $request->only(['title', 'cost', 'status']);

        $service->options()->create($form)->save();

        $option =  $service->options()->latest()->first();

        return response()->json($option);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreatmentServiceOptionRequest $request, TreatmentServiceOption $option)
    {
        $form = $request->only($option->fillable);

        $option->update($form);

        $option->refresh();

        return response()->json($option);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentServiceOption $option)
    {
        $option->delete();

        return response()->json(['message' => __('message.deleted-successfully')]);
    }
}
