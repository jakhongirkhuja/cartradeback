<?php

namespace App\Models;

use App\Models\Cars\Car;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auksion extends Model
{
    use HasFactory;
    
    public function car()
    {
        return $this->hasOne(Car::class, 'auksion_id','id');
    }
    public function auksionHistory(){
        return $this->hasMany(AuksionHistory::class, 'auksion_id');
    }
}
