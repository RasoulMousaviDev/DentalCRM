<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendSMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;

class SendLoginCredentials
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
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        $user->makeVisible('password');
        
        $message = __('messages.login-credentials', $user->toArray());

        SendSMS::dispatch($user->phone, $message);

        $user->update(['password' => Hash::make($user->password)]);
    }
}
