<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCallRequest;
use App\Http\Requests\StoreCallRequest;
use App\Models\Call;
use App\Models\Followup;
use App\Models\Patient;
use Illuminate\Http\Request;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCallRequest $request)
    {
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
            ])->select('id', 'name')]);

        $calls = $calls->paginate(10);

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
