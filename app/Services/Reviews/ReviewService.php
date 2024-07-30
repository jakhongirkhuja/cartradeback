<?php

namespace App\Services\Reviews;

use App\Helper\ErrorHelperResponse;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class ReviewService {
    public function reviewPost($userData){
        try {
            $reviews = new Review();
            if(isset($userData['avatar'])){
                $avatar = (string) Str::uuid().'-'.Str::random(15).'.'.$userData['avatar']->getClientOriginalExtension();
                $userData['avatar']->move(public_path('/files/reviews'),$avatar);
            }
            if(auth()->user()){
                $reviews->user_id = auth()->user()->id;
            }else{
                
            }
            $reviews->fullName = $userData['fullname'];
            $reviews->email = $userData['email'];
            $reviews->body = $userData['body'];
            $reviews->status = isset($userData['status'])? $userData['status'] : false;
            $reviews->save();
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($reviews, Response::HTTP_CREATED);
    }
    public function reviewEdit($userData, $id){
        $reviews = Review::find($id);
        if(!$reviews){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            
    
            if($userData->hasFile('avatar')){
                if(file_exists(public_path('/files/reviews/'.$reviews->avatar))){
                    unlink(public_path('/files/reviews/'.$reviews->avatar));
                }
                $avatar = (string) Str::uuid().'-'.Str::random(15).'.'.$userData['avatar']->getClientOriginalExtension();
                $userData['avatar']->move(public_path('/files/reviews'),$avatar);
            }
            $reviews->fullname = $userData['fullname'];
            $reviews->email = $userData['email'];
            $reviews->body = $userData['body'];
            $reviews->status = isset($userData['status'])? $userData['status'] : false;
            $reviews->save();
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($reviews, Response::HTTP_CREATED);
    }
    
    
}