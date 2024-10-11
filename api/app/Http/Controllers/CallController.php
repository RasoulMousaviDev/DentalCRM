<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCallRequest;
use App\Http\Requests\StoreCallRequest;
use App\Models\Call;
use App\Models\Followup;
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
    public function index(IndexCallRequest $request)
    {
        $rows = $request->input('rows', 10);

        $patient = $request->get('patient');

        $calls = Call::latest()->with('status:id,value,severity');

        if ($patient)
            $calls->where('patient_id', $patient);
        else
            $calls->with(['patient' => fn($q) => $q->without([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'status'
            ])->select('id', 'firstname', 'lastname')]);
        $calls = $calls->when($request->input('query'), function ($query, $value) {
            $query->whereHas('patient', function (Builder $query) use ($value) {
                $query->whereAny(['firstname', 'lastname'], 'like', "%{$value}%");
            })->orWhereAny(['mobile', 'desc'], 'like', "%{$value}%");
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
            $created_at = Jalalian::fromFormat('Y/m/d', $created_at)->toCarbon()->format('Y-m-d');
            $query->whereDate('created_at', $created_at);
        })->when($request->input('status'), function ($query, $status) {
            $query->whereHas('status', function (Builder $query) use ($status) {
                $query->where('id', $status);
            });
        });


        $calls = $calls->paginate($rows);

        return response()->json($this->paginate($calls));
    }


    public function store(StoreCallRequest $request)
    {
        $form = $request->only(['status', 'mobile',  'desc']);

        $patient = Patient::find($request->get('patient'));

        $status = $request->get('patient_status');

        if ($status == $patient->status)
            $form['log'] = __('messages.call-stored');
        else {
            $patient->update(compact('status'));
            $form['log'] = __('messages.patient-status-changed', [
                'from' => $patient->status->title,
                'to' => Patient::find($status)->title
            ]);
        }

        $patient->calls()->create($form)->save();

        if ($request->has('followup_id'))
            Followup::find($request->get('followup_id'))->update(['status' => 'done']);


        $call = $patient->calls()->with('status:id,value,severity')->latest()->first();

        $response = compact('call');

        if ($request->has('followup')) {
            $form = $request->get('followup');
            $patient->followups()->create($form);
            $response['followup'] = $patient->followups()->latest()->first();
        }


        return response()->json($response);
    }
}
