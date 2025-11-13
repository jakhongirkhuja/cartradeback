<?php

namespace App\Services;

use App\Models\General;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsService
{

    public function getToken()
    {
        try {
            $res = DB::transaction(function () {
                $json  =  [
                    'email' => 'nurlanmalibekov1972@gmail.com',
                    'password' => 'npeJ4XEII7WVcTSpbd7Wrvn1ED3Mz3M8aMHIHP1n',

                ];
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    // 'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
                ])->post('https://notify.eskiz.uz/api/auth/login', $json);
                if ($response->ok()) {
                    $general = General::where('name', 'eskiz')->first();
                    if ($general) {
                        $general->value = $response['data']['token'];
                        $general->save();
                        return $general->value;
                    } else {
                        $general = new General();
                        $general->name = 'eskiz';
                        $general->value = $response['data']['token'];
                        $general->save();
                        return $general->value;
                    }
                }
                return false;
            });
            return $res;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function sendSms($phone, $code = null, $message = null, $counter = 0)
    {
        $general = General::where('name', 'eskiz')->first();
        if ($general) {
            $token =  $general->value;
        } else {
            $token = $this->getToken();
        }
        if (!$message) {
            $message = 'Ваш код для входа в cartrade.uz: ' . $code . '. @cartrade.uz #' . $code;
        }
        $json  =  [
            'mobile_phone' => $phone,
            'message' => $message,
            'from' => 4546,
            'callback_url' => 'https://api.1000oshpaz.uz/api/phoneNumberStatus'
        ];
        if ($token) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer ' . $token,
            ])->post('https://notify.eskiz.uz/api/message/sms/send', $json);
            Log::info('Sending request to API', $json);
            Log::info('body: ' . $response->body());
            Log::info('info: ' . $response->status());

            if ($response->status() == 401) {
                $counter++;
                if ($counter < 3) {
                    Log::error('' . $response);
                    $this->getToken();
                    $this->sendSms($phone, $code, null, $counter);
                    return false;
                }
            }
            if (!$response->ok()) {
                return false;
                $lang['ru'] = 'CМС не отправлено';
                $lang['uz'] = 'SMS yuborilmadi';
                $lang['en'] = 'SMS not sent';
                $validate['message'] = $lang;
                return response()->json(['success' => false, 'errors' => 'user  found'], Response::HTTP_NOT_FOUND);
            }
            Log::info('' . $response);
            if ($response->ok()) {
                return true;
            }


            Log::error('counter reached limit');
            return false;
        }
        Log::error('token not found');
        return false;
    }
}
