<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = $request->input('rows', 10);

        $columns = ['firstname', 'lastname', 'telephone', 'birthday'];

        $patients = Patient::when($request->input('id'), function ($query, $id) {
            $query->where('id', $id);
        })->when($request->input('gender'), function ($query, $gender) {
            $query->where('gender', $gender);
        })->when($request->input('mobile'), function ($query, $mobile) {
            $query->whereHas('mobiles', function (Builder $query) use ($mobile) {
                $query->where('number', $mobile);
            });
        })->when($request->input('province'), function ($query, $province) {
            $query->whereHas('province', function (Builder $query) use ($province) {
                $query->where('id', $province);
            });
        })->when($request->input('city'), function ($query, $city) {
            $query->whereHas('city', function (Builder $query) use ($city) {
                $query->where('id', $city);
            });
        })->when($request->input('lead_source'), function ($query, $leadSource) {
            $query->whereHas('lead_source', function (Builder $query) use ($leadSource) {
                $query->where('id', $leadSource);
            });
        })->when($request->input('status'), function ($query, $status) {
            $query->whereHas('status', function (Builder $query) use ($status) {
                $query->where('id', $status);
            });
        });

        foreach ($columns as $column)
            $patients->when($request->input($column), function ($query, $value) use ($column) {
                $query->where($column, 'like', "%{$value}%");
            });

        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $patients->when($request->input($date), function ($query, $value) use ($date) {
                $date = collect($date)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));
                    
                $query->whereBetween($date, $value);
            });
        }

        $patients = $patients->with([
            'mobiles',
            'city:id,title',
            'province:id,title',
            'leadSource:id,title',
            'treatments:id,title',
            'status'
        ])->latest()->paginate($rows);

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
            'desc'
        ]);

        $patient = Patient::create($form);

        $mobiles = $request->get('mobiles');

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $treatments = $request->get('treatments');

        $patient->treatments()->attach($treatments);

        $patient = $patient->latest()->first();

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

        return response()->json($patient);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->mobiles()->delete();

        $patient->treatments()->detach();

        $patient->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
