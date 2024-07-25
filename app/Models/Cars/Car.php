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
        return $this->belongsTo(Auksion::class);
    }
    
    public function color()
    {
        return $this->belongsTo(CarColor::class);
    }
    public function condation()
    {
        return $this->belongsTo(CarCondition::class);
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
    public function carMark()
    {
        return $this->belongsTo(Mark::class);
    }
    public function carBodyType()
    {
        return $this->belongsTo(BodyType::class);
    }
    public function carFuilType()
    {
        return $this->belongsTo(FuilType::class);
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
