<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; font-size: 12px; }
        .header { text-align: center; border-bottom: 3px double #e5b922; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 20px; color: #111; }
        .header h2 { margin: 5px 0 0 0; font-size: 13px; color: #e5b922; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; font-size: 10px; color: #666; }
        
        .section-title { font-size: 14px; font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 25px; margin-bottom: 12px; color: #111; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.profile-table td { padding: 6px; vertical-align: top; }
        table.profile-table td.label { font-weight: bold; width: 30%; color: #555; }
        
        table.data-table { border: 1px solid #ddd; }
        table.data-table th { background-color: #f5f5f5; border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left; font-size: 11px; }
        table.data-table td { border: 1px solid #ddd; padding: 8px; font-size: 11px; }
        table.data-table tr:nth-child(even) { background-color: #fafafa; }

        .score-box { background-color: #fdfbeb; border: 1px solid #f9ebbe; border-radius: 4px; padding: 12px; margin-bottom: 15px; }
        .score-box table { margin-bottom: 0; }
        .score-box .score-val { font-size: 22px; font-weight: bold; color: #b89111; text-align: right; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PSTI KOTA BANDUNG</h1>
        <h2>{{ $title }}</h2>
        <p>Gedung KONI Kota Bandung, Jl. Jakarta No. 18 | Tanggal Cetak: {{ $date }}</p>
    </div>

    <div class="score-box">
        <table>
            <tr>
                <td>
                    <span style="font-size: 14px; font-weight: bold; color: #444;">SKOR ANALITIS PSAMS (RANKING)</span><br>
                    <span style="font-size: 10px; color: #777;">Dihitung berdasarkan pembobotan teknik (40%), fisik (30%), mental (10%), dan prestasi (20%).</span>
                </td>
                <td class="score-val" style="vertical-align: middle;">
                    {{ $ranking_score }} <span style="font-size: 11px; font-weight: normal; color: #666;">/ 100</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Biodata Atlet</div>
    <table class="profile-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td>: {{ $athlete->nama_lengkap }}</td>
            <td class="label">NIA (Nomor Induk)</td>
            <td>: {{ $athlete->nomor_induk_atlet }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td>: {{ $athlete->nik }}</td>
            <td class="label">Status Keaktifan</td>
            <td>: {{ $athlete->status }}</td>
        </tr>
        <tr>
            <td class="label">Tempat / Tgl Lahir</td>
            <td>: {{ $athlete->tempat_lahir }}, {{ $athlete->tanggal_lahir->format('d-m-Y') }}</td>
            <td class="label">Jenis Kelamin</td>
            <td>: {{ $athlete->jenis_kelamin }}</td>
        </tr>
        <tr>
            <td class="label">Tinggi / Berat Badan</td>
            <td>: {{ $athlete->tinggi_badan }} cm / {{ $athlete->berat_badan }} kg</td>
            <td class="label">Kelas Tanding / Posisi</td>
            <td>: {{ $athlete->kelas_tanding }}</td>
        </tr>
        <tr>
            <td class="label">Klub Afiliasi</td>
            <td>: {{ $athlete->klub }}</td>
            <td class="label">Tingkatan / Sabuk</td>
            <td>: {{ $athlete->sabuk }}</td>
        </tr>
        <tr>
            <td class="label">Pelatih Pendamping</td>
            <td>: {{ $athlete->coach ? $athlete->coach->nama : '-' }}</td>
            <td class="label">Alamat Lengkap</td>
            <td>: {{ $athlete->alamat }}</td>
        </tr>
    </table>

    <div class="section-title">Parameter Evaluasi Fisik & Teknik Terbaru</div>
    @if($athlete->latestStat)
    <table class="data-table">
        <thead>
            <tr>
                <th colspan="4" style="text-align: center; background-color: #e5b922; color: #fff; font-size: 12px;">TEKNIK & AKURASI</th>
                <th colspan="4" style="text-align: center; background-color: #38bdf8; color: #fff; font-size: 12px;">KONDISI FISIK</th>
                <th colspan="3" style="text-align: center; background-color: #10b981; color: #fff; font-size: 12px;">MENTAL & DISIPLIN</th>
            </tr>
            <tr>
                <th>Tendangan</th>
                <th>Smash/Blok</th>
                <th>Akurasi</th>
                <th>Kecepatan</th>
                <th>Endurance</th>
                <th>Agility</th>
                <th>Flexibility</th>
                <th>Strength</th>
                <th>Disiplin</th>
                <th>Fokus</th>
                <th>Leadership</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $athlete->latestStat->tendangan }}</td>
                <td>{{ $athlete->latestStat->pukulan }}</td>
                <td>{{ $athlete->latestStat->akurasi }}</td>
                <td>{{ $athlete->latestStat->kecepatan }}</td>
                <td>{{ $athlete->latestStat->endurance }}</td>
                <td>{{ $athlete->latestStat->agility }}</td>
                <td>{{ $athlete->latestStat->flexibility }}</td>
                <td>{{ $athlete->latestStat->strength }}</td>
                <td>{{ $athlete->latestStat->disiplin }}</td>
                <td>{{ $athlete->latestStat->fokus }}</td>
                <td>{{ $athlete->latestStat->leadership }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p style="font-style: italic; color: #888;">Belum ada data evaluasi bulanan yang tercatat.</p>
    @endif

    <div class="section-title">Riwayat Medali & Prestasi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Kejuaraan / Turnamen</th>
                <th style="width: 15%;">Tingkat</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Hasil / Babak</th>
                <th style="width: 15%;">Medali</th>
            </tr>
        </thead>
        <tbody>
            @forelse($athlete->achievements as $index => $ach)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $ach->nama_kejuaraan }}</strong></td>
                <td>{{ $ach->tingkat }}</td>
                <td>{{ $ach->tanggal->format('d-m-Y') }}</td>
                <td>{{ $ach->hasil }}</td>
                <td><span style="font-weight: bold; color: {{ $ach->medali === 'Emas' ? '#b89111' : ($ach->medali === 'Perak' ? '#777' : '#c2410c') }}">{{ $ach->medali ?: 'Tanpa Medali' }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #888; font-style: italic;">Belum ada riwayat prestasi yang dicatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh PSTI Sport Analytics & Management System (PSAMS) Kota Bandung.
    </div>
</body>
</html>
