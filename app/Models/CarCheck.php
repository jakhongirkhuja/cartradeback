<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCheck extends Model
{
    use HasFactory;
    public function results()
    {
        return $this->hasMany(CarCheckResult::class);
    }
    public function category()
    {
        return $this->belongsTo(CarCheckCategory::class, 'car_check_category_id');
    }

    // Each car check belongs to a sub-category
    public function subCategory()
    {
        return $this->belongsTo(CarCheckSubCategory::class, 'car_check_sub_category_id');
    }
}
