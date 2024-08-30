<?php

namespace App\Http\Controllers;

use App\Models\PatientStatus;
use Illuminate\Http\Request;

class PatientStatusController extends Controller
{
    public function index()
    {
        $statuses = PatientStatus::all();

        return response()->json(['items' => $statuses]);
    }
}
