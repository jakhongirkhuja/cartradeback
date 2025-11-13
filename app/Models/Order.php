<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function saveModel($data)
    {
        $currentDate = Carbon::now();
        $this->user_id = Auth::id();
        $this->order_number = $currentDate->format('F')[0] . $currentDate->format('l')[0] . '-' . $currentDate->valueOf();
        $this->delivery_time = Carbon::parse($data['delivery_time'])->toDateTimeString();
        $this->order_type = $data['order_type'];
        if ($data['order_type'] == 'delivery') {
            $this->address = $data['address'];
            $this->latitude = $data['latitude'];
            $this->longitude = $data['longitude'];
            $user = User::find(Auth::id());
            if ($user->address == null) {
                $user->address  = $data['address'];
                $user->latitude = $data['latitude'];
                $user->longitude = $data['longitude'];
                $user->save();
            }
        }
        $this->payment_method = $data['payment_method'];
        if ($this->payment_method) { // if $this->payment_method == true ->order was on deliever, if payment false;
            $this->order_status = 'on_queue';
        }
        $this->payment_type = $data['payment_type'];
        $this->comment = $data['comment'];
        $this->cooker_id = 0;
        // dd($data['cart_items']);
        $this->save();
        $inside = true;
        $totalPrice =  0;
        $totalQuantity = 0;
        // dd($data['cart_items']);


        $this->total_amount  = $totalPrice;
        $this->total_quantity = $totalQuantity;
        $this->save();
    }
}
