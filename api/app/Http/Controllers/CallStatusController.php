<?php

namespace App\Http\Controllers;

use App\Models\CallStatus;

class CallStatusController extends Controller
{
    public function index()
    {
        $statuses = CallStatus::all();

        return response()->json(['items' => $statuses]);
    }
}
