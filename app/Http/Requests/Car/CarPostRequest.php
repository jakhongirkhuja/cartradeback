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
            'title'=>'required',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'start_price'=>'required',
            'buy_price'=>'required',
            'mark_id'=>'required|numeric',
            'car_model_id'=>'required|numeric',
            'car_color_id'=>'required|numeric',
            'transmission_id'=>'required|numeric',
            'car_condtion_id'=>'required|numeric',
            'body_type_id'=>'required|numeric',
            'fuil_type_id'=>'required|numeric',
            'drive_types'=>'required',
            'year'=>'required',
            'mileage'=>'required',
            'engine_capacity'=>'required',
            'doors'=>'required',
            'cylinders'=>'required',
            'vin'=>'required',
            'salon'=>'required',
            'engine'=>'required',
            'carbody'=>'required',
            'time_end'=>'required',
            'time_start'=>'required',
            'body'=>'required',
            'functions'=>'required',
        ];
    }
}
