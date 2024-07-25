<?php

namespace App\Http\Requests\UserAuth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            'email' => 'email|exists:users,email|max:50',
            'phoneNumber' => 'required|size:12',
            'name' => 'required|max:150',
            'verify_number'=>'required|size:6',
            'familyName' => 'required|max:150',
            'password' => [
                'required',
                'min:6',
                
            ]
        ];
    }
}
