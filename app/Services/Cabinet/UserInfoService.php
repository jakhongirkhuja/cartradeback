<?php

namespace App\Services\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Models\Auksion;
use App\Models\Cars\Car;
use App\Models\Cars\CarImage;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\Tarif;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserInfoService {
    public function userFillBalancee($userData){
        try {
            $userTransaction = new UserTransaction();
            $userTransaction->amount = (int) $userData['amount'];
            $userTransaction->user_id =auth()->user()->id;
            $userTransaction->sign = true;
            $userTransaction->service = 'user_balance';
            $userTransaction->service_id = auth()->user()->id;
            $userTransaction->action_user_id = auth()->user()->id;
            $userTransaction->save();
            $user = User::find(auth()->user()->id);
            $user->balance = $user->balance + $userTransaction->amount;
            $user->save();
            return response()->json($user);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка '.$th->getMessage();
            $lang['uz']= 'Xatolik '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function userTarifChoose($userData){
        $tarif = Tarif::find($userData['tarif_id']);
        if(!$tarif){
            $lang['ru']= 'Тариф не найден';
            $lang['uz']= 'Tarif topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $value =(int) $userData['value'];
        $price = (int) $tarif->price;
        $newPrice = $price * $value;
        $user = User::find(auth()->user()->id);
        if($user->balance<$newPrice ){
            $lang['ru']= 'Не дастатчно средств';
            $lang['uz']= 'Yetarli mablag mavjud emas';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        try {
           
            $userTransaction = new UserTransaction();
            $userTransaction->amount =$newPrice ;
            $userTransaction->user_id =auth()->user()->id;
            $userTransaction->sign = false;
            $userTransaction->service = 'tarif';
            $userTransaction->service_id = $tarif->id;
            $userTransaction->action_user_id = auth()->user()->id;
            $userTransaction->save();
            $newDate = Carbon::now()->addMonths($value);
            $user->balance = $user->balance - $newPrice;
            $user->tarif_id = $tarif->id;
            $user->tarif_payed_till = $newDate;
            $user->tarif_connected_at = Carbon::now();
            $user->save();
            return response()->json($user,Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка '.$th->getMessage();
            $lang['uz']= 'Xatolik '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function userPasswordChange($userData){
        try {
            $user = User::find(auth()->user()->id);
          
            if(Hash::check($userData['old_password'],$user->password) ){
               
                if($userData['new_password']==$userData['repeat_password']){
                    $user->password = Hash::make($userData['new_password']);
                    $user->save();
                    return response()->json($user, Response::HTTP_OK);
                }else{
                    $lang['ru']= 'Повторный пароль неправильный';
                    $lang['uz']= 'Takroriy mahfiy so`z xato kirtilgan';
                    return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
                }
            }
            $lang['ru']= 'Старый пароль неправильный';
            $lang['uz']= 'Eski mahfiy so`z xato kirtilgan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка '.$th->getMessage();
            $lang['uz']= 'Xatolik '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
    }
    public function userInfoChange($userData){
        
        try {
            
            $user = User::find(auth()->user()->id);
            $user->name = $userData['name'];
            $user->familyName = $userData['familyName'];
            $user->email = isset($userData['email'])? $userData['email'] : null;
            
            if(isset($userData['avatar'])){
                $imageName = (string) Str::uuid().'-'.Str::random(15).'.'.$userData['avatar']->getClientOriginalExtension();
                $userData['avatar']->move(public_path('/files/user'),$imageName);
                if($user->avatar && file_exists(public_path('/files/user/'.$user->avatar))){
                    unlink(public_path('/files/user/'.$user->avatar));
                }
                $user->avatar = $imageName;
            }
            $user->save();
            return response()->json($user, Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function userRemove($id){
        $user = User::find($id);
        if($user){
            try {
                $phoneNumber = PhoneNumber::where('phoneNumber', $user->phoneNumber)->first();
                if($phoneNumber) $phoneNumber->delete();
                $user->delete();
                $lang['ru']= 'Пользователь удален';
                $lang['uz']= 'Foydalanuvchi o`chirildi';
                return response()->json($lang, Response::HTTP_OK);
            } catch (\Throwable $th) {
                $lang['ru']= 'Ошибка '.$th->getMessage();
                $lang['uz']= 'Xatolik '.$th->getMessage();
                return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        $lang['ru']= 'Пользователь не найден';
        $lang['uz']= 'Foydalanuvchi topilmadi';
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
    }
    public function userInfoChangeAdmin($userData){
        
        try {
            
            $user = User::find($userData['user_id']);
            if($user){
                $phoneNumber = User::where('phoneNumber',$userData['phoneNumber'])->where('id','!=',$userData['user_id'] )->first();
                if($phoneNumber){
                    $lang['ru']= 'Пользователь с таким номером существует';
                    $lang['uz']= 'Bunday raqam bilan foydalanuchsi mavjud';
                    return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
                }
                $user->name = $userData['name'];
                $user->familyName = $userData['familyName'];
                $user->email = isset($userData['email'])? $userData['email'] : null;
                $user->role = $userData['role'];
                $userBalance = $user->balance;
                $updatedBalance = (int)$userData['balance'];
                if($updatedBalance>$userBalance ){
                    $resultBalance = $updatedBalance- $userBalance;
                    $userTransaction = new UserTransaction();
                    $userTransaction->amount = $resultBalance;
                    $userTransaction->user_id = $user->id;
                    $userTransaction->sign = true;
                    $userTransaction->service = 'user_balance';
                    $userTransaction->service_id = $user->id;
                    $userTransaction->action_user_id = auth()->user()->id;
                    $userTransaction->save();
                }else if ($updatedBalance<$userBalance){
                    $resultBalance = $userBalance - $updatedBalance;
                    $userTransaction = new UserTransaction();
                    $userTransaction->amount = $resultBalance;
                    $userTransaction->user_id = $user->id;
                    $userTransaction->sign = false;
                    $userTransaction->service = 'user_balance';
                    $userTransaction->service_id = $user->id;
                    $userTransaction->action_user_id = auth()->user()->id;
                    $userTransaction->save();
                }
                $user->balance = (int)$userData['balance'];
                $user->phoneNumber = (int) $userData['phoneNumber'];
                $user->status = $userData['status'];
                if(isset($userData['avatar'])){
                    $imageName = (string) Str::uuid().'-'.Str::random(15).'.'.$userData['avatar']->getClientOriginalExtension();
                    $userData['avatar']->move(public_path('/files/user'),$imageName);
                    if($user->avatar && file_exists(public_path('/files/user/'.$user->avatar))){
                        unlink(public_path('/files/user/'.$user->avatar));
                    }
                    $user->avatar = $imageName;
                }
                $user->save();
                return response()->json($user, Response::HTTP_OK);
            }
            $lang['ru']= 'Пользователь не найден';
            $lang['uz']= 'Foydalanuchsi topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
            
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function phoneNumberChange($userData){
        try {
            $user = User::find($userData['user_id']);
            if($user){
                $user->phoneNumber = $userData['phoneNumber'];
                $user->save();
                return response()->json($user, Response::HTTP_OK);
            }
            $lang['ru']= 'Пользователь не найден';
            $lang['uz']= 'Foydalanuchsi topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
            
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();;
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function roleChange($userData){
        try {
            $user = User::find($userData['user_id']);
            if($user){
                $user->role = $userData['role'];
                $user->save();
                return response()->json($user, Response::HTTP_OK);
            }
            $lang['ru']= 'Пользователь не найден';
            $lang['uz']= 'Foydalanuchsi topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
            
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();;
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    
    
}