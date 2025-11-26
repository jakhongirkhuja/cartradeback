<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;

class CarPostRequest extends FormRequest
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
            'title' => 'required',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            // 'start_price' => 'required',
            'mark_id' => 'required|numeric',
            'car_model_id' => 'required|numeric',
            'car_color_id' => 'required|numeric',
            'transmission_id' => 'required|numeric',
            'car_condtion_id' => 'required|numeric',
            'body_type_id' => 'required|numeric',
            'fuil_type_id' => 'required|numeric',
            'drive_types' => 'required',
            'year' => 'required',
            'mileage' => 'required',
            'engine_capacity' => 'required',
            'doors' => 'required',
            'cylinders' => 'required',
            'vin' => 'required',
            'engine_number' => 'required',
            'salon' => 'required',
            'engine' => 'required',
            'carbody' => 'required',
            'body' => 'required',
            'functions' => 'required',
            'type' => 'required',
            // 'rent_status' => 'required_if:type,rent|numeric|min:0|max:1',
            'rent_price' => 'required_if:type,rent|numeric',
            // 'rent_initial_price' => 'required_if:type,rent|numeric',
            'rent_deposit' => 'required_if:type,rent|numeric',
            'rent_price_overflow' => 'required_if:type,rent|numeric',
            'rent_limit_km' => 'required_if:type,rent|numeric',
            'technical_passport' => 'required_if:type,rent|image|mimes:jpeg,png,jpg|max:3096',
            'insurance' => 'required_if:type,rent|image|mimes:jpeg,png,jpg|max:3096',

        ];
    }
}
