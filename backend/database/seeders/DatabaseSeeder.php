<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Referee;
use App\Models\Board;
use App\Models\Athlete;
use App\Models\Achievement;
use App\Models\AthleteStat;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users (Roles: Super Admin, Pengurus, Pelatih, Atlet, Wasit)
        User::create([
            'name' => 'Super Admin PSAMS',
            'email' => 'superadmin@psti.bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Super Admin',
            'position' => 'System Administrator'
        ]);

        User::create([
            'name' => 'Dr. H. Ahmad Wijaya',
            'email' => 'pengurus@psti.bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Pengurus',
            'position' => 'Ketua Umum'
        ]);

        User::create([
            'name' => 'Coach Herman Sulistyo',
            'email' => 'pelatih@psti.bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Pelatih',
            'position' => 'Pelatih Kepala'
        ]);

        User::create([
            'name' => 'Muhammad Rafli',
            'email' => 'atlet@psti.bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Atlet'
        ]);

        User::create([
            'name' => 'Bambang Triyadi',
            'email' => 'wasit@psti.bandung.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Wasit',
            'position' => 'Wasit Utama'
        ]);

        // 2. Create Takraw Clubs
        $club1 = Club::create([
            'nama_klub' => 'Bandung Takraw Club',
            'alamat' => 'Gelanggang Olahraga Pajajaran, Bandung',
            'pelatih' => 'Herman Sulistyo',
            'jumlah_atlet' => 24
        ]);

        $club2 = Club::create([
            'nama_klub' => 'Siliwangi Takraw Club',
            'alamat' => 'Komplek Militer Siliwangi, Bandung',
            'pelatih' => 'Aris Setiawan',
            'jumlah_atlet' => 18
        ]);

        // 3. Create Coaches
        $coach1 = Coach::create([
            'nama' => 'Herman Sulistyo',
            'lisensi' => 'ASTAF Level 2 (Asia)',
            'klub' => 'Bandung Takraw Club',
            'nomor_hp' => '0812-3456-7890',
            'email' => 'herman.s@psti.bandung.go.id',
            'masa_berlaku_lisensi' => '2028-12-31'
        ]);

        $coach2 = Coach::create([
            'nama' => 'Aris Setiawan',
            'lisensi' => 'Nasional A',
            'klub' => 'Siliwangi Takraw Club',
            'nomor_hp' => '0813-9876-5432',
            'email' => 'aris.s@psti.bandung.go.id',
            'masa_berlaku_lisensi' => '2027-06-30'
        ]);

        // 4. Create Referees (Wasit)
        Referee::create([
            'nama' => 'Bambang Triyadi',
            'lisensi' => 'ISTAF International Referee',
            'level' => 'A-International',
            'masa_berlaku' => '2029-05-18'
        ]);

        Referee::create([
            'nama' => 'Hendra Gunawan',
            'lisensi' => 'Nasional Utama',
            'level' => 'Nasional A',
            'masa_berlaku' => '2027-11-20'
        ]);

        // 5. Create Board Members (Pengurus)
        Board::create([
            'nama' => 'Dr. H. Ahmad Wijaya, M.Si.',
            'jabatan' => 'Ketua Umum',
            'periode' => '2024 - 2028'
        ]);

        Board::create([
            'nama' => 'Drs. Cecep Hermawan',
            'jabatan' => 'Sekretaris Umum',
            'periode' => '2024 - 2028'
        ]);

        // 6. Create Athletes
        $athlete1 = Athlete::create([
            'nomor_induk_atlet' => 'PSTI-2026-0001',
            'nama_lengkap' => 'Muhammad Rafli',
            'nik' => '3273012345670001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2004-08-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Lombok No. 12, Sumur Bandung, Kota Bandung',
            'no_hp' => '0811-2233-4455',
            'email' => 'atlet@psti.bandung.go.id',
            'klub' => 'Bandung Takraw Club',
            'pelatih_id' => $coach1->id,
            'tinggi_badan' => 178.50,
            'berat_badan' => 68.00,
            'kelas_tanding' => 'Regu Putra (Killer)',
            'sabuk' => 'Utama (Level A)',
            'status' => 'Aktif'
        ]);

        $athlete2 = Athlete::create([
            'nomor_induk_atlet' => 'PSTI-2026-0002',
            'nama_lengkap' => 'Andika Wijaya',
            'nik' => '3273012345670002',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2005-03-22',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Cigadung Raya Barat No. 10, Cibeunying Kaler, Kota Bandung',
            'no_hp' => '0812-7788-9900',
            'email' => 'andika@psti.bandung.go.id',
            'klub' => 'Siliwangi Takraw Club',
            'pelatih_id' => $coach2->id,
            'tinggi_badan' => 182.00,
            'berat_badan' => 72.50,
            'kelas_tanding' => 'Regu Putra (Tekong)',
            'sabuk' => 'Madya (Level B)',
            'status' => 'Aktif'
        ]);

        // 7. Create Achievements
        Achievement::create([
            'athlete_id' => $athlete1->id,
            'nama_kejuaraan' => 'Kejuaraan Daerah Sepak Takraw Jawa Barat 2025',
            'tingkat' => 'Provinsi',
            'lokasi' => 'GOR Pajajaran Bandung',
            'tanggal' => '2025-07-15',
            'hasil' => 'Juara 1 Regu Putra',
            'medali' => 'Emas'
        ]);

        Achievement::create([
            'athlete_id' => $athlete1->id,
            'nama_kejuaraan' => 'Kejuaraan Nasional Piala Menpora 2025',
            'tingkat' => 'Nasional',
            'lokasi' => 'GOR Sumantri Brodjonegoro Jakarta',
            'tanggal' => '2025-10-20',
            'hasil' => 'Juara 3 Double Event',
            'medali' => 'Perunggu'
        ]);

        Achievement::create([
            'athlete_id' => $athlete2->id,
            'nama_kejuaraan' => 'Kejurda Jabar 2025',
            'tingkat' => 'Provinsi',
            'lokasi' => 'GOR Pajajaran Bandung',
            'tanggal' => '2025-07-15',
            'hasil' => 'Juara 2 Regu Putra',
            'medali' => 'Perak'
        ]);

        // 8. Create Athlete Stats (Histori Bulanan)
        // Month 1 (April 2026)
        AthleteStat::create([
            'athlete_id' => $athlete1->id,
            'tendangan' => 78.00,
            'pukulan' => 80.00,
            'akurasi' => 75.00,
            'kecepatan' => 82.00,
            'endurance' => 70.00,
            'agility' => 75.00,
            'flexibility' => 85.00,
            'strength' => 74.00,
            'disiplin' => 80.00,
            'fokus' => 78.00,
            'leadership' => 85.00,
            'record_date' => '2026-04-30'
        ]);

        // Month 2 (Mei 2026)
        AthleteStat::create([
            'athlete_id' => $athlete1->id,
            'tendangan' => 82.00,
            'pukulan' => 84.00,
            'akurasi' => 80.00,
            'kecepatan' => 85.00,
            'endurance' => 75.00,
            'agility' => 78.00,
            'flexibility' => 88.00,
            'strength' => 78.00,
            'disiplin' => 85.00,
            'fokus' => 82.00,
            'leadership' => 88.00,
            'record_date' => '2026-05-31'
        ]);

        // Athlete 2 Stats
        AthleteStat::create([
            'athlete_id' => $athlete2->id,
            'tendangan' => 75.00,
            'pukulan' => 70.00,
            'akurasi' => 85.00,
            'kecepatan' => 76.00,
            'endurance' => 80.00,
            'agility' => 72.00,
            'flexibility' => 70.00,
            'strength' => 75.00,
            'disiplin' => 88.00,
            'fokus' => 85.00,
            'leadership' => 70.00,
            'record_date' => '2026-05-31'
        ]);

        // 9. Create Attendances
        Attendance::create([
            'athlete_id' => $athlete1->id,
            'checkin_time' => Carbon::parse('2026-06-04 08:00:00'),
            'checkout_time' => Carbon::parse('2026-06-04 10:30:00'),
            'duration' => 150, // 2.5 hours
            'latitude' => -6.90389,
            'longitude' => 107.61861,
            'selfie' => 'selfie_mock_1.jpg'
        ]);

        Attendance::create([
            'athlete_id' => $athlete1->id,
            'checkin_time' => Carbon::parse('2026-06-05 08:00:00'),
            'checkout_time' => null,
            'duration' => null,
            'latitude' => -6.90389,
            'longitude' => 107.61861,
            'selfie' => 'selfie_mock_2.jpg'
        ]);

        // 10. Create Schedules
        Schedule::create([
            'pelatih_id' => $coach1->id,
            'klub_id' => $club1->id,
            'tanggal' => '2026-06-08',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '11:00:00',
            'lokasi' => 'GOR Pajajaran Lapangan Takraw A'
        ]);

        Schedule::create([
            'pelatih_id' => $coach2->id,
            'klub_id' => $club2->id,
            'tanggal' => '2026-06-09',
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '17:00:00',
            'lokasi' => 'Lapangan Takraw Siliwangi'
        ]);
    }
}
