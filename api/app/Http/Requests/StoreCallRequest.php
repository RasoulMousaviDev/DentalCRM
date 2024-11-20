<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|exists:statuses,id',
            'mobile' => 'required|numeric|digits:11|starts_with:09|exists:patient_mobiles,number',
            'desc' => 'required|string',
            'patient.id' => 'required|exists:patients,id',
            'patient.status' => 'required|exists:statuses,id',
            'follow_up.due_date' => 'required_unless:patient.status,9,null|date',
            'follow_up.desc' => 'required_unless:patient.status,9,null|string',
            "follow_up_id" => "nullable|exists:follow_ups,id"
        ];
    }
}
