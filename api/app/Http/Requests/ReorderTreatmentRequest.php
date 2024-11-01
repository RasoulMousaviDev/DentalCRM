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
            'rows' => 'required|array|min:2',
            'rows.*.id' => 'required|exists:treatments',
            'rows.*.order' => 'required|integer'
        ];
    }
}
