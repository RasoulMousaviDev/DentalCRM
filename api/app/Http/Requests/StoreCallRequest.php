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
            'status' => 'required|exists:call_statuses,id',
            'mobile' => 'required|numeric|digits:11|starts_with:09|exists:patient_mobiles,number',
            'desc' => 'required|string',
            'patient' => 'required|exists:patients,id',
            'patient_status' => 'required|exists:patient_statuses,id',
            'followup.due_date' => 'required_if:patient_status,1|date',
            'followup.desc' => 'required_if:patient_status,1|string',
            "followup_id" => "nullable|exists:followups,id"
        ];
    }
}
