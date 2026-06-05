<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    protected $fillable = [
        'nama',
        'lisensi',
        'klub',
        'nomor_hp',
        'email',
        'foto',
        'masa_berlaku_lisensi',
    ];

    protected $casts = [
        'masa_berlaku_lisensi' => 'date',
    ];

    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class, 'pelatih_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'pelatih_id');
    }
}
