<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['destination_id','date','time_slot','quota','booked','price','is_available'];
    protected $casts = ['date' => 'date', 'price' => 'decimal:2', 'is_available' => 'boolean'];

    public function destination() { return $this->belongsTo(Destination::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

    public function getRemaining(): int { return max(0, $this->quota - $this->booked); }
    public function getIsFull(): bool { return $this->booked >= $this->quota; }
}