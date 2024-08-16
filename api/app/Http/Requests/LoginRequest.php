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
            'type' => 'required|in:email,phone',
            'email' => 'required_if:type,email|email|exists:users,email',
            'phone' => 'required_if:type,phone|numeric|digits:11|starts_with:09|exists:users,phone',
            'password' => 'required|min:8',
        ];
    }
}
