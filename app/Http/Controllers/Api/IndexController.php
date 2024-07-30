<?php

namespace App\Http\Controllers\Api;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\ChangeTarifRequest;
use App\Models\Auksion;
use App\Models\Cars\BodyType;
use App\Models\Cars\CarColor;
use App\Models\Cars\CarCondition;
use App\Models\Cars\CarModel;
use App\Models\Cars\FuilType;
use App\Models\Cars\Mark;
use App\Models\Cars\Transmission;
use App\Models\Enquery;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function auksion(Request $request){
        if($request->id){
            $auksions = Auksion::with('car.images','car.color','car.condation','car.carModel','car.carMark',
            'car.carBodyType', 'car.carFuilType', 'car.transmission'
            )->where('status', false)->find($request->id);
        }else{
            $auksions = Auksion::with('car.images','car.color','car.condation','car.carModel','car.carMark',
            'car.carBodyType', 'car.carFuilType', 'car.transmission'
            )->where('status', false)->latest()->paginate(50);
        }
        return response()->json($auksions);
    }
    public function loadMark(){
        return response()->json(Mark::select('id', 'name', 'cyrillic_name')->get(),Response::HTTP_OK);
    }
    public function loadModel($mark_id){
        return response()->json(CarModel::select('id', 'name', 'cyrillic_name')->where('mark_id', $mark_id)->get(),Response::HTTP_OK);
    }
    public function filters(){
        $data['marks'] = Mark::select('id', 'name', 'cyrillic_name')->get();
        $data['type'] = BodyType::all();
        $data['condition']= CarCondition::all();
        $data['colors']= CarColor::all();
        $data['fuils'] = FuilType::all();
        $data['transmissions'] = Transmission::all();
        return response()->json($data,Response::HTTP_OK);
    }
    public function enquery($type, Request $request){
        if($type=='guest'){
            $validator = Validator::make($request->all(), [
                'mark_id'=>'required',
                'model_id'=>'required',
                'phoneNumber'=>'required|size:12',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'familyName'=>'required',
                'phoneNumber'=>'required|size:12',
                
            ]);
        }
       

        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $enquery = new Enquery();
            if($type=='guest'){
                $enquery->mark_id = $data['mark_id'];
                $enquery->car_model_id = $data['model_id'];
            }else{

                $enquery->name = $data['name'];
                $enquery->familyName = $data['familyName'];
            }
            $enquery->phoneNumber = $data['phoneNumber'];
            $enquery->email = isset($data['email'])? $data['email'] : null;
            $enquery->type = $type;
            $enquery->save();
            $lang['ru']= 'Заявка отправлена';
            $lang['uz']= 'Ariza yuborildi';
            return response()->json($lang, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        

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
