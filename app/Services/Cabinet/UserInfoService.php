<?php

namespace App\Services\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Models\Auksion;
use App\Models\Cars\Car;
use App\Models\Cars\CarImage;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserInfoService {
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