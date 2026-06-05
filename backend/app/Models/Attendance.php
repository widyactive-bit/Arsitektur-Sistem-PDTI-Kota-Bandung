<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'athlete_id',
        'checkin_time',
        'checkout_time',
        'duration',
        'latitude',
        'longitude',
        'selfie',
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
        'duration' => 'integer',
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
}
