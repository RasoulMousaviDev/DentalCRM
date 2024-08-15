<?php

namespace App\Http\Controllers;

use App\Models\OTPCode;
use App\Models\User;
use Illuminate\Http\Request;

class OTPCodeController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function generate(Request $request) {}

    /**
     * Remove the specified resource from storage.
     */
    public function verify(Request $request)
    {
        // $user = User::first();
        // $token = auth()->claims(['foo' => 'bar'])->login($user);
    }
}
