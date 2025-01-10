<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexFollowUpRequest;
use App\Models\FollowUp;
use App\Models\Role;
use App\Models\Status;
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
        FollowUp::where('due_date', '<', Carbon::now())->whereHas('status', function (Builder $query) {
            $query->where('name', "pending");
        })->update(['status' => Status::firstWhere('name', 'lost')->id]);

        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'status'];

        $dateFields = ['due_date', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $followUps = FollowUp::with([
            'patient:id,firstname,lastname,mobiles',
            'status:id,value,severity'
        ]);

        if ($isAdmin) $followUps = $followUps->with('patient.user:id,name')->when($request->input('consultant'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        });
        else $followUps = $followUps->whereHas('patient', function (Builder $query) use ($user) {
            $query->where('user', $user->id);
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
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $followUps = $followUps->latest()->paginate($rows);

        $response = $this->paginate($followUps);

        $response['statuses'] = FollowUp::model()->statuses;

        return response()->json($response);
    }
}
