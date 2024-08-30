<?php

namespace App\Listeners;

use App\Events\OTPCodeCreated;
use App\Jobs\SendEmail;
use App\Jobs\SendSMS;
use App\Mail\OTPCodeCreated as MailOTPCodeCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $otpCode = $event->OTPCode;

        if ($otpCode->type === 'mobile'){
            $message = __('messages.opt-code-created', ['code' => $otpCode->code]);
            SendSMS::dispatch($otpCode->user->mobile, $message);
        }
        else if ($otpCode->type === 'email') {
            $message = (new MailOTPCodeCreated($otpCode))->onQueue('emails');

            Mail::to($otpCode->user->email)->queue($message);
        }
    }
}
