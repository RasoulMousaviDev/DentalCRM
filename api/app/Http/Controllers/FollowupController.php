<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexFollowupRequest;
use App\Models\Followup;

class FollowupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexFollowupRequest $request)
    {
        $patient = $request->get('patient');

        $calls = Followup::where('patient_id', $patient)->latest()->paginate(10);

        return response()->json($this->paginate($calls));
    }
}
