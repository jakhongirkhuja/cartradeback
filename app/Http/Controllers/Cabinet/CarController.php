<?php

namespace App\Http\Controllers\Cabinet;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Car\CarEditRequest;
use App\Http\Requests\Car\CarImageEditRequest;
use App\Http\Requests\Car\CarPostRequest;
use App\Http\Requests\CarChangeStatus;
use App\Models\Auksion;
use App\Models\Cars\Car;
use App\Services\Cabinet\CarService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CarController extends Controller
{
    public function car(Request $request)
    {

        if ($request->id) {
            if (auth()->user()->role == 'admin') {
                $car = Car::with('images', 'auksion', 'carMark', 'carModel', 'color', 'condation', 'carBodyType', 'carFuilType', 'transmission', 'checkResults.check')->find($request->id);
            } else {
                $car = Car::with('images', 'auksion', 'carMark', 'carModel', 'color', 'condation', 'carBodyType', 'carFuilType', 'transmission')->where('user_id', auth()->user()->id)->find($request->id);
            }
        } else {
            if (auth()->user()->role == 'admin') {
                $car = Car::with('images', 'auksion', 'carMark', 'carModel', 'color', 'condation', 'carBodyType', 'carFuilType', 'transmission')->latest()->paginate(50);
            } else {
                $car = Car::with('images', 'auksion', 'carMark', 'carModel', 'color', 'condation', 'carBodyType', 'carFuilType', 'transmission')->where('user_id', auth()->user()->id)->latest()->paginate(50);
            }
        }

        return response()->json($car);
    }
    public function carBet()
    {
        $auksion = Auksion::with('car.images', 'car.carMark', 'car.carModel', 'car.color', 'car.condation', 'car.carBodyType', 'car.carFuilType', 'car.transmission', 'auksionHistory')->whereHas('auksionHistory', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->get();
        return response()->json($auksion);
    }
    public function carPost(CarPostRequest $request, CarService $carService)
    {
        return $carService->carPost($request->validated());
    }
    public function carEdit($id, CarEditRequest $request, CarService $auksion)
    {
        return $auksion->carEdit($request->validated(), $id);
    }
    public function carDelete($id, CarService $auksion)
    {
        return $auksion->carDelete($id);
    }
    public function carImageDelete($id, CarService $auksion)
    {
        return $auksion->carImageDelete($id);
    }
    public function checksSave($id, $type, Request $request, CarService $auksion)
    {
        $user = auth()->user();
        if ($user->role == 'admin') {
            $car = Car::find($id);
            $car->auto_type = $type;
            $car->save();
        }
        if (!$car) {
            $lang['ru'] = 'Не найден';
            $lang['uz'] = 'Topilmadi';
            return ErrorHelperResponse::returnError($lang, Response::HTTP_NOT_FOUND);
        }
        try {
            $data = $request->validate([
                'checks' => 'required|array',
                'checks.*.id' => 'required|integer|exists:car_checks,id',
                'checks.*.status' => 'nullable|boolean',
                'checks.*.comment' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors(),
            ], 422);
        }
        return $auksion->checksSave($id, $data);
    }
    public function carImageAdd($id, CarImageEditRequest $request, CarService $auksion)
    {
        return $auksion->carImageAdd($request->validated(), $id);
    }
    public function auksionChangeStatus(CarChangeStatus $request, CarService $carservice)
    {
        return $carservice->carChangeStatus($request->validated());
    }
}
