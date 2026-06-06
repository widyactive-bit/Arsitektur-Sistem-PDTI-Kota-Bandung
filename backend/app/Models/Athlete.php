<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Athlete extends Model
{
    protected $fillable = [
        'nomor_induk_atlet',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'email',
        'foto',
        'klub',
        'pelatih_id',
        'tinggi_badan',
        'berat_badan',
        'kelas_tanding',
        'sabuk',
        'status',
        'ktp',
        'kk',
        'sertifikat',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
        'sertifikat' => 'array',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'pelatih_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'athlete_id');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(AthleteStat::class, 'athlete_id');
    }

    public function latestStat(): HasOne
    {
        return $this->hasOne(AthleteStat::class, 'athlete_id')->latestOfMany('record_date');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'athlete_id');
    }

    /**
     * Calculate score using PSAMS formula:
     * score = (teknik*0.4) + (fisik*0.3) + (mental*0.1) + (prestasi*0.2)
     */
    public function calculateRankingScore(): float
    {
        $latest = $this->latestStat;
        if (!$latest) {
            return 0.0;
        }

        $teknik = ($latest->tendangan + $latest->pukulan + $latest->akurasi + $latest->kecepatan) / 4;
        $fisik = ($latest->endurance + $latest->agility + $latest->flexibility + $latest->strength) / 4;
        $mental = ($latest->disiplin + $latest->fokus + $latest->leadership) / 3;
        
        // Prestasi calculation: 20 points per achievement (capped at 100)
        $achievementsCount = $this->achievements()->count();
        $prestasi = min(100.0, $achievementsCount * 20.0);

        return ($teknik * 0.4) + ($fisik * 0.3) + ($mental * 0.1) + ($prestasi * 0.2);
    }
}
