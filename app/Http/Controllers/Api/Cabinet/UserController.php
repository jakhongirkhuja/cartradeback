<?php

namespace App\Http\Controllers\Api\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\UserInfoChangeRequest;
use App\Http\Requests\Cabinet\UserPasswordChangeRequest;
use App\Http\Requests\Cabinet\UserPhoneChangeRequest;
use App\Http\Requests\Cabinet\UserRoleChangeRequest;
use App\Models\User;
use App\Services\Cabinet\UserInfoService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function passwordChange(UserPasswordChangeRequest $request, UserInfoService $userData ){
        return $userData->userPasswordChange($request->validated());
    }
    public function infoChange(UserInfoChangeRequest $request, UserInfoService $userData ){
        return $userData->userInfoChange($request->validated());
    }
    public function phoneNumberChange(UserPhoneChangeRequest $request, UserInfoService $userData ){
        return $userData->phoneNumberChange($request->validated());
    }
    public function userRoleChange(UserRoleChangeRequest $request, UserInfoService $userData ){
        return $userData->roleChange($request->validated());
    }
    public function userListChange(Request $request){
        if($request->user_id){
            return response()->json(User::find($request->user_id));
        }else{
            return response()->json(User::latest()->paginate(50));
        }
    }

}
