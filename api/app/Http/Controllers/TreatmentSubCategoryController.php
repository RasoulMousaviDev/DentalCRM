<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentSubCategoryRequest;
use App\Http\Requests\UpdateTreatmentSubCategoryRequest;
use App\Models\Treatment;
use App\Models\TreatmentSubCategory;
use Illuminate\Http\Request;
use Mockery\Matcher\Any;

class TreatmentSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Treatment $treatment)
    {
        $items = $treatment->subCategories()->with('options')->get();

        return response()->json(['items' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentSubCategoryRequest $request, Treatment $treatment)
    {
        $form = $request->only(['title', 'status']);

        $treatment->subCategories()->create($form)->save();

        $subCategory =  $treatment->subCategories()->latest()->first();

        return response()->json($subCategory);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreatmentSubCategoryRequest $request, TreatmentSubCategory $subCategory)
    {
        $form = $request->only($subCategory->fillable);

        $subCategory->update($form);

        $subCategory->refresh();

        return response()->json($subCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TreatmentSubCategory $subCategory)
    {
        $subCategory->options()->delete();

        $subCategory->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
