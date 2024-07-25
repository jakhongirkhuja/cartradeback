<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\ChangeTarifRequest;
use App\Models\Auksion;
use App\Models\Cars\BodyType;
use App\Models\Cars\CarCondition;
use App\Models\Cars\Mark;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IndexController extends Controller
{
    public function auksion(){
        $auksions = Auksion::with('car')->where('status', true)->latest()->paginate(50);
        return response()->json($auksions);
    }
    public function filters(){
        $data['marks'] = Mark::with(['models' => function($query) {
            $query->select('mark_id', 'name','cyrillic_name');
        }])->select('id', 'name', 'cyrillic_name')->get();
        $data['type'] = BodyType::all();
        $data['condition']= CarCondition::all();
        return response()->json($data,Response::HTTP_OK);
    }
    public function auksionBet(){
        
    }
    public function tarifs(Request $request){
        if($request->id){
            return response()->json(Tarif::find($request->id));
        }
        return response()->json(Tarif::orderby('order','asc')->get());
    }
    public function changeTarif(ChangeTarifRequest $request, $id){
        $data = $request->validated();
        $tarif = Tarif::find($id);
        return $tarif->saveModel($data);
    }
}
