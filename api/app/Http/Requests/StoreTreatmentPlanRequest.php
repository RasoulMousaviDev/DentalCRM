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
            'patient.id' => 'required|exists:patients,id',
            'patient.visit_type' => 'required|in:in-person,online',
            'patient.desc' => 'nullable|string',
            'payment.method' => 'required|in:cash,installments',
            'payment.months_count' => 'required_if:payment.method,installments|integer|in:3,6,9,12',
            'payment.deposit_amount' => 'required_if:payment.method,installments|numeric|between:100000,100000000',
            'payment.start_date' => 'required_if:payment.method,installments|date',
            'payment.total_amount' => 'required|numeric',
            'payment.discount_amount' => 'nullable|numeric',
            'tooths' => 'required|array|min:1',
            'tooths.*' => 'required|array|min:1',
            'tooths.*.*' => 'required|array|min:1',
        ];
    }

}
