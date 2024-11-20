<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSMSTemplateRequest;
use App\Http\Requests\UpdateSMSTemplateRequest;
use App\Models\Call;
use App\Models\Patient;
use App\Models\SMSTemplate;
use Illuminate\Http\Request;

class SMSTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = SMSTemplate::latest()->paginate(10);

        $response = $this->paginate($templates);

        $response['statuses'] = [
            'patient' => Patient::model()->statuses,
            'call' => Call::model()->statuses,
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSMSTemplateRequest $request)
    {
        $form = $request->only(['template', 'model','status', 'active']);

        SMSTemplate::create($form)->save();

        $smsTemplate = SMSTemplate::latest()->first();

        return response()->json($smsTemplate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSMSTemplateRequest $request, SMSTemplate $smsTemplate)
    {
        $form = $request->only($smsTemplate->fillable);

        $smsTemplate->update($form);

        $smsTemplate->refresh();

        return response()->json($smsTemplate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SMSTemplate $smsTemplate)
    {
        $smsTemplate->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
