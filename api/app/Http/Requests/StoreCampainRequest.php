<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampainRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'desc' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'budget' => 'nullable|integer'
        ];
    }
}
