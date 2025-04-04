<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Appointment;
use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
            'roles' => $user->roles->pluck('id'),
            'menu' => $user->role->menu,
        ];

        return response()->json($user);
    }

    public function changeRole(ChangeRoleRequest $request)
    {
        $role_id = $request->input('id');

        $user = auth()->user();

        if ($user->hasRole($role_id)) {
            $user->update(compact('role_id'));

            return $this->me();
        }

        return response()->json(['message' => __('messages.have-not-access')], 403);
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
        $ttl = auth('api')->factory()->getTTL() * 60;
        $expires_at = Carbon::now()->addSeconds($ttl)->toString();

        return response()->json(compact('token', 'expires_at'));
    }
}
