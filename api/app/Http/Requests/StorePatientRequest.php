<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'birthday' => 'required|date',
            'gender'=> 'required|in:male,female',
            'mobiles' => 'required|array|min:1',
            'mobiles.*' => 'required|numeric|digits:11|starts_with:09|unique:patient_mobiles,number',
            'telephone' => 'required|numeric|digits:11|starts_with:0|unique:patients,telephone',
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'lead_source' => 'required|exists:lead_sources,id',
            'status' => 'required|exists:statuses,id',
            'desc' => 'nullable|string',
            'treatments' => 'nullable|array',
            'treatments.*' => 'required|exists:treatments,id'
        ];
    }

}
