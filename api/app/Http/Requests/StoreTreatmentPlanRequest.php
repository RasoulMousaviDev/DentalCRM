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
            'payment_method' => 'required|in:cash,installments',
            'visit_type' => 'required|in:in-person,online',
            'months_count' => 'required_if:payment_method,installments|integer|in:3,6,9,12',
            'deposit_amount' => 'required_if:payment_method,installments|numeric|between:100000,100000000',
            'start_date' => 'required_if:payment_method,installments|date',
            'total_amount' => 'required|numeric',
            'discount_amount' => 'nullable|numeric',
            'treatments_details' => 'required|array|min:1',
            'treatments_details.*.tooths' => 'required|array|min:1',
            'desc' => 'nullable|string',
        ];
    }
}
