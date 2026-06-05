<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table (Auth)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['Super Admin', 'Pengurus', 'Pelatih', 'Atlet', 'Wasit'])->default('Atlet');
            $table->string('position')->nullable(); // For Pengurus/Wasit
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Clubs Table
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klub');
            $table->string('alamat');
            $table->string('pelatih'); // Head coach text
            $table->integer('jumlah_atlet')->default(0);
            $table->timestamps();
        });

        // 3. Coaches Table
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('lisensi');
            $table->string('klub');
            $table->string('nomor_hp');
            $table->string('email')->unique();
            $table->string('foto')->nullable();
            $table->date('masa_berlaku_lisensi');
            $table->timestamps();
        });

        // 4. Referees Table
        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('lisensi');
            $table->string('level');
            $table->date('masa_berlaku');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // 5. Board Table (Pengurus)
        Schema::create('board', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('periode');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // 6. Athletes Table
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk_atlet')->unique();
            $table->string('nama_lengkap');
            $table->string('nik')->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('no_hp');
            $table->string('email')->unique();
            $table->string('foto')->nullable();
            $table->string('klub'); // Text or relation
            $table->foreignId('pelatih_id')->nullable()->constrained('coaches')->nullOnDelete();
            $table->decimal('tinggi_badan', 5, 2); // cm
            $table->decimal('berat_badan', 5, 2);  // kg
            $table->string('kelas_tanding');
            $table->string('sabuk'); // Belt/Level in sepak takraw (or standard tier)
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });

        // 7. Achievements Table (Prestasi)
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->string('nama_kejuaraan');
            $table->string('tingkat'); // Kota, Provinsi, Nasional, Internasional
            $table->string('lokasi');
            $table->date('tanggal');
            $table->string('hasil'); // Juara 1, Babak Penyisihan, dll
            $table->string('medali')->nullable(); // Emas, Perak, Perunggu, Tanpa Medali
            $table->timestamps();
        });

        // 8. Athlete Statistics Table (Bulanan/Histori)
        Schema::create('athlete_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            
            // Teknik
            $table->decimal('tendangan', 5, 2)->default(0);
            $table->decimal('pukulan', 5, 2)->default(0); // Dada/Paha block
            $table->decimal('akurasi', 5, 2)->default(0);
            $table->decimal('kecepatan', 5, 2)->default(0);
            
            // Fisik
            $table->decimal('endurance', 5, 2)->default(0);
            $table->decimal('agility', 5, 2)->default(0);
            $table->decimal('flexibility', 5, 2)->default(0);
            $table->decimal('strength', 5, 2)->default(0);
            
            // Mental
            $table->decimal('disiplin', 5, 2)->default(0);
            $table->decimal('fokus', 5, 2)->default(0);
            $table->decimal('leadership', 5, 2)->default(0);
            
            $table->date('record_date'); // To track monthly history
            $table->timestamps();
        });

        // 9. Attendance Table (Absensi GPS)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->timestamp('checkin_time');
            $table->timestamp('checkout_time')->nullable();
            $table->integer('duration')->nullable(); // in minutes
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('selfie'); // Path to selfie photo
            $table->timestamps();
        });

        // 10. Schedules Table (Jadwal Latihan)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatih_id')->constrained('coaches')->onDelete('cascade');
            $table->foreignId('klub_id')->constrained('clubs')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('lokasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('athlete_stats');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('athletes');
        Schema::dropIfExists('board');
        Schema::dropIfExists('referees');
        Schema::dropIfExists('coaches');
        Schema::dropIfExists('clubs');
        Schema::dropIfExists('users');
    }
};
