<?php

namespace App\Models;

use App\Models\Cars\Car;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCheckResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'car_id',
        'car_check_id',
        'status',
        'comment'
    ];
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function check()
    {
        return $this->belongsTo(CarCheck::class, 'car_check_id');
    }
}
