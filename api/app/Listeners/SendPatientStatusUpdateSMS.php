<?php

namespace App\Listeners;

use App\Events\PatientUpdated;
use App\Jobs\SendSMS;
use App\Models\SMSTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPatientStatusUpdateSMS
{
    /**
     * Handle the event.
     */
    public function handle(PatientUpdated $event): void
    {
        $patient = $event->patient;

        if ($patient->isDirty('status')) {
            $mobie = $patient->mobiles()->first()->number;

            $smsTemplate = SMSTemplate::firstWhere([
                'model' => 'patient',
                'status' => $patient->status,
                'active' => 1
            ]);

            if ($smsTemplate) {
                $patient->load([
                    'mobiles',
                    'city',
                    'province',
                    'leadSource',
                    'treatments',
                    'user',
                    'status'
                ]);


                $message = $smsTemplate->template;

                preg_match_all('/:(\w+(\.\w+)*)/', $message, $matches);

                if (str_contains($message, ':appointment'))
                    $patient->appointment = $patient->appointments()->orderBy('updated_at', 'DESC')->first();

                $patient = $patient->toArray();

                foreach ($matches[1] as $key)
                    $message = str_replace(":$key", data_get($patient, $key) ?? ":$key", $message);


                SendSMS::dispatch($mobie, $message);
            }
        }
    }
}
