<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'country', 'description',
        'price_per_night', 'rating', 'distance_km',
        'has_wifi', 'has_pool', 'has_restaurant', 'has_parking', 'has_spa',
        'image_url', 'category', 'is_featured',
    ];

    protected $casts = [
        'has_wifi'       => 'boolean',
        'has_pool'       => 'boolean',
        'has_restaurant' => 'boolean',
        'has_parking'    => 'boolean',
        'has_spa'        => 'boolean',
        'is_featured'    => 'boolean',
        'price_per_night'=> 'decimal:2',
        'rating'         => 'decimal:1',
        'distance_km'    => 'decimal:1',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function savedByUsers()
    {
        return $this->hasMany(SavedDestination::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
