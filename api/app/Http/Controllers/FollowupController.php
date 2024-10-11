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

        $followups = Followup::latest();

        if ($patient)
            $followups->where('patient_id', $patient);
        else
            $followups->with(['patient' => fn($q) => $q->without([
                'mobiles',
                'city',
                'province',
                'leadSource',
                'status'
            ])->select('id', 'firstname', 'lastname')]);

        $followups = $followups->paginate(10);

        return response()->json($this->paginate($followups));
    }
}
