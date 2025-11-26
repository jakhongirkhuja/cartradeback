<?php

namespace App\Services\Cabinet;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\BookingImage;
use App\Models\Cars\Car;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
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
    private function checkExistance($start_date, $end_date, $car_id)
    {
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();

        $exists = Booking::where('car_id', $car_id)->whereNotIn('rent_status', ['completed', 'rejected'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
        return $exists;
    }
    public function createBookingService($data)
    {

        try {
            if ($this->checkExistance($data['start_date'], $data['end_date'], $data['car_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking ordered at the this time is busy.',
                ], 404);
            }
            $booking = new Booking();
            $booking->car_id = $data['car_id'];
            $booking->user_id = Auth::id();
            $booking->start_date = $data['start_date'];
            $booking->end_date = $data['end_date'];
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $days = abs($startDate->diffInDays($endDate)) ?: 1;

            $car = Car::find($data['car_id']);
            $booking->total_price = $days * $car->rent_price;
            $booking->save();

            $serviceId = '72351';
            $merchantId = '20368';
            $merchantUserId = '55180';
            $clickUrl = "https://my.click.uz/services/pay";
            $clickUrl .= "?service_id=$serviceId&merchant_id=$merchantId&merchant_user_id=$merchantUserId";
            $clickUrl .= "&amount=$booking->total_price&transaction_param=$booking->id&return_url=https://cartrade.uz/ru/success";
            return response()->json(['success' => true, 'data' => $booking, 'link' => $clickUrl]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to order car booking.',
                'error' => $th->getMessage(),
            ], 404);
        }
    }
    private function stepOne($data, $booking_id, $step, $accept)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        $booking = Booking::find($booking_id);
        if ($bookingHistory) {
            if (Auth::user()->role == 'admin' || Auth::user()->role == 'moderator') {
                if ($accept) {
                    $booking->rent_status = 'accepted';
                    $booking->save();
                    $bookingHistory->accept = true;
                    $bookingHistory->save();
                } else {
                    $booking->rent_status = 'rejected';
                    $booking->comment = $data['comment'];

                    $booking->save();
                    $bookingHistory->accept = false;
                    $bookingHistory->comment = $data['comment'];
                    $bookingHistory->save();
                }
                return response()->json($bookingHistory);
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'у вас нет привилегий администратора'
                    ],
                    403
                );
            }
        } else {
            $bookingHistory = new BookingHistory();
            $bookingHistory->booking_id = $booking_id;
            $bookingHistory->user_id = Auth::id();
            $bookingHistory->save = true;
            $bookingHistory->step = 1;
            if ($accept) {
                $booking->rent_status = 'accepted';
                $booking->save();
                $bookingHistory->accept = true;
            } else {
                $booking->rent_status = 'rejected';
                $booking->comment = $data['comment'];

                $booking->save();
                $bookingHistory->accept = false;
                $bookingHistory->comment = $data['comment'];
            }
            $bookingHistory->save();
            return response()->json($bookingHistory);
        }
    }
    private function stepTwo($booking_id, $step)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = true;
        $bookingHistory->save = true;
        $bookingHistory->step = 2;
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepThree($data, $booking_id, $step, $accept)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        if ($bookingHistory->save) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже не можете обновить',
            ], 403);
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = $accept == 1 ? true : false;
        $bookingHistory->save = true;
        $bookingHistory->step = 3;
        foreach ($data['images'] as $image) {
            $fileName = Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('files/body'), $fileName);
            $images[] = $fileName;
        }
        $bookingHistory->images = json_encode($images);
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepFour($data, $booking_id, $step, $accept)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        if ($bookingHistory->save) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже не можете обновить',
            ], 403);
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = $accept == 1 ? true : false;
        $bookingHistory->save = true;
        $bookingHistory->step = 4;
        foreach ($data['images'] as $image) {
            $fileName = Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('files/body'), $fileName);
            $images[] = $fileName;
        }
        $bookingHistory->images = json_encode($images);
        $bookingHistory->text = $data['text'];
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepFive($data, $booking_id, $step, $accept)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        if ($bookingHistory->save) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже не можете обновить',
            ], 403);
        }
        $booking = Booking::where('id', $booking_id)->where('user_id', Auth::id())->first();
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав',
            ], 403);
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = $accept == 1 ? true : false;
        $bookingHistory->save = true;
        $bookingHistory->step = 5;
        foreach ($data['images'] as $image) {
            $fileName = Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('files/documents'), $fileName);
            $images[] = $fileName;
        }
        $bookingHistory->images = json_encode($images);
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepSix($booking_id, $step)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        $booking = Booking::where('id', $booking_id)->where('user_id', Auth::id())->first();
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав',
            ], 403);
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = true;
        $bookingHistory->save = true;
        $bookingHistory->step = 6;
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepSeven($booking_id, $step)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = true;
        $bookingHistory->save = true;
        $bookingHistory->step = 7;
        $bookingHistory->save();
        return response()->json($bookingHistory);
    }
    private function stepEight($booking_id, $step)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        if (!$bookingHistory) {
            $bookingHistory = new BookingHistory();
        }
        $bookingHistory = new BookingHistory();
        $bookingHistory->booking_id = $booking_id;
        $bookingHistory->user_id = Auth::id();
        $bookingHistory->accept = true;
        $bookingHistory->save = true;
        $bookingHistory->step = 8;
        $bookingHistory->save();
        $booking = Booking::find($booking_id);
        $booking->rent_status = 'car_given';
        $booking->save();
        return response()->json($bookingHistory);
    }
    private function stepNine($data, $booking_id, $step, $accept)
    {
        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->where('step', $step)->first();
        $booking = Booking::find($booking_id);
        if ($bookingHistory) {
            if (Auth::user()->role == 'admin' || Auth::user()->role == 'moderator') {
                if ($accept) {
                    $booking->rent_status = 'completed';
                    $booking->save();
                    $bookingHistory->accept = true;
                    $bookingHistory->save();
                } else {
                    $booking->rent_status = 'completed-defect';
                    $booking->comment = $data['comment'];
                    $booking->save();
                    $bookingHistory->accept = false;
                    $bookingHistory->comment = $data['comment'];
                    foreach ($data['images'] as $image) {
                        $fileName = Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('files/completed'), $fileName);
                        $images[] = $fileName;
                    }
                    $bookingHistory->images = json_encode($images);
                    $bookingHistory->save();
                }
                return response()->json($bookingHistory);
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'у вас нет привилегий администратора'
                    ],
                    403
                );
            }
        } else {
            $bookingHistory = new BookingHistory();
            $bookingHistory->booking_id = $booking_id;
            $bookingHistory->user_id = Auth::id();
            $bookingHistory->save = true;
            $bookingHistory->step = 1;
            if ($accept) {
                $booking->rent_status = 'completed';
                $booking->save();
                $bookingHistory->accept = true;
            } else {
                $booking->rent_status = 'completed-defect';
                $booking->comment = $data['comment'];

                $booking->save();
                $bookingHistory->accept = false;
                $bookingHistory->comment = $data['comment'];
                if (isset($data['images'])) {

                    foreach ($data['images'] as $image) {
                        $fileName = Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('files/completed'), $fileName);
                        $images[] = $fileName;
                    }
                    $bookingHistory->images = json_encode($images);
                }
            }
            $bookingHistory->save();
            return response()->json($bookingHistory);
        }
    }
    public function bookingSteps($data)
    {
        $step = $data['step'];
        $booking_id = $data['booking_id'];
        $accept = $data['accept'];

        $bookingHistory = BookingHistory::where('booking_id', $booking_id)->orderby('step', 'desc')->first();
        if ($bookingHistory == $step - 1) {

            switch ($step) {
                case 1:
                    return $this->stepOne($data, $booking_id, $step, $accept);
                case 2:
                    return $this->stepTwo($booking_id, $step);
                case 3:
                    return $this->stepThree($data, $booking_id, $step, $accept);
                case 4:
                    return $this->stepFour($data, $booking_id, $step, $accept);
                case 5:
                    return $this->stepFive($data, $booking_id, $step, $accept);
                case 6:
                    return $this->stepSix($booking_id, $step);
                case 7:
                    return $this->stepSeven($booking_id, $step);
                case 8:
                    return $this->stepEight($booking_id, $step);
                case 9:
                    return $this->stepNine($data, $booking_id, $step, $accept);
                default:
                    # code...
                    break;
            }
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Что-то пошло не так'
                ],
                406
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Этапы еще не пройдены'
                ],
                406
            );
        }
    }
}
