<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstallmentPlanRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'months_count' => 'required|integer|unique:installment_plans,months_count',
            'deposit_percent' => 'required|integer|between:0,100',
            'interest_percent' => 'required|integer|between:0,100',
            'status' => 'required|boolean'
        ];
    }
}
