<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSMSTemplateRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'template' => 'required|string',
            'model' => ['required', 'in:patient,call', Rule::unique('sms_templates')
                ->where(function ($query) {
                    return $query->where('status', $this->input('status'));
                })],
            'status' => ['required', 'exists:statuses,id', Rule::unique('sms_templates')
                ->where(function ($query) {
                    return $query->where('model', $this->input('model'));
                })],
            'active' => 'required|boolean',
        ];
    }
}
