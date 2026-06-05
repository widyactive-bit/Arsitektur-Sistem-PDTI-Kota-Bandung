<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'pelatih_id',
        'klub_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'pelatih_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'klub_id');
    }
}
