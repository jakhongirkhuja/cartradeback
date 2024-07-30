<?php

namespace App\Services\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Models\Auksion;
use App\Models\AuksionHistory;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class AuksionService {
    public function auksionPost($userData){
        try {
            $auksion = new Auksion();
            $auksion->user_id = auth()->user()->id;
            $auksion->time_end = Carbon::parse($userData['time_end'])->timestamp;
            $auksion->status = $userData['status'];
            $auksion->save();
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка';
            $lang['uz']= 'Xatolik';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($auksion, Response::HTTP_CREATED);
    }
    public function auksionEdit($userData, $id){
        $auksion = Auksion::find($id);
        if(!$auksion){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            $auksion->user_id = auth()->user()->id;
            $auksion->time_end = Carbon::parse($userData['time_end'])->timestamp;
            $auksion->status = $userData['status'];
            $auksion->save();
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка';
            $lang['uz']= 'Xatolik';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($auksion, Response::HTTP_CREATED);
    }
    public function auksionDelete($id){
        $auksion = Auksion::find($id);
        if(!$auksion){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $auksion->delete();
        $lang['ru']= 'Удален';
        $lang['uz']= 'O`chirildi';
        return response()->json($lang, Response::HTTP_OK);
    }
    public function auksionBet($userData){
        if(auth()->user()->role=='dealer'){
            $auksion = Auksion::find($userData['auksion_id']);
            $auksion->status = true;
            $auksion->save();
            if($auksion && $auksion->status){
                $found = AuksionHistory::where('auksion_id',$userData['auksion_id'])->where('bid_price',(int ) $userData['bid_price'] )->first();
                $maxPrice= AuksionHistory::where('auksion_id',$userData['auksion_id'])->orderby('bid_price','desc')->first();
                if($found || $maxPrice->bid_price>= (int ) $userData['bid_price']){
                    $lang['ru']= 'Отклонено. Введите другое значение.';
                    $lang['uz']= 'Rad etildi, boshqa qiymatni kiriting';
                    return ErrorHelperResponse::returnError($lang,Response::HTTP_FORBIDDEN);
                }
                
                try {
                    $auksionHistory = new AuksionHistory();
                    $auksionHistory->auksion_id = $userData['auksion_id'];
                    $auksionHistory->user_id = auth()->user()->id;
                    $auksionHistory->bid_price = (int ) $userData['bid_price'];
                    $auksionHistory->save();    
                    return response()->json(AuksionHistory::where('auksion_id',$userData['auksion_id'])->orderby('bid_price','desc')->first());
                } catch (\Throwable $th) {
                    $lang['ru']= 'Ошибка: '.$th->getMessage();
                    $lang['uz']= 'Xatolik: '.$th->getMessage();
                    return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $lang['ru']= 'Отказано в доступе';
        $lang['uz']= 'Ruxsat berilmadi';
        return ErrorHelperResponse::returnError($lang,Response::HTTP_UNAUTHORIZED);
    }
    public function auksionBuy($userData){
        if(auth()->user()->role=='dealer'){

            $auksion = Auksion::with('car')->find($userData['auksion_id']);
            if($auksion && $auksion->status){
                try {
                    $auksionHistory = new AuksionHistory();
                    $auksionHistory->auksion_id = $userData['auksion_id'];
                    $auksionHistory->user_id = auth()->user()->id;
                    $auksionHistory->bid_price = $auksion ->car->buy_price;
                    $auksionHistory->save();    
    
                    $auksion->current_price = $auksionHistory->bid_price;
                    $auksion->sold_price = $auksionHistory->bid_price;
                    $auksion->status = false;
                    $auksion->buy_user_id = auth()->user()->id;
                    $auksion->save();
    
                    $userTransaction = new UserTransaction();
                    $userTransaction->amount = $auksionHistory->bid_price;
                    $userTransaction->sign = false;
                    $userTransaction->service = 'auksion';
                    $userTransaction->service_id =$userData['auksion_id'];
                    $userTransaction->save();
    
                    $user = User::find(auth()->user()->id);
                    $balance = $user->balance;
                    $newBalance = $balance-$auksionHistory->bid_price;
                    if($newBalance<=0){
                        $user->balance_sign = false;
                        $user->balance = $newBalance * (-1);
                    }else{
                        $user->balance_sign = true;
                    }
                    $user->save();
                    
                    return response()->json(AuksionHistory::where('auksion_id',$userData['auksion_id'])->orderby('bid_price','desc')->first());
                } catch (\Throwable $th) {
                    $lang['ru']= 'Ошибка: '.$th->getMessage();
                    $lang['uz']= 'Xatolik: '.$th->getMessage();
                    return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $lang['ru']= 'Отказано в доступе';
        $lang['uz']= 'Ruxsat berilmadi';
        return ErrorHelperResponse::returnError($lang,Response::HTTP_UNAUTHORIZED);
    }
    
}