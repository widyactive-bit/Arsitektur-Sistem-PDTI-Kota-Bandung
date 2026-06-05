<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $fillable = [
        'nama',
        'lisensi',
        'level',
        'masa_berlaku',
        'foto',
    ];

    protected $casts = [
        'masa_berlaku' => 'date',
    ];
}
