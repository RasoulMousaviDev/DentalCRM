<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstallmentPlanRequest;
use App\Http\Requests\UpdateInstallmentPlanRequest;
use App\Models\InstallmentPlan;

class InstallmentPlanController extends Controller
{
    public function index()
    {
        $installmentPlans = InstallmentPlan::orderBy('months_count')->get();

        return response()->json(['items' => $installmentPlans]);
    }

    public function store(StoreInstallmentPlanRequest $request)
    {
        $form = $request->only(['months_count', 'deposit_percent', 'interest_percent', 'status']);

        InstallmentPlan::create($form)->save();

        $installmentPlan = InstallmentPlan::latest()->first();

        return response()->json($installmentPlan);
    }


    public function update(UpdateInstallmentPlanRequest $request, InstallmentPlan $installmentPlan)
    {
        $form = $request->only($installmentPlan->fillable);

        $installmentPlan->update($form);

        $installmentPlan->refresh();

        return response()->json($installmentPlan);
    }

    public function destroy(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->delete();

        return response()->json(['message' => __('message.deleted-successfully')]);
    }
}
