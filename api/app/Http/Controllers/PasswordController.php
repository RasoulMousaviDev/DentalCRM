<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request) {

        $payload = auth()->payload();
        $type = $payload->get('type');

        if($type === 'otp'){

            $password = $request->get('password');

            $user = auth()->user();

            $user->update(['password' => Hash::make($password)]);

            auth()->logout(true);

            return response()->json(['message' => 'ok']);
        }

        return response()->json(['message' => 'no access to api by this token'], 403);
    }

    public function change(ChangePasswordRequest $request) {

        $user = auth()->user();

        if (Hash::check($request->current_password, $user->password)) {

            $user->update(['password' => Hash::make($request->new_password)]);

            return response()->json(['message' => 'ok']);

        }
        
        $key = 'password-change:' . $user->id;

        $executed = RateLimiter::attempt($key, $maxAttempts = 5, function () {}, $decaySeconds = 300, $perMinute = 2);

        if (!$executed)
            return response()->json(['message' => __('errors.toManyAttempts')], 429);

        return response()->json(['message' => 'your current password is incorrect!'], 403);
    }
}
