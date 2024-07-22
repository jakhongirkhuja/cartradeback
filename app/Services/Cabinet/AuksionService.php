<?php

namespace App\Services\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Models\Auksion;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\User;
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
    
    
}