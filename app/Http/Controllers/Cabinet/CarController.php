<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\CarEditRequest;
use App\Http\Requests\Car\CarImageEditRequest;
use App\Http\Requests\Car\CarPostRequest;
use App\Models\Auksion;
use App\Models\Cars\Car;
use App\Services\Cabinet\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function car(Request $request){
        
        if($request->id){
            if(auth()->user()->role=='admin'){
                $car = Car::with('images','auksion','carMark','carModel','color','condation','carBodyType','carFuilType','transmission')->find($request->id);

            }else{
                $car = Car::with('images','auksion','carMark','carModel','color','condation','carBodyType','carFuilType','transmission')->where('user_id', auth()->user()->id)->find($request->id);

            }
        }else{
            if(auth()->user()->role=='admin'){
                $car = Car::with('images','auksion','carMark','carModel','color','condation','carBodyType','carFuilType','transmission')->latest()->paginate(50);

            }else{
                $car = Car::with('images','auksion','carMark','carModel','color','condation','carBodyType','carFuilType','transmission')->where('user_id', auth()->user()->id)->latest()->paginate(50);

            }
        }
        
        return response()->json($car);
    }
    public function carBet(){
        $auksion = Auksion::with('car.images','car.carMark','car.carModel','car.color','car.condation','car.carBodyType','car.carFuilType','car.transmission','auksionHistory')->whereHas('auksionHistory', function($q){
            $q->where('user_id', auth()->user()->id);
        })->get();
        return response()->json($auksion);
    }
    public function carPost(CarPostRequest $request, CarService $carService){
        return $carService->carPost($request->validated());
    }
    public function carEdit($id,CarEditRequest $request, CarService $auksion){
        return $auksion->carEdit($request->validated(), $id);
    }
    public function carDelete($id, CarService $auksion){
        return $auksion->carDelete($id);
    }
    public function carImageDelete($id, CarService $auksion){
        return $auksion->carImageDelete($id);
    }
    public function carImageAdd($id, CarImageEditRequest $request, CarService $auksion){
        return $auksion->carImageAdd($request->validated(), $id);
    }
}
