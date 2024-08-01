<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;

class UserInfoChangeAdminRequest extends FormRequest
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
            'user_id'=>'required',
            'name'=>'required|min:2',
            'familyName'=>'required|min:2',
            'email'=>'email',
            'avatar'=>'image|mimes:jpeg,png,jpg|max:2048',
            'role'=>'required',
            'phoneNumber'=>'required|size:12',
            'status'=>'required',
            'balance'=>'required',
        ];
    }
}
