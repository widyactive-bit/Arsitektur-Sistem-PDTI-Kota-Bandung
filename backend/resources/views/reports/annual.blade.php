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
        table.summary-table td { padding: 8px; text-align: center; border: 1px solid #ddd; }
        table.summary-table td.count { font-size: 20px; font-weight: bold; color: #b89111; }
        table.summary-table td.label { font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; }
        
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
        <p>Laporan Kinerja & Evaluasi Tahunan Periode Tahun {{ $year }} | Cetak: {{ $date }}</p>
    </div>

    <div class="section-title">Ringkasan Statistik Organisasi</div>
    <table class="summary-table">
        <tr>
            <td style="width: 25%;">
                <div class="count">{{ $athletes_count }}</div>
                <div class="label">Atlet Terdaftar</div>
            </td>
            <td style="width: 25%;">
                <div class="count">{{ $coaches_count }}</div>
                <div class="label">Pelatih Resmi</div>
            </td>
            <td style="width: 25%;">
                <div class="count">{{ $referees_count }}</div>
                <div class="label">Wasit Aktif</div>
            </td>
            <td style="width: 25%;">
                <div class="count">{{ $clubs_count }}</div>
                <div class="label">Klub Anggota</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Prestasi & Medali Sepanjang Tahun {{ $year }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Atlet</th>
                <th style="width: 35%;">Nama Kejuaraan / Turnamen</th>
                <th style="width: 15%;">Tingkat</th>
                <th style="width: 10%;">Medali</th>
                <th style="width: 10%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($achievements as $index => $ach)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $ach->athlete->nama_lengkap }}</strong></td>
                <td>{{ $ach->nama_kejuaraan }}</td>
                <td>{{ $ach->tingkat }}</td>
                <td><span style="font-weight: bold; color: {{ $ach->medali === 'Emas' ? '#b89111' : ($ach->medali === 'Perak' ? '#777' : '#c2410c') }}">{{ $ach->medali ?: 'Tanpa Medali' }}</span></td>
                <td>{{ $ach->tanggal->format('d-m-Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #888; font-style: italic;">Belum ada catatan medali/prestasi yang diraih sepanjang tahun ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Program Kerja & Evaluasi Humas</div>
    <p style="font-size: 11px; text-align: justify;">
        Berdasarkan data di atas, pembinaan olahraga Sepak Takraw di bawah PSTI Kota Bandung menunjukkan grafik perkembangan yang positif dengan total {{ $athletes_count }} atlet aktif dan {{ $clubs_count }} klub terdaftar. Program sertifikasi pelatih dan peningkatan level lisensi wasit (Level A/B dan Internasional) terus digalakkan guna menjamin standar kualitas kompetisi lokal di wilayah Bandung.
    </p>

    <div class="footer">
        Dokumen Laporan Resmi Persatuan Sepak Takraw Indonesia (PSTI) Pengurus Cabang Kota Bandung.
    </div>
</body>
</html>
