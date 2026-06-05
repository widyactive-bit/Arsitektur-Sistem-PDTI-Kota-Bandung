<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteStat extends Model
{
    protected $fillable = [
        'athlete_id',
        'tendangan',
        'pukulan',
        'akurasi',
        'kecepatan',
        'endurance',
        'agility',
        'flexibility',
        'strength',
        'disiplin',
        'fokus',
        'leadership',
        'record_date',
    ];

    protected $casts = [
        'record_date' => 'date',
        'tendangan' => 'float',
        'pukulan' => 'float',
        'akurasi' => 'float',
        'kecepatan' => 'float',
        'endurance' => 'float',
        'agility' => 'float',
        'flexibility' => 'float',
        'strength' => 'float',
        'disiplin' => 'float',
        'fokus' => 'float',
        'leadership' => 'float',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
}
