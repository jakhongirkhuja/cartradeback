<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingImage;
use App\Models\Cars\Car;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $car = Car::findOrFail($data['car_id']);

            if ($car->status == false || $car->type !== 'rent') {
                throw new \Exception('Car not available for booking.');
            }
            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            $days = $end->diffInDays($start);
            $totalPrice = $car->rent_price * $days;

            $booking = Booking::create([
                'car_id' => $car->id,
                'order_number' => Str::uuid(),
                'user_id' => Auth::id(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_price' => $totalPrice,

            ]);

            return $booking;
        });
    }
    public function uploadImage($data, $booking, $status)
    {
        foreach ($data['images'] as $key => $images) {
            $imageName = (string) Str::uuid() . '-' . Str::random(15) . '.' . $images->getClientOriginalExtension();
            $images->move(public_path('/files/booking'), $imageName);
            $image = new BookingImage();
            $image->booking_id = $booking->id;
            $image->image = '/files/booking/' . $imageName;
            $image->status = $status;
            $image->save();
        }
    }
    public function uploadImageSignature($data)
    {
        $booking = Booking::find($data['booking_id']);
        if ($data['status'] == 'owner') {
            $booking->owner_signature = $data['signature'];
        } else {
            $booking->client_signature = $data['signature'];
        }


        if ($booking->owner_signature && $booking->client_signature) {
            $booking->status = 'in_use';
        }
        $booking->save();
        // foreach ($data['images'] as $key => $images) {
        //     $imageName = (string) Str::uuid() . '-' . Str::random(15) . '.' . $images->getClientOriginalExtension();
        //     $images->move(public_path('/files/signatrue'), $imageName);
        //     $image = new BookingImage();
        //     $image->booking_id = $booking->id;
        //     $image->image = '/files/booking/' . $imageName;
        //     $image->status = $status;
        //     $image->save();
        // }
    }
    public function changeStatus($data)
    {
        $booking = Booking::find($data['booking_id']);

        $status = $data['rent_status'];
        $booking->rent_status = $status;
        if ($status == 'car_given') {
            $this->uploadImage($data, $booking, $status);
        }
        if ($status == 'completed') {
            $this->uploadImage($data, $booking, $status);
        }
        if ($status == 'rejected') {
            $booking->comment = $data['comment'];
        }
        $booking->save();
    }
}
