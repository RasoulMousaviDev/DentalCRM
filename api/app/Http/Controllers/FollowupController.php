<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexFollowupRequest;
use App\Models\Followup;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class FollowupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexFollowupRequest $request)
    {
        $rows = $request->input('rows', 10);

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

        $followups = $followups->when($request->input('query'), function ($query, $value) {
            $query->whereHas('patient', function (Builder $query) use ($value) {
                $query->whereAny(['firstname', 'lastname'], 'like', "%{$value}%");
            })->orWhere('desc', 'like', "%{$value}%");
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('due_date'), function ($query, $due_date) {
            $due_date = Jalalian::fromFormat('Y/m/d', $due_date)->toCarbon()->format('Y-m-d');
            $query->whereDate('due_date', $due_date);
        })->when($request->input('status'), function ($query, $status) {
            $query->where('status', $status);
        });

        $followups = $followups->paginate($rows);

        return response()->json($this->paginate($followups));
    }
}
