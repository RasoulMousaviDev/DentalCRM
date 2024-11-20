<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCallRequest;
use App\Http\Requests\StoreCallRequest;
use App\Models\Call;
use App\Models\FollowUp;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $calls = Call::latest()->with('status:id,value,severity')->with('patient:id,firstname,lastname');

        $calls = $calls->when($request->input('patient'), function ($query, $patient) {
            $query->where('patient_id', $patient);
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('mobile'), function ($query, $mobile) {
            $query->where('mobile', 'like', "%{$mobile}%");
        })->when($request->input('created_at'), function ($query, $created_at) {
            $date = collect($created_at)->map(fn($d, $i) => Carbon::parse($d)
                ->setTimezone('Asia/Tehran')
                ->{$i ? 'endOfDay' : 'startOfDay'}()
                ->format('Y-m-d H:i:s'));
            $query->whereBetween('created_at', $date);
        })->when($request->input('status'), function ($query, $status) {
            $query->whereHas('status', function (Builder $query) use ($status) {
                $query->where('id', $status);
            });
        });


        $calls = $calls->paginate($rows);

        $response = $this->paginate($calls);

        $response['statuses'] = Call::model()->statuses;

        return response()->json($response);
    }


    public function store(StoreCallRequest $request)
    {
        $request = collect($request->all())->dot();

        $form = $request->only(['status', 'mobile',  'desc']);

        $patient = Patient::find($request->get('patient.id'));

        $status = $request->get('patient.status');

        if ($status == $patient->status)
            $form['log'] = __('messages.call-stored');
        else {
            $patient->status = $patient->status()->get();
            $from = $patient->status->value;
            $patient->update(compact('status'));
            $patient->refresh();
            $to = $patient->status->value;
            $form['log'] = __('messages.patient-status-changed', compact('from', 'to'));
        }

        $patient->calls()->create($form->toArray())->save();

        if ($request->has('follow_up_id'))
            FollowUp::find($request->get('follow_up_id'))->update(['status' => 19]);

        $call = $patient->calls()
            ->with('status:id,value,severity')
            ->with('patient:id,firstname,lastname')
            ->latest()->first();

        $response = compact('call');

        $request = $request->undot();

        if ($request->has('follow_up')) {
            $form = $request->undot()->get('follow_up');
            $patient->followUps()->create($form);
            $response['follow_up'] = $patient->followUps()->latest()->first();
        }

        return response()->json($response);
    }
}
