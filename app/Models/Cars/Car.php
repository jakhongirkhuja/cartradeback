<?php

namespace App\Models\Cars;

use App\Models\Auksion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
   
    public function aukstion()
    {
        return $this->belongsTo(Auksion::class,'auksion_id');
    }
    
    public function color()
    {
        return $this->belongsTo(CarColor::class,'car_color_id');
    }
    public function condation()
    {
        return $this->belongsTo(CarCondition::class,'car_condtion_id');
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
    public function carMark()
    {
        return $this->belongsTo(Mark::class,'mark_id');
    }
    public function carBodyType()
    {
        return $this->belongsTo(BodyType::class,'body_type_id');
    }
    public function carFuilType()
    {
        return $this->belongsTo(FuilType::class,'fuil_type_id');
    }
    public function transmission(){
        return $this->belongsTo(Transmission::class,'transmission_id');
    }
    
    public function images()
    {
        return $this->hasMany(CarImage::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
