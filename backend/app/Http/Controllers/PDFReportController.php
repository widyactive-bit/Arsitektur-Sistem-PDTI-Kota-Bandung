<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Club;
use App\Models\Referee;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFReportController extends Controller
{
    /**
     * Export Laporan Profil & Analisis Atlet
     */
    public function downloadAthleteReport($id)
    {
        $athlete = Athlete::with(['coach', 'achievements', 'latestStat'])->findOrFail($id);
        $score = $athlete->calculateRankingScore();

        $data = [
            'title' => 'LAPORAN ANALITIS ATLET SEPAK TAKRAW',
            'date' => date('d-m-Y'),
            'athlete' => $athlete,
            'ranking_score' => round($score, 2)
        ];

        $html = view('reports.athlete', $data)->render();
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->download("laporan_atlet_{$athlete->nomor_induk_atlet}.pdf");
    }

    /**
     * Export Laporan Profil Pelatih & Atlet Binaan
     */
    public function downloadCoachReport($id)
    {
        $coach = Coach::with('athletes')->findOrFail($id);

        $data = [
            'title' => 'LAPORAN PROFIL & DATA BINAAN PELATIH',
            'date' => date('d-m-Y'),
            'coach' => $coach
        ];

        $html = view('reports.coach', $data)->render();
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->download("laporan_pelatih_{$coach->id}.pdf");
    }

    /**
     * Export Laporan Klub Takraw
     */
    public function downloadClubReport($id)
    {
        $club = Club::findOrFail($id);
        
        // Find athletes in this club
        $athletes = Athlete::where('klub', $club->nama_klub)->get();

        $data = [
            'title' => 'LAPORAN PROFIL & DAFTAR ANGGOTA KLUB',
            'date' => date('d-m-Y'),
            'club' => $club,
            'athletes' => $athletes
        ];

        $html = view('reports.club', $data)->render();
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->download("laporan_klub_{$club->id}.pdf");
    }

    /**
     * Export Laporan Tahunan PSTI Kota Bandung
     */
    public function downloadPstiAnnualReport()
    {
        $athletesCount = Athlete::count();
        $coachesCount = Coach::count();
        $refereesCount = Referee::count();
        $clubsCount = Club::count();
        $achievements = Achievement::with('athlete')->orderBy('tanggal', 'desc')->get();

        $data = [
            'title' => 'LAPORAN TAHUNAN & EVALUASI PRESTASI PSTI KOTA BANDUNG',
            'year' => date('Y'),
            'date' => date('d-m-Y'),
            'athletes_count' => $athletesCount,
            'coaches_count' => $coachesCount,
            'referees_count' => $refereesCount,
            'clubs_count' => $clubsCount,
            'achievements' => $achievements
        ];

        $html = view('reports.annual', $data)->render();
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->download("laporan_tahunan_psti_bandung_" . date('Y') . ".pdf");
    }
}
