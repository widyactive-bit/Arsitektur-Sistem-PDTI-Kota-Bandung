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
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PSTI KOTA BANDUNG</h1>
        <h2>{{ $title }}</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <div class="section-title">Profil Klub</div>
    <table class="profile-table">
        <tr>
            <td class="label">Nama Klub</td>
            <td>: {{ $club->nama_klub }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Sekretariat</td>
            <td>: {{ $club->alamat }}</td>
        </tr>
        <tr>
            <td class="label">Pelatih Kepala</td>
            <td>: {{ $club->pelatih }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Anggota Aktif</td>
            <td>: {{ $club->jumlah_atlet }} Atlet</td>
        </tr>
    </table>

    <div class="section-title">Daftar Atlet Terdaftar dari Klub Ini</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nomor Induk (NIA)</th>
                <th style="width: 45%;">Nama Atlet</th>
                <th style="width: 25%;">Posisi / Kelas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($athletes as $index => $ath)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ath->nomor_induk_atlet }}</td>
                <td><strong>{{ $ath->nama_lengkap }}</strong></td>
                <td>{{ $ath->kelas_tanding }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #888; font-style: italic;">Belum ada atlet takraw dari klub ini yang terdaftar di database utama PSTI.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh PSTI Sport Analytics & Management System (PSAMS) Kota Bandung.
    </div>
</body>
</html>
