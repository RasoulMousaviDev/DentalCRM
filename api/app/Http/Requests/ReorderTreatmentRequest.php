<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTreatmentRequest extends FormRequest
{
       /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'orders' => 'required|array|min:2',
            'orders.*.id' => 'required|exists:treatments',
            'orders.*.order' => 'required|integer'
        ];
    }
}
