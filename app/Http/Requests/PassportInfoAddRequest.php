<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassportInfoAddRequest extends FormRequest
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

            'passport_expired' => 'required|date|after:passport_given',
            'passport_given'   => 'required|date',
            'passport_inn'     => 'required|digits_between:10,14',
            'passport_number'  => 'required|string|max:50',
            'passport_photo'   => 'required|image|mimes:jpg,jpeg,png|max:3072',
        ];
    }
}
