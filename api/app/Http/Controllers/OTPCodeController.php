<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateOTPCodeRequest;
use App\Http\Requests\VerifyOTPCodeRequest;
use App\Models\OTPCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class OTPCodeController extends Controller
{

    public function generate(GenerateOTPCodeRequest $request)
    {

        $type = $request->get('type'); // email or phone

        $key = 'otp-code-generate:' . $request->ip;

        $executed = RateLimiter::attempt($key, $maxAttempts = 5, function () {}, $decaySeconds = 300, $perMinute = 2);

        if (!$executed)
            return response()->json(['message' => __('errors.toManyAttempts')], 429);

        $user = User::firstWhere($type, $request->get($type));

        if ($user->OTPCode) {
            $expires_at = $user->OTPCode->expires_at;

            $is_available = Carbon::parse($expires_at)->timestamp - time() > 0;

            if ($is_available)
                return response()->json(['message' => 'ok', 'expires_at' => $expires_at]);

            $user->OTPCode->delete();
        }

        $code = random_int(100000, 999999);

        $expires_at = Carbon::now()->addSeconds(120);

        $user->OTPCode()->create(compact(['code', 'type', 'expires_at']));

        return response()->json(['message' => 'ok', 'expires_at' => $expires_at]);
    }


    public function verify(VerifyOTPCodeRequest $request)
    {
        $type = $request->get('type'); // email or phone

        $key = 'otp-code-verify:' . $request->ip;

        $executed = RateLimiter::attempt($key, $maxAttempts = 5, function () {}, $decaySeconds = 300, $perMinute = 2);

        if (!$executed)
            return response()->json(['message' => __('errors.toManyAttempts')], 429);

        $user = User::firstWhere($type, $request->get($type));

        if ($user->otpCode) {

            if ($user->OTPCode->code === $request->get('code')) {

                $expires_at = $user->OTPCode->expires_at;

                $is_available = Carbon::parse($expires_at)->timestamp - time() > 0;

                if ($is_available) {
                    $token = auth()->claims(['type' => 'otp'])->login($user);

                    $ttl = auth()->factory()->getTTL() * 60;
                    $expires_at = Carbon::now()->addSeconds($ttl)->toString();

                    $user->otpCode->delete();

                    return response()->json(compact('token', 'expires_at'));
                }

                return response()->json(['message' => __('errors.OTPCodeExpired')], 400);
            }

            return response()->json(['message' => __('errors.OTPCodeIncorrect')], 400);
        }

        return response()->json(['message' => __('errors.OTPCodeNotfound')], 400);
    }
}
