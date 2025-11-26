<?php

namespace App\Models;

use App\Models\Cars\Car;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'car_id',
        'user_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    protected static function booted()
    {
        static::creating(function ($booking) {
            if (empty($booking->order_number)) {
                $booking->order_number = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }
    public function history()
    {
        return $this->hasMany(BookingHistory::class);
    }
}
