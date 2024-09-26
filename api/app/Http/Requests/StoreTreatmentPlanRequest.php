<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentPlanRequest extends FormRequest
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
            'payment_type' => 'required|in:cash,installments',
            'months' => 'required_if:payment_type,installments|integer|in:3,6,9,12',
            'desc' => 'required|string'
        ];
    }
}
