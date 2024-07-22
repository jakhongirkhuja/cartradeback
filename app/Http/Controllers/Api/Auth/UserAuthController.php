<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAuth\UserForgetPasswordRequest;
use App\Http\Requests\UserAuth\UserLoginRequest;
use App\Http\Requests\UserAuth\UserRegisterRequest;
use App\Http\Requests\UserAuth\UserRegisterSmsRequest;
use App\Services\Auth\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAuthController extends Controller
{
    public function registerSendSms(UserRegisterSmsRequest $request, UserService $userService){
        try {
            $userService = $userService->userRegisterSmSSend($request->validated(),false);
        } catch (\Exception $exception) {
            $lang['ru']= $exception->getMessage();
            $lang['uz']= $exception->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $userService;
    }
    public function register(UserRegisterRequest $request, UserService $userService){
        try {
            
            $userService = $userService->userRegister($request->validated());
        } catch (\Exception $exception) {
            $lang['ru']= $exception->getMessage();
            $lang['uz']= $exception->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $userService;
    }
    public function login(UserLoginRequest $request, UserService $userService){
        try {
            $userService = $userService->userLogin($request->validated());
        } catch (\Exception $exception) {
            $lang['ru']= $exception->getMessage();
            $lang['uz']= $exception->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $userService;
    }
    public function resetPassword(UserForgetPasswordRequest $request, UserService $userService){
        try {
            $userService = $userService->resetPassword($request->validated());
        } catch (\Exception $exception) {
            $lang['ru']= $exception->getMessage();
            $lang['uz']= $exception->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $userService;
    }
    
}
