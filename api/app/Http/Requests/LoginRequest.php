<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:email,mobile',
            'email' => 'required_if:type,email|email|exists:users,email',
            'mobile' => 'required_if:type,mobile|numeric|digits:11|starts_with:09|exists:users,mobile',
            'password' => 'required|min:8',
        ];
    }

    public function attributes()
    {
        return ['type' => ' '];
    }
}
