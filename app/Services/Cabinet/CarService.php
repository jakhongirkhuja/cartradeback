<?php

namespace App\Services\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Models\Auksion;
use App\Models\Cars\Car;
use App\Models\Cars\CarImage;
use App\Models\PhoneNumber;
use App\Models\Review;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class CarService {
    public function carPost($userData){
        try {
            $carFound = Car::where('vin', $userData['vin'])->first();
            if($carFound){
                $lang['ru']= 'Лот уже существует';
                $lang['uz']= 'Lot mavjud';
                return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
            }
            $auksion = new Auksion();
            $auksion->user_id = auth()->user()->id;
            $auksion->time_end = Carbon::parse($userData['time_end']);
            $auksion->time_start = Carbon::parse($userData['time_start']);
            $auksion->key = Str::orderedUuid();
            $auksion->status = false;
            $auksion->save();
            $car = new Car();
            $car->user_id = auth()->user()->id;
            $car->title = $userData['title'];
            $car->start_price = (int) filter_var($userData['start_price'], FILTER_SANITIZE_NUMBER_INT);
            $car->buy_price = (int) filter_var($userData['buy_price'], FILTER_SANITIZE_NUMBER_INT);
            $car->mark_id = (int)$userData['mark_id'];
            $car->car_model_id = (int) $userData['car_model_id'];
            $car->car_color_id =(int) $userData['car_color_id'];
            $car->transmission_id =(int) $userData['transmission_id'];
            $car->car_condtion_id =(int) $userData['car_condtion_id'];
            $car->body_type_id = (int)$userData['body_type_id'];
            $car->fuil_type_id = (int)$userData['fuil_type_id'];
            $car->drive_types = $userData['drive_types'];
            $car->year =(int) $userData['year'];
            $car->mileage = $userData['mileage'];
            $car->engine_capacity = $userData['engine_capacity'];
            $car->doors =(int) $userData['doors'];
            $car->cylinders =(int) $userData['cylinders'];
            $car->vin = $userData['vin'];
            $car->auksion_id =$auksion->id;
            
            $car->salon =(int)$userData['salon'];
            $car->engine =(int) $userData['engine'];
            $car->carbody = (int)$userData['carbody'];
            $car->body = $userData['body'];
            $car->functions = $userData['functions'];
            $car->status = true;
            $car->save();

            foreach ($userData['images'] as $key => $images) {
                $imageName = (string) Str::uuid().'-'.Str::random(15).'.'.$images->getClientOriginalExtension();
                $images->move(public_path('/files/cars'),$imageName);
                $image = new CarImage();
                $image->car_id = $car->id;
                $image->image = $imageName;
                $image->save();
            }
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка '.$th->getMessage();
            $lang['uz']= 'Xatolik '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($car, Response::HTTP_CREATED);
    }
    public function carEdit($userData, $id){
        $user = auth()->user();
        if($user->role=='admin'){
            $car = Car::find($id);
        }else{
            $car = Car::where('id',$id)->where('user_id', $user->id)->first();
        }
        
        
        if(!$car){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        if($car->vin!=$userData['vin']){
            $carVin = Car::where('vin',$userData['vin'])->where('id', $car->id)->first();
            if($carVin){
                $lang['ru']= 'Лот по vin номер уже существует';
                $lang['uz']= 'Lot vin raqam bo`yicha mavjud';
                return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
            }
        }
        
        $auksion = Auksion::find($car->auksion_id);
        if(!$auksion){
            $lang['ru']= 'Лот не существует';
            $lang['uz']= 'Lot mavjud emas';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            $auksion->time_end = Carbon::parse($userData['time_end']);
            $auksion->time_start = Carbon::parse($userData['time_start']);
            $auksion->status = false;
            $auksion->save();
            $car->title = $userData['title'];
            $car->start_price = (int) filter_var($userData['start_price'], FILTER_SANITIZE_NUMBER_INT);
            $car->buy_price = (int) filter_var($userData['buy_price'], FILTER_SANITIZE_NUMBER_INT);
            $car->mark_id = (int)$userData['mark_id'];
            $car->car_model_id = (int) $userData['car_model_id'];
            $car->car_color_id =(int) $userData['car_color_id'];
            $car->transmission_id =(int) $userData['transmission_id'];
            $car->car_condtion_id =(int) $userData['car_condtion_id'];
            $car->body_type_id = (int)$userData['body_type_id'];
            $car->fuil_type_id = (int)$userData['fuil_type_id'];
            $car->drive_types = $userData['drive_types'];
            $car->year =(int) $userData['year'];
            $car->mileage = $userData['mileage'];
            $car->engine_capacity = $userData['engine_capacity'];
            $car->doors =(int) $userData['doors'];
            $car->cylinders =(int) $userData['cylinders'];
            $car->vin = $userData['vin'];
            $car->auksion_id =$auksion->id;
            $car->key = Str::orderedUuid();
            $car->salon =(int)$userData['salon'];
            $car->engine =(int) $userData['engine'];
            $car->carbody = (int)$userData['carbody'];
            $car->body = $userData['body'];
            $car->functions = $userData['functions'];
            $car->status = true;
            $car->save();
            
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($car, Response::HTTP_CREATED);
    }
    public function carDelete( $id){
        $user = auth()->user();
        if($user->role=='admin'){
            $car = Car::with('auksion')->find($id);
        }else{
            $car = Car::with('auksion')->where('id',$id)->where('user_id', $user->id)->first();
        }
        if(!$car){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            $images = CarImage::where('car_id', $id)->get();
            if($images->count()>0){
                foreach ($images as $key => $image) {
                    if(file_exists(public_path('/files/cars/'.$image->image))){
                        unlink(public_path('/files/cars/'.$image->image));
                    }
                    $image->delete();
                }
            }
            $auksion  = $car->aukstion;
            $car->delete();
            if($auksion) $auksion->delete();
            
            $lang['ru']= 'Удален';
            $lang['uz']= 'O`chirildi';
            return response()->json($lang, Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function carImageDelete( $id){
        $image = CarImage::with('car.user')->find($id);
        
        if(!$image){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        if(auth()->user()->role=='admin' || $image->car->user->id==auth()->user()->id){
            try {
                
                if(file_exists(public_path('/files/cars/'.$image->image))){
                    unlink(public_path('/files/cars/'.$image->image));
                }
                $image->delete();
                $lang['ru']= 'Удален';
                $lang['uz']= 'O`chirildi';
                return response()->json($lang, Response::HTTP_OK);
            } catch (\Throwable $th) {
                $lang['ru']= 'Ошибка';
                $lang['uz']= 'Xatolik';
                return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            $lang['ru']= 'Отказано в доступе';
            $lang['uz']= 'Ruxsat berilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNAUTHORIZED);
        }
    }
    public function carImageAdd( $userData, $id){
        $user = auth()->user();
        if($user->role=='admin'){
            $car = Car::find($id);
        }else{
            $car = Car::where('id',$id)->where('user_id', $user->id)->first();
        }
        if(!$car){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            
          
                $imageName = (string) Str::uuid().'-'.Str::random(15).'.'.$userData['image']->getClientOriginalExtension();
                $userData['image']->move(public_path('/files/cars'),$imageName);
                $image = new CarImage();
                $image->car_id = $id;
                $image->image = $imageName;
                $image->save();
            
            return response()->json($image, Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка';
            $lang['uz']= 'Xatolik';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    
    
    
}