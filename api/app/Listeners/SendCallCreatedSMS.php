<?php

namespace App\Listeners;

use App\Events\CallCreated;
use App\Jobs\SendSMS;
use App\Models\Patient;
use App\Models\SMSTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCallCreatedSMS
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CallCreated $event): void
    {
        $call = $event->call;

        $mobile = $call->mobile;

        $smsTemplate = SMSTemplate::firstWhere([
            'model' => 'call',
            'status' => $call->status,
            'active' => 1
        ]);

        if ($smsTemplate) {
            $patient = Patient::find($call->patient)->first();
            
            $patient->load([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'treatments',
                'user',
                'status'
            ]);

            $patient = $patient->toArray();
            $message =  $smsTemplate->template;

            preg_match_all('/:(\w+(\.\w+)*)/', $message, $matches);

            foreach ($matches[1] as $key)
                $message = str_replace(":$key", data_get($patient, $key) ?? ":$key", $message);

            SendSMS::dispatch($mobile, $message);
        }
    }
}
