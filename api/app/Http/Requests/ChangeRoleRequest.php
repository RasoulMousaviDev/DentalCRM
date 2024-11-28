<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ChangeRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'id' => [
                'required',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) use ($userId) {
                    $roleExistsForUser = DB::table('user_roles')
                        ->where('user_id', $userId)
                        ->where('role_id', $value)
                        ->exists();

                    if (!$roleExistsForUser) 
                        $fail(__('validation.exists', compact('attribute')));
                    
                }
            ]
        ];
    }
}
