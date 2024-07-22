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
        return $this->hasOne(Car::class);
    }
}
