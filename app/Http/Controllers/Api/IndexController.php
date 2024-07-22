<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auksion;
use App\Models\Cars\BodyType;
use App\Models\Cars\CarCondition;
use App\Models\Cars\Mark;
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
}
