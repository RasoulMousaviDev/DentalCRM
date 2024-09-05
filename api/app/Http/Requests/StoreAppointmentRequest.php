<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient' => 'required|exists:patients,id',
            'treatment' =>'required|exists:treatments,id',
            'due_date' => 'required|date',
            'desc' => 'nullable|string'
        ];
    }
}
