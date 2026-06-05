<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'nama_klub',
        'alamat',
        'pelatih',
        'jumlah_atlet',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'klub_id');
    }
}
