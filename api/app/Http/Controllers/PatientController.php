<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\TransferPatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname', 'telephone'];

        $filterableFields = [
            'id',
            'gender',
            'province',
            'city',
            'lead_source',
            'insurance'
        ];

        $dateFields = ['birthday', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'on-site-consultant', 'reception'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $patients = $isAdmin ? Patient::with('user:id,name') : $user->patients();

        $patients = $patients->with([
            'mobiles',
            'treatments:id,title',
            'status:id,value,severity',
            'treatmentPlans.user:id,name',

        ]);

        if ($isAdmin) $patients
            ->when($request->input('phone_consultant'), function ($query, $user) {
                $query->whereHas('user', function (Builder $query) use ($user) {
                    $query->where('name', 'like', "%{$user}%");
                });
            })->when($request->input('on_site_consultant'), function ($query, $user) {
                $query->whereHas('treatmentPlans.user', function (Builder $query) use ($user) {
                    $query->where('name', 'like', "%{$user}%");
                });
            })->when($request->input('no_phone_consultant'), function ($query, $value) {
                if ($value) $query->whereHas('user.roles', function (Builder $query) {
                    $role = Role::firstWhere('name', "phone-consultant");
                    $query->whereNot('id', $role->id);
                });
            })->when($request->input('no_treatment_plan'), function ($query, $value) {
                if ($value) $query->doesntHave('treatmentPlans');
            });

        foreach ($searchableFields as $field) {
            $patients->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, 'like', "%{$value}%");
            });
        }

        foreach ($filterableFields as $field) {
            $patients->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $patients->when($request->input($field), function ($query, $value) use ($field) {
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $patients->when($request->input('mobile'), function ($query, $mobile) {
            $query->whereHas('mobiles', function (Builder $query) use ($mobile) {
                $query->where('number', 'like', "%{$mobile}%");
            });
        });

        $patients->when($request->input('status'), function ($query, $value) {
            $query->whereIn('status', $value);
        });


        $patients->when($request->input('no_call'), function ($query, $value) {
            if ($value) $query->doesntHave('calls');
        });

        $patients->when($request->input('call_status'), function ($query, $value) {
            if ($value) $query->whereDoesntHave('calls', function ($query) use ($value) {
                $query->where('status', $value);
            });
        });

        $patients->when($request->input('no_pending_follow_up'), function ($query, $value) {
            if ($value) $query->whereDoesntHave('followUps', function ($query) {
                $status = Status::firstWhere('name', 'pending');
                $query->where('status', $status->id);
            });
        });

        $patients->when($request->input('has_late_follow_up'), function ($query, $value) {
            if ($value) $query->whereHas('followUps', function ($query) {
                $status = Status::firstWhere('name', 'pending');
                $date = Carbon::setTimezone('Asia/Tehran')->addMonth(1)->format('Y-m-d H:i:s');
                
                $query->where('status', $status->id)->where('due_date', '>', $date);
            });
        });


        $patients = $patients->latest()->paginate($rows);

        $response = $this->paginate($patients);

        $response['statuses'] = Patient::model()->statuses;

        return response()->json($response);
    }

    public function store(StorePatientRequest $request)
    {
        $form = $request->only([
            'firstname',
            'lastname',
            'birthday',
            'telephone',
            'gender',
            'province',
            'city',
            'lead_source',
            'status',
            'desc',
            'insurance',
            'user'
        ]);

        $user =  auth()->user();

        if (isset($form['user']))
            $user = User::find($form['user']);

        $patient = $user->patients()->create($form);

        $mobiles = $request->get('mobiles', []);

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $treatments = $request->get('treatments');

        $patient->treatments()->attach($treatments);

        $patient = $patient->with([
            'mobiles',
            'user:id,name',
            'treatments:id,title',
            'treatmentPlans.user:id,name',
            'status:id,value,severity'
        ])->latest()->first();

        return response()->json($patient);
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'mobiles',
            'city:id,title',
            'province:id,title',
            'leadSource:id,title',
            'treatments:id,title',
            'user:name',
            'treatmentPlans.user:id,name',
            'status'
        ]);
        return response()->json($patient);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $form = $request->only($patient->fillable);

        $patient->mobiles()->delete();

        $mobiles = $request->get('mobiles', []);

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $patient->treatments()->detach();

        $treatments = $request->get('treatments');

        $patient->treatments()->attach($treatments);

        $patient->update($form);

        $patient->refresh();

        $patient->load([
            'mobiles',
            'user:id,name',
            'treatments:id,title',
            'status:id,value,severity',
            'treatmentPlans.user:id,name',
        ]);

        return response()->json($patient);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->treatments()->detach();
        $patient->mobiles()->delete();
        $patient->calls()->delete();
        $patient->followUps()->delete();
        foreach ($patient->appointments as $appointment) {
            $appointment->treatments()->detach();
            $appointment->delete();
        }
        $patient->treatmentPlans()->delete();
        $patient->photos()->delete();
        $patient->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }

    public function transfer(TransferPatientRequest $request)
    {
        $to = $request->get('to');

        $patients = Patient::when($request->input('from'), function ($query, $from) {
            $query->where('user', $from);
        })->when($request->input('status'), function ($query, $status) {
            $query->whereIn('status', $status);
        })->when($request->input('lead_source'), function ($query, $lead_source) {
            $query->whereIn('lead_source', $lead_source);
        })->when($request->input('created_at'), function ($query, $dates) {
            $value = collect($dates)->map(fn($d, $i) => Carbon::parse($d)
                ->setTimezone('Asia/Tehran')
                ->{$i ? 'endOfDay' : 'startOfDay'}()
                ->format('Y-m-d H:i:s'));

            $query->whereBetween('created_at', $value);
        })->when($request->input('count'), function ($query, $count) {
            $query->limit($count);
        })->when($request->input('no_pending_follow_up'), function ($query, $value) {
            if ($value) $query->whereDoesntHave('followUps', function ($query) {
                $status = Status::firstWhere('name', 'pending');
                $query->where('status', $status->id);
            });
        })->when($request->input('no_phone_consultant'), function ($query, $value) {
            if ($value) $query->whereHas('user.roles', function (Builder $query) {
                $role = Role::firstWhere('name', "phone-consultant");
                $query->whereNot('id', $role->id);
            });
        });

        $patients->update(['user' => $to]);

        return response()->json(['message' => __('messages.transferred-successfully')]);
    }
}
