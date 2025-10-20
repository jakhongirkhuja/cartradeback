<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCheckSubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['title_ru', 'title_uz', 'title_en', 'order', 'type', 'car_check_category_id'];
}
