<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexFollowUpRequest;
use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $followUps = FollowUp::latest()
            ->with('status:id,value,severity')
            ->with('patient:id,firstname,lastname')
            ->with('patient.user:name');

        $followUps = $followUps->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user);
            });
        })->when($request->input('patient'), function ($query, $patient) {
            $query->where('patient', $patient);
        })->when($request->input('firstname'), function ($query, $firstname) {
            $query->whereHas('patient', function (Builder $query) use ($firstname) {
                $query->where('firstname', 'like', "%{$firstname}%");
            });
        })->when($request->input('lastname'), function ($query, $lastname) {
            $query->whereHas('patient', function (Builder $query) use ($lastname) {
                $query->where('lastname', 'like', "%{$lastname}%");
            });
        })->when($request->input('status'), function ($query, $status) {
            $query->where('status', $status);
        });

        $dates = ['due_date', 'created_at', 'updated_at'];

        foreach ($dates as $date) {
            $followUps->when($request->input($date), function ($query, $value) use ($date) {
                $date = collect($date)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));
                $query->whereBetween($date, $value);
            });
        }

        $followUps = $followUps->paginate($rows);

        $response = $this->paginate($followUps);

        $response['statuses'] = FollowUp::model()->statuses;

        return response()->json($response);
    }
}
