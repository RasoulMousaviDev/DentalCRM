<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $type = $request->input('type'); // email or mobile

        $key = 'login:' . $request->ip;

        $executed = RateLimiter::attempt($key, $maxAttempts = 5, function () {}, $decaySeconds = 300, $perMinute = 2);

        if (!$executed) {
            return response()->json(['message' => __('errors.toManyAttempts')], 429);
        }

        $credentials = $request->only([$type, 'password']);

        if (!$token = auth()->claims(['type' => 'password'])->attempt($credentials))
            return response()->json(['message' => __('errors.login.incorrect.' . $type)], 400);

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth()->user();

        $user = [
            'name' => $user->name,
            'role' => $user->role,
            'roles' => $user->roles->pluck('id')
        ];

        return response()->json($user);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    public function logout()
    {
        auth()->logout(true);

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        $ttl = auth()->factory()->getTTL() * 60;
        $expires_at = Carbon::now()->addSeconds($ttl)->toString();

        return response()->json(compact('token', 'expires_at'));
    }
}
