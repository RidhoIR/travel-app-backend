<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'destination_id',
        'check_in', 'check_out', 'nights',
        'total_price', 'status',
    ];

    protected $casts = [
        'check_in'    => 'date',
        'check_out'   => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
