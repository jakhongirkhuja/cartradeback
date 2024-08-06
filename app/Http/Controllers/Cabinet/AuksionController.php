<?php

namespace App\Http\Controllers\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auksion\AuksionBetRequest;
use App\Http\Requests\Auksion\AuksionBuyRequest;
use App\Http\Requests\Auksion\AuksionChangeStatus;
use App\Http\Requests\Auksion\AuksionPostRequest;
use App\Models\Auksion;
use App\Models\AuksionHistory;
use App\Services\Cabinet\AuksionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuksionController extends Controller
{
    public function auksion(Request $request){
        if($request->id){
            $auksion = Auksion::whereHas('car', function ($query){
                $query->where('user_id', auth()->user()->id);
            })->find($request->id);
        }else{
            $auksion = Auksion::whereHas('car', function ($query){
                $query->where('user_id', auth()->user()->id);
            })->latest()->paginate(20);
        }
        return response()->json($auksion);
    }
    public function auksionPost(AuksionPostRequest $request, AuksionService $auksion){
        return $auksion->auksionPost($request->validated());
    }
    public function auksionEdit($id,AuksionPostRequest $request, AuksionService $auksion){
        return $auksion->auksionEdit($request->validated(), $id);
    }
    public function auksionDelete($id, AuksionService $auksion){
        return $auksion->auksionDelete($id);
    }


    public function auksionBet(AuksionBetRequest $request, AuksionService $auksion){
        return $auksion->auksionBet($request->validated());
    }
    public function auksionBuy(AuksionBuyRequest $request, AuksionService $auksion){
        return $auksion->auksionBuy($request->validated());
    }
    public function auksionlastPrice($id){
        $bitPrice = AuksionHistory::select('bid_price')->where('auksion_id',$id)->orderby('bid_price','desc')->first();
        if($bitPrice){
            return response()->json($bitPrice);
        }
        $lang['ru']= 'Не найден';
        $lang['uz']= 'Topilmadi';
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
    }
    public function auksionChangeStatus(AuksionChangeStatus $request, AuksionService $auksion){
        return $auksion->auksionChangeStatus($request->validated());
    }
}
