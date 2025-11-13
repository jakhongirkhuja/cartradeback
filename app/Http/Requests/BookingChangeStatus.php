<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingChangeStatus extends FormRequest
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
            'rent_status' => 'required|in:accepted,car_given,in_use,completed,rejected',
            'comment' => 'required_if:rent_status,rejected',
            'images.' => 'required_if:rent_status,car_given,completed|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:4096',
        ];
    }
}
