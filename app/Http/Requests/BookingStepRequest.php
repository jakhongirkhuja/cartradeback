<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingStepRequest extends FormRequest
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
            'booking_id' => 'required|exists:bookings,id',
            // 'save' => 'required|numeric|min:0|max:1',
            'step' => 'required|numeric|min:0|max:10',
            'accept' => 'nullable|numeric|min:0|max:1',
            'comment' => [
                'string',
                'max:1000',
                Rule::when(
                    fn() => ((int)$this->step === 1 && (int)$this->accept === 0)
                        ||
                        ((int)$this->step === 9 && (int)$this->accept === 0),
                    ['required']
                ),
            ],
            'text' => [
                'string',
                'max:1000',
                Rule::requiredIf(fn() => (int)$this->step === 4),
            ],
            'images' => [
                'array',
                Rule::requiredIf(fn() => (int)$this->step === 3 ||  (int)$this->step === 4 ||  (int)$this->step === 5),
            ],

            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
                Rule::requiredIf(fn() => (int)$this->step === 3 ||  (int)$this->step === 4 ||  (int)$this->step === 5),
            ],


        ];
    }
}
