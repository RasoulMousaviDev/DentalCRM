<?php

namespace App\Http\Requests;

use App\Models\Status;
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
        $status = Status::firstWhere('name', 'in-progress');

        return [
            'status' => 'required|exists:statuses,id',
            'mobile' => 'required|numeric|digits:11',
            'desc' => 'required|string',
            'patient.id' => 'required|exists:patients,id',
            'patient.status' => 'required|exists:statuses,id',
            'follow_up.due_date' => 'required_if:patient.status,' . $status->id . ',null|date',
            'follow_up.desc' => 'required_if:patient.status,' . $status->id . ',null|string',
            "follow_up_id" => "nullable|exists:follow_ups,id"
        ];
    }
}
