<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentSubCategoryOptionRequest;
use App\Http\Requests\UpdateTreatmentSubCategoryOptionRequest;
use App\Models\TreatmentSubCategory;
use App\Models\TreatmentSubCategoryOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentSubCategoryOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TreatmentSubCategory $subCategory)
    {
        $items = $subCategory->options()->get();

        return response()->json(['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentSubCategoryOptionRequest $request, TreatmentSubCategory $subCategory)
    {
        $form = $request->only(['title', 'cost', 'status']);

        $subCategory->options()->create($form)->save();

        $option =  $subCategory->options()->latest()->first();

        return response()->json($option);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreatmentSubCategoryOptionRequest $request, TreatmentSubCategoryOption $option)
    {
        $form = $request->only($option->fillable);

        $option->update($form);

        $option->refresh();

        return response()->json($option);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentSubCategoryOption $option)
    {
        $option->delete();

        return response()->json(['message' => __('message.deleted-successfully')]);
    }
}
