<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|numeric|digits:11|starts_with:09|unique:users,mobile',
            'roles' => 'required|array',
            'roles.*' => 'required|integer|exists:roles,id',
            'status' => 'required|boolean'
        ];
    }
}
