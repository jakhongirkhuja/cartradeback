<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClickUz;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\SendSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickController extends Controller
{
    public function prepare(Request $request)
    {

        Log::info('Prepare', [$request->all()]);
        $clickTransId = $request->input('click_trans_id');
        $serviceId = $request->input('service_id');
        $clickPaydocId = $request->input('click_paydoc_id');
        $merchantTransId = $request->input('merchant_trans_id');
        $amount = $request->input('amount');
        $action = $request->input('action');
        $error = $request->input('error');
        $errorNote = $request->input('error_note');
        $signTime = $request->input('sign_time');
        $signString = $request->input('sign_string');
        $secretKey = 'jY3bE6mUfW7Grt';
        $generatedSignString = md5($clickTransId . $serviceId . $secretKey . $merchantTransId . $amount . $action . $signTime);
        // dd($generatedSignString);
        Log::info('Request SignString:', ['sign_string' => $signString]);
        Log::info('Request generatedSignString:', ['generatedSignString' => $generatedSignString]);

        if ($signString !== $generatedSignString) {
            return response()->json(['error' => -1, 'error_note' => 'Invalid sign_string']);
        }
        $booking = Booking::find($merchantTransId);

        if (!$booking) {

            return response()->json(['error' => -5, 'error_note' => 'Order does not exist']);
        }
        if ($booking->total_price != $amount) {
            return response()->json(['error' => -2, 'error_note' => 'Incorrect parameter amount']);
        }

        $clickTransCheck = ClickUz::where('click_trans_id', $clickTransId)->first();
        if (!$clickTransCheck) {
            ClickUz::create([
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'amount' => $amount,
                'amount_rub' => $amount,
                'sign_time' => $signTime,
                'situation' => $error
            ]);
        } else {
            if ($clickTransCheck->status == 'success') {
                return response()->json([
                    'click_trans_id' => $clickTransId,
                    'merchant_trans_id' => $merchantTransId,
                    'merchant_confirm_id' => $merchantTransId,
                    'error' => -9,
                    'error_note' => 'Do not find a user!!!'
                ]);
            } else {
                ClickUz::update(['situation' => 1, 'status' => 'pending']);
            }
        }


        if ($error == 0) {
            $response = [
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'merchant_prepare_id' => $merchantTransId,
                'error' => 0,
                'error_note' => 'Payment prepared successfully',
            ];
        } else {
            $response = [
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'merchant_prepare_id' => $merchantTransId,
                'error' => -9,
                'error_note' => 'Do not find a Order!!!',
            ];
        }

        Log::info('Click Prepare Response:', $response);

        return response()->json($response);
    }
    public function complete(Request $request)
    {
        $clickTransId = $request->input('click_trans_id');
        $serviceId = $request->input('service_id');
        $clickPaydocId = $request->input('click_paydoc_id');
        $merchantTransId = $request->input('merchant_trans_id');
        $merchantPrepareId = $request->input('merchant_prepare_id');
        $amount = $request->input('amount');
        $action = $request->input('action');
        $error = $request->input('error');
        $errorNote = $request->input('error_note');
        $signTime = $request->input('sign_time');
        $signString = $request->input('sign_string');
        $secretKey = 'jY3bE6mUfW7Grt';
        // $secretKey = env('MERCHANT_KEY'); 

        $generatedSignString = md5($clickTransId . $serviceId . $secretKey . $merchantTransId . $merchantPrepareId . $amount . $action . $signTime);
        // dd($generatedSignString);
        if ($signString !== $generatedSignString) {
            return response()->json(['error' => -1, 'error_note' => 'Invalid sign_string']);
        }
        $booking = Booking::find($merchantTransId);

        if (!$booking) {
            return response()->json(['error' => -5, 'error_note' => 'Order does not exist']);
        }
        if ($booking->total_price != $amount) {
            return response()->json(['error' => -2, 'error_note' => 'Incorrect parameter amount']);
        }
        if ($error == 0) {

            ClickUz::where('click_trans_id', $clickTransId)->update(['situation' => 1, 'status' => 'success']);
            try {
                if ($booking) {
                    $booking->status = 'booked';
                    $booking->save();
                    $userTransaction = new UserTransaction();
                    $userTransaction->user_id = $booking->user_id;
                    $userTransaction->amount = $booking->total_price;
                    $userTransaction->sign = true;
                    $userTransaction->service = 'payment';
                    $userTransaction->service_id = $booking->id;
                    $userTransaction->action_user_id =  $booking->user_id;
                    $userTransaction->save();
                    $userOrder = User::find($booking->user_id);
                    $data = [
                        "ReceiptSeq" => rand(12, 456),
                        "IsRefund" => 0,
                        "Items" => [
                            [
                                "Name" => "Легковые автомобили аренда",
                                "SPIC" => "08703001001000000",
                                "PackageCode" => "309810",
                                "GoodPrice" => $booking->total_price,
                                "Price" => $booking->total_price,
                                "VAT" => $booking->total_price * 0.12,
                                "VATPercent" => 12,
                                "Amount" => 1000,
                                "CommissionInfo" => [
                                    "Pinfl" => "30111976530011"
                                ]
                            ]
                        ],
                        "ReceivedCash" => 0,
                        "ReceivedCard" => $booking->total_price * 100,
                        "TotalVAT" => 0,
                        "Time" => Carbon::now()->format('Y-m-d H:i:s'),
                        "ReceiptType" => 0,
                        "ExtraInfo" => [
                            "PhoneNumber" => $userOrder?->phoneNumber ?? ''
                        ]
                    ];
                    $response = Http::post('http://5.182.26.53/api/fiscal', $data);
                    if ($response->ok()) {
                        $dataResponse = $response->json();
                        $qrCodeUrl = $dataResponse['QRCodeURL'] ?? null;

                        if ($booking) {
                            $booking->fiscalUrl = $qrCodeUrl;
                            $booking->save();
                        }
                        Log::info('Operation is success: ' . json_encode($dataResponse));
                    } else {
                        Log::info('Operation is not success: ' . json_encode($response->body()));
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
            }

            try {
                // $notification = new Notification();
                // $title_ru = 'У вас есть заказ';
                // $title_uz  = "Sizga yangi buyurtma keldi";
                // $body_ru = '';
                // $body_uz = '';
                // $notification->saveModel($order->id, $order->cooker_id, 'seller', 'orders', $title_ru, $title_uz, $body_ru, $body_uz);
                $sendsmsservice = new SendSmsService();
                $user = User::find($booking->user_id);
                if ($user) {
                    $phone = $user->phone;
                    $orderNumber = preg_replace('/\D/', '', $booking->order_number);
                    $message = 'Вам поступил новый заказ №' . $orderNumber . '. Подробнее: https://cartrade.uz';
                    $sendsmsservice->sendSms($phone, null, $message);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
            return response()->json([
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'merchant_confirm_id' => $merchantTransId,
                'error' => 0,
                'error_note' => 'Payment Success'
            ]);
        } else {
            ClickUz::where('click_trans_id', $clickTransId)->update(['situation' => -9, 'status' => 'error']);
            // Order::where('id', $merchantTransId)->update(['status' => 'canceled']);
            return response()->json([
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'merchant_confirm_id' => $merchantTransId,
                'error' => -9,
                'error_note' => 'Do not find a user!!!'
            ]);
        }
    }
}
