<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SoapClient;

class SMS
{
    /**
     * Create a new class instance.
     */
    public static function send($phone, $text)
    {
        if (gettype($phone) == 'array')
            $phones = $phone;
        else $phones = [$phone];

        try {
            $recId = [0];
            $status = 0x0;
            $request = new SoapClient('http://payamak-service.ir/SendService.svc?wsdl', ['encoding' => 'UTF-8']);
            $request = $request->SendSMS($parameters = [
                'userName' => env('SMS_USERNAME', 'empty'),
                'password' => env('SMS_PASSWORD', 'empty'),
                'fromNumber' => env('SMS_NUMBER', 'empty'),
                'toNumbers' => $phones,
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
