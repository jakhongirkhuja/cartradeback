<?php

namespace App\Http\Controllers\Api\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\FillBalanceRequest;
use App\Http\Requests\Cabinet\TarifChooseRequest;
use App\Http\Requests\Cabinet\UserInfoChangeAdminRequest;
use App\Http\Requests\Cabinet\UserInfoChangeRequest;
use App\Http\Requests\Cabinet\UserPasswordChangeRequest;
use App\Http\Requests\Cabinet\UserPhoneChangeRequest;
use App\Http\Requests\Cabinet\UserRoleChangeRequest;
use App\Http\Requests\PassportInfoAddRequest;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\Cabinet\UserInfoService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function passwordChange(UserPasswordChangeRequest $request, UserInfoService $userData)
    {
        return $userData->userPasswordChange($request->validated());
    }
    public function infoChange(UserInfoChangeRequest $request, UserInfoService $userData)
    {
        return $userData->userInfoChange($request->validated());
    }
    public function infoChangePassport(PassportInfoAddRequest $request, UserInfoService $userData)
    {
        return $userData->infoChangePassport($request->validated());
    }
    public function phoneNumberChange(UserPhoneChangeRequest $request, UserInfoService $userData)
    {
        return $userData->phoneNumberChange($request->validated());
    }
    public function userRoleChange(UserRoleChangeRequest $request, UserInfoService $userData)
    {
        return $userData->roleChange($request->validated());
    }
    public function userInfoChangeAdmin(UserInfoChangeAdminRequest $request, UserInfoService $userData)
    {
        return $userData->userInfoChangeAdmin($request->validated());
    }
    public function userListChange(Request $request)
    {
        if ($request->user_id) {
            return response()->json(User::find($request->user_id));
        } else {
            return response()->json(User::where('id', '!=', 1)->latest()->paginate(50));
        }
    }
    public function userRemove($id, UserInfoService $userData)
    {
        return $userData->userRemove($id);
    }
    public function userTransactions()
    {
        return response()->json(UserTransaction::where('user_id', auth()->user()->id)->latest()->get());
    }
    public function userFillBalance(FillBalanceRequest $request, UserInfoService $userData)
    {
        return $userData->userFillBalancee($request->validated());
    }
    public function userTarifChoose(TarifChooseRequest $request, UserInfoService $userData)
    {
        return $userData->userTarifChoose($request->validated());
    }
}
