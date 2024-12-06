<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTreatmentPlanRequest;
use App\Http\Requests\StoreTreatmentPlanRequest;
use App\Http\Requests\UpdateTreatmentPlanRequest;
use App\Models\Patient;
use App\Models\Role;
use App\Models\TreatmentPlan;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TreatmentPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexTreatmentPlanRequest $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'visit_type', 'payment_method', 'status'];

        $dateFields = ['created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $treatmentPlans = TreatmentPlan::with([
            'patient:id,firstname,lastname',
            'status:id,value,severity'
        ]);


        if ($isAdmin) $treatmentPlans = $treatmentPlans->with('patient.user:id,name')->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        });
        else $treatmentPlans = $treatmentPlans->whereHas('patient', function (Builder $query) use ($user) {
            $query->where('user', $user->id);
        });

        foreach ($searchableFields as $field) {
            $treatmentPlans->when($request->input($field), function ($query, $value) use ($field) {
                $query->whereHas('patient', function (Builder $query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            });
        }

        foreach ($filterableFields as $field) {
            $treatmentPlans->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $treatmentPlans->when($request->input($field), function ($query, $value) use ($field) {
                $field = collect($field)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $treatmentPlans = $treatmentPlans->latest()->paginate($rows);

        $response = $this->paginate($treatmentPlans);

        $response['statuses'] = TreatmentPlan::model()->statuses;

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreatmentPlanRequest $request)
    {
        $form = $request->only([
            'payment_method',
            'months_count',
            'deposit_amount',
            'total_amount',
            'discount_amount',
            'start_date',
            'treatments_details',
            'desc',
        ]);

        $patient = Patient::find($request->get('patient'));

        $patient->treatmentPlans()->create($form)->save();

        $treatmentPlan = $patient->treatmentPlans()
            ->with('status:id,value,severity')
            ->with('patient:id,firstname,lastname,user')
            ->with('patient.user:id,name')
            ->latest()
            ->first();

        return response()->json($treatmentPlan);
    }

    public function show(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->load([
            'status:id,value,severity',
            'patient:id,firstname,lastname,user',
            'patient.user:id,name'
        ]);

        return response()->json($treatmentPlan);
    }

    public function update(UpdateTreatmentPlanRequest $request, TreatmentPlan $treatmentPlan)
    {
        $status = $request->get('status');

        $treatmentPlan->update(compact('status'));

        $treatmentPlan->refresh();

        $treatmentPlan->load([
            'status:id,value,severity',
            'patient:id,firstname,lastname,user',
            'patient.user:id,name'
        ]);

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
