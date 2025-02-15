<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentServiceOptionRequest extends FormRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|unique:treatment_service_options,title,' . $this->option->id,
            'cost' => 'required|numeric|between:100000,100000000',
            'status' => 'required|boolean'
        ];
    }
}
