<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateOTPCodeRequest;
use App\Http\Requests\VerifyOTPCodeRequest;
use App\Models\OTPCode;
use App\Models\User;
use Illuminate\Http\Request;

class OTPCodeController extends Controller
{

    public function generate(GenerateOTPCodeRequest $request)
    {

        $type = $request->input('type'); // email or phone

        $code = random_int(100000, 999999);

        //user

        OTPCode::firstOrCreate([
            $type => $request->get($type)
        ], [
            'code' => $code,
            'expires_at' => '',
            'type' => $type
        ]);
    }


    public function verify(VerifyOTPCodeRequest $request)
    {
        // $user = User::first();
        // $token = auth()->claims(['foo' => 'bar'])->login($user);
    }
}
