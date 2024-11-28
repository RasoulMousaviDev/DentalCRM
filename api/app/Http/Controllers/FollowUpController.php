<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexFollowUpRequest;
use App\Models\FollowUp;
use App\Models\Role;
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

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'status'];

        $dateFields = ['due_date', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $followUps = $isAdmin ?
            FollowUp::with('patient.user:name') :
            FollowUp::whereHas('patient', function (Builder $query) use ($user) {
                $query->where('user', $user->id);
            });

        if ($isAdmin) $followUps->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        });

        foreach ($searchableFields as $field)
            $followUps->when($request->input($field), function ($query, $value) use ($field) {
                $query->whereHas('patient', function (Builder $query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            });

        foreach ($filterableFields as $field) {
            $followUps->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $followUps->when($request->input($field), function ($query, $value) use ($field) {
                $field = collect($field)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $followUps = $followUps->with([
            'patient:id,firstname,lastname',
            'status:id,value,severity'
        ])->latest()->paginate($rows);

        $response = $this->paginate($followUps);

        $response['statuses'] = FollowUp::model()->statuses;

        return response()->json($response);
    }
}
