<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferPatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'required|exists:users,id',
            'to' => 'required|exists:users,id|different:from',
            'status' => 'required|array',
            'status.*' => 'required|exists:statuses,id',
            'lead_source' => 'required|array',
            'lead_source' => 'required|exists:lead_sources,id',
            'count' => 'nullable|numeric|min:1',
            'created_at' => 'nullable|array',
            'created_at.*' => 'required|date'
        ];
    }
}
