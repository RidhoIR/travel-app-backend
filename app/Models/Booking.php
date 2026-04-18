<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id','destination_id','schedule_id',
        'check_in','check_out','nights','guests',
        'total_price','status','payment_status','payment_method',
    ];
    protected $casts = [
        'check_in' => 'date', 'check_out' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function destination() { return $this->belongsTo(Destination::class); }
    public function schedule() { return $this->belongsTo(Schedule::class); }
}
