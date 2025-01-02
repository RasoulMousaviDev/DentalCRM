<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SoapClient;

class SMS
{
    /**
     * Create a new class instance.
     */
    public static function send($mobile, $text)
    {
        if (gettype($mobile) == 'array')
            $mobiles = $mobile;
        else $mobiles = [$mobile];

        try {
            $recId = [0];
            $status = 0x0;
            $request = new SoapClient(env('SMS_API', ''), ['encoding' => 'UTF-8']);
            $request = $request->SendSMS($parameters = [
                'userName' => env('SMS_USERNAME', ''),
                'password' => env('SMS_PASSWORD', ''),
                'fromNumber' => env('SMS_NUMBER', ''),
                'toNumbers' => $mobiles,
                'messageContent' => $text,
                'isFlash' => false,
                'recId' => &$recId,
                'status' => &$status,
            ]);

            return $request->SendSMSResult || $request->recId->long;
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
    }
}
