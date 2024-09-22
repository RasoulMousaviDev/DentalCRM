<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSurvayQuestionRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = request()->route('survay');

        return [
            'title' => ['required', 'string', Rule::unique('survay_questions')->where('survay_id', $id)],
            'order' => 'required|integer',
            'status' => 'required|boolean',
        ];
    }
}
