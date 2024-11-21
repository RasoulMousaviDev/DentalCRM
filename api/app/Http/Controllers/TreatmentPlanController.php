<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTreatmentPlanRequest;
use App\Http\Requests\StoreTreatmentPlanRequest;
use App\Http\Requests\UpdateTreatmentPlanRequest;
use App\Models\Patient;
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

        $treatmentPlans = TreatmentPlan::latest()
            ->with('status:id,value,severity')
            ->with('patient:id,firstname,lastname')
            ->with('patient.user:name');

        $treatmentPlans = $treatmentPlans->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user);
            });
        })->when($request->input('patient'), function ($query, $patient) {
            $query->where('patient', $patient);
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('visit_type'), function ($query, $type) {
            $query->where('visit_type', $type);
        })->when($request->input('payment_method'), function ($query, $method) {
            $query->where('payment_method', $method);
        })->when($request->input('status'), function ($query, $status) {
            $query->where('status', $status);
        });

        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $treatmentPlans->when($request->input($date), function ($query, $value) use ($date) {
                $date = collect($date)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($date, $value);
            });
        }

        $treatmentPlans = $treatmentPlans->paginate($rows);

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
            'checks_count',
            'deposit_amount',
            'total_amount',
            'discount_amount',
            'start_date',
            'treatments_details',
            'desc',
        ]);

        $patient = Patient::find($request->get('patient'));

        $patient->treatmentPlans()->create($form)->save();

        $treatmentPlan = $patient->treatmentPlans()->latest()->first();

        return response()->json($treatmentPlan);
    }

    public function show(TreatmentPlan $treatmentPlan)
    {
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

        $treatmentPlan->load(['patient' => fn($q) => $q->without([
            'mobiles',
            'city',
            'province',
            'leadSource',
            'status'
        ])->select('id', 'firstname', 'lastname')]);

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
