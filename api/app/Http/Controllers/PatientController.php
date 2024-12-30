<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\Role;
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
            'status',
            'insurance'
        ];

        $dateFields = ['birthday', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'on-site-consultant'])->pluck('id');

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
            'insurance'
        ]);

        $patient = auth()->user()->patients()->create($form);

        $mobiles = $request->get('mobiles');

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

        $mobiles = $request->get('mobiles');

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
        $patient->appointments()->delete();
        $patient->treatmentPlans()->delete();
        $patient->photos()->delete();

        $patient->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
