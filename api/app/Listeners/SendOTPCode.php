<?php

namespace App\Listeners;

use App\Events\OTPCodeCreated;
use Illuminate\Support\Facades\Log;

class SendOTPCode
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
    public function handle(OTPCodeCreated $event): void
    {

        // match($event->OTPCode->type){
        //     'phone' => dispatch(new SendSMS()),
        //     'email' => dispatch(new SendEmail())
        // };
    }
}
