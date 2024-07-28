<?php
namespace App\Services\Auth;

use App\Helper\ErrorHelperResponse;
use App\Models\PhoneNumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService {
    
    public function userRegisterSmSSend($userData, $password){
        $userPhone = PhoneNumber::where('phoneNumber', $userData['phoneNumber'])->first();
        if(!$userPhone){
            $userPhone = new PhoneNumber();
        }
        $userPhone->phoneNumber = $userData['phoneNumber'];
        $userPhone->verify_number = mt_rand(100000, 1000000);        
        $userPhone->verify_number_at = Carbon::now();
        $userPhone->status = false;
       
        $userPhone->save();


        if($password!=null){

        }else{

        }
        return response()->json($userPhone);
    }
    public function userRegisterPhoneConfirm($userData){
        $userPhone = PhoneNumber::where('phoneNumber', $userData->phoneNumber)->first();
        $start = \Carbon\Carbon::parse($userData->verify_number_at);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
        if((int)$diffhour>0){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }

        if((int)$diffminut>5){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        if($userPhone->verify_number==$$userData->verify_number){
            // $number->random = Str::random(5);
            $userPhone->status = 1;
            if($number->save()){
                $lang['ru']= 'Номер активирован';
                $lang['uz']= 'Raqam aktiv holata';
                $validate['message'] =$lang;
                $validate['code']=$number->random;
                $validate['phoneNumber']=$number->phoneNumber;
                return ErrorHelperResponse::returnError($validate,Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Something wrong please connect with admin',Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        
        }
    }
    public function userRegister($userData)
    {
       
        $userPhone = PhoneNumber::where('phoneNumber', $userData['phoneNumber'])->first();
        if(!$userPhone){
            $lang['ru']= 'Номер не подтверждения, нужно сначала отправить код';
            $lang['uz']= 'Raqam tasdiqlanmagan, tasdiqlash raqamini yuboring';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $start = Carbon::parse($userPhone['verify_number_at']);
        $end = Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');
        if((int)$diffhour>0){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }

        if((int)$diffminut>5){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        
        if($userPhone->verify_number!=$userData['verify_number']){
            $lang['ru']= 'Неверный код подтверждения';
            $lang['uz']= 'Tasdiqlash kodi xato kiritilgan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        
        try {
            $user = User::where('phoneNumber',$userData['phoneNumber'] )->first();
            if($user){
                $lang['ru']= 'Пользователь с таким номером существует';
                $lang['uz']= 'Bunday raqam ega bo`lgan foydalanuvchi mavjud';
                return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
            }
            $user = new User();
            $user->name = $userData['name'];
            $user->familyName = $userData['familyName'];
            $user->phoneNumber = $userData['phoneNumber'];
            $user->verify_number = $userPhone->verify_number;
            $user->verify_number_at = Carbon::now();
            $user->role = $userData['role']==1? 'dealer' : 'client';
            $user->password = Hash::make($userData['password']);
            $user->save();
            $userPhone->status = 1;
            $userPhone->save();
        } catch (\Throwable $th) {
            $lang['ru']= $th->getMessage();
            $lang['uz']= $th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $lang2['ru']= 'Регистрация успешно завершена';
        $lang2['uz']= 'Roʻyxatdan oʻtish muvaffaqiyatli yakunlandi';
        
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
       
        $response['user']= $user;
        $response['token'] = $token;
        $response['response'] = $lang2;
        $lang['message']  = $response;
        return response()->json($lang);
    }
    public function userLogin($userData){
        $user = User::where('phoneNumber', $userData['phoneNumber'])->first();
        if(!$user){
            $lang['ru']= 'Пользователь не найден';
            $lang['uz']= 'Foydalanuvchi topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $phoneNumber = PhoneNumber::where('phoneNumber', $userData['phoneNumber'])->first();
        
        if(!$phoneNumber || !$phoneNumber->status){
            $lang['ru']= 'Пользователь не найден или Номер не был активирован';
            $lang['uz']= 'Foydalanuvchi topilmadi yoki raqam faolashtirilmagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        if (Hash::check($userData['password'], $user->password)) {
            $lang2['ru']= 'Авторизация успешно пройдена';
            $lang2['uz']= 'Avtorizatsiya muvaffaqiyatli yakunlandi';
            $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
            $response['user']= $user;
            $response['token'] = $token;
            $response['response'] = $lang2;
            $lang['message']  = $response;
            return response()->json($lang);
        }else{
            $lang['ru']= 'Пароль не правильный';
            $lang['uz']= 'Mahfiy so`z notog`ri';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_FORBIDDEN);
        }
        
    }
    public function resetPassword($userData){
        $userPhone = PhoneNumber::where('phoneNumber', $userData['phoneNumber'])->first();
        if($userPhone->verify_number!=$userData['verify_number']){
            $lang['ru']= 'Неверный код подтверждения';
            $lang['uz']= 'Tasdiqlash kodi xato kiritilgan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $user = User::where('phoneNumber', $userData['phoneNumber'])->first();
        if(!$user){
            $lang['ru']= 'Пользователь не найден или Номер не был активирован';
            $lang['uz']= 'Foydalanuvchi topilmadi yoki raqam faolashtirilmagan';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $userPhone->status = true;
        $userPhone->save();
        $str= Str::random(8);
        $user->password = Hash::make($str);
        $user->save();
        return response()->json($str);
    }
}