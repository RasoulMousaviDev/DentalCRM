<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|between:100000,100000000',
            'payment_date' => 'required|date'
        ];
    }
}
