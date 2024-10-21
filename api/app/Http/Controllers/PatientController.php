<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
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

        $patients = Patient::when($request->input('query'), function ($query, $value) use ($columns) {
            $query->whereAny($columns, 'like', "%{$value}%")
                ->orWhereHas('mobiles', function (Builder $query) use ($value) {
                    $query->where('number', 'like', "%{$value}%");
                });
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

        $patients = $patients->latest()->paginate($rows);

        return response()->json($this->paginate($patients));
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

        $mobiles = $request->get('mobiles');

        $patient = Patient::create($form);

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

        $patient = $patient->latest()->first();

        return response()->json($patient);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $form = $request->only($patient->fillable);

        $patient->mobiles()->delete();

        $mobiles = $request->get('mobiles');

        foreach ($mobiles as $mobile)
            $patient->mobiles()->create(['number' => $mobile]);

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

        $patient->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}
