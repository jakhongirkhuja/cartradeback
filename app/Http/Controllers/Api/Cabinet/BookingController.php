<?php

namespace App\Http\Controllers\Api\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingChangeStatus;
use App\Http\Requests\BookingStoreRequest;
use App\Http\Requests\SignatureRequest;
use App\Models\Booking;
use App\Models\Cars\Car;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}
    public function store(BookingStoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $booking = $this->bookingService->createBooking($validated);
            try {

                $serviceId = '72351';
                $merchantId = '20368';
                $merchantUserId = '55180';
                $clickUrl = "https://my.click.uz/services/pay";
                $clickUrl .= "?service_id=$serviceId&merchant_id=$merchantId&merchant_user_id=$merchantUserId";
                $clickUrl .= "&amount=$booking->total_price&transaction_param=$booking->id&return_url=https://cartrade.uz/ru/success?booking_id=$booking->id";
                return response()->json(['success' => true, 'data' => $booking, 'link' => $clickUrl]);
            } catch (\Throwable $th) {
                Log::error('' . $th);
                return response()->json(['success' => false, 'errors' => 'not given'], Response::HTTP_BAD_GATEWAY);
            }
            return response()->json($booking, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
    public function changeStatus(BookingChangeStatus $request)
    {
        try {
            $booking = $this->bookingService->changeStatus($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Booking status updated successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking status.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function uploadImageSignature(SignatureRequest $request)
    {
        try {
            $this->bookingService->uploadImageSignature($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Booking signature uploaded',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking signature.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function userBookings()
    {
        $user = Auth::user();
        if ($user->role === 'rent') {
            $booking = Booking::wherehas(['car' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->where('status', '!=', 'pending')->orderby('id', 'desc')->paginate();
        } else {
            $booking = Booking::with('car')->where('user_id', $user->id)->where('status', '!=', 'pending')->orderby('id', 'desc')->paginate();
        }
        return response()->json($booking);
    }
}
