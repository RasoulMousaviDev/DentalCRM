<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCallRequest;
use App\Http\Requests\StoreCallRequest;
use App\Models\Call;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'status'];

        $dateFields = ['due_date', 'created_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $calls = Call::with([
            'patient:id,firstname,lastname',
            'status:id,value,severity'
        ]);

        if ($isAdmin) $calls =  $calls->with('patient.user:id,name')->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        });
        else $calls = $calls->whereHas('patient', function (Builder $query) use ($user) {
            $query->where('user', $user->id);
        });

        foreach ($searchableFields as $field) {
            $calls->when($request->input($field), function ($query, $value) use ($field) {
                $query->whereHas('patient', function (Builder $query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            });
        }

        foreach ($filterableFields as $field) {
            $calls->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $calls->when($request->input($field), function ($query, $value) use ($field) {
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $calls->when($request->input('mobile'), function ($query, $mobile) {
            $query->where('mobile', 'like', "%{$mobile}%");
        });

        $calls = $calls->latest()->paginate($rows);

        $response = $this->paginate($calls);

        $response['statuses'] = Call::model()->statuses;

        return response()->json($response);
    }


    public function store(StoreCallRequest $request)
    {
        $request = collect($request->all())->dot();

        $form = $request->only(['status', 'mobile',  'desc']);

        $patient = Patient::find($request->get('patient.id'));

        $patient->load('status');

        $status = $request->get('patient.status');

        if ($status == $patient->status)
            $form['log'] = __('messages.call-stored');
        else {
            $from = $patient->toArray()['status']['value'];
            $patient->update(compact('status'));
            $patient->refresh();
            $to = $patient->toArray()['status']['value'];
            $form['log'] = __('messages.patient-status-changed', compact('from', 'to'));
        }

        $patient->calls()->create($form->toArray())->save();

        if ($request->has('follow_up_id')) {
            $status = Status::firstWhere('name', 'done');
            FollowUp::find($request->get('follow_up_id'))->update(['status' => $status->id]);
        }

        $call = $patient->calls()
            ->with(['status:id,value,severity', 'patient:id,firstname,lastname,user'])
            ->latest()->first();

        $response = compact('call');

        $request = $request->undot();

        if ($request->has('follow_up')) {
            $form = $request->undot()->get('follow_up');
            $form['status'] = Status::firstWhere('name', 'pending')->id;
            $patient->followUps()->create($form);
            $response['follow_up'] = $patient->followUps()->with(['status:id,value,severity', 'patient:id,firstname,lastname,user'])
                ->latest()->first();
        }

        return response()->json($response);
    }
}
