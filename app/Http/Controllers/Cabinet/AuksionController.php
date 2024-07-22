<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auksion\AuksionPostRequest;
use App\Models\Auksion;
use App\Services\Cabinet\AuksionService;
use Illuminate\Http\Request;

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
}
