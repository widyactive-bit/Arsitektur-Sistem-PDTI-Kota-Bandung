<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AthleteStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AthleteApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/athlete/profile",
     *     summary="Mendapatkan Informasi Profil & Biodata Atlet Aktif",
     *     tags={"Atlet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profil Biodata Atlet"
     *     )
     * )
     */
    public function profile(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)
            ->with(['coach', 'latestStat'])
            ->first();

        if (!$athlete) {
            return response()->json(['message' => 'Profil atlet tidak ditemukan.'], 404);
        }

        // Include calculate score dynamically
        $rankingScore = $athlete->calculateRankingScore();
        
        return response()->json([
            'athlete' => $athlete,
            'ranking_score' => round($rankingScore, 2)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/athlete/stats",
     *     summary="Mendapatkan Histori Statistik Bulanan Atlet",
     *     tags={"Atlet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar Histori Parameter Fisik, Teknik & Mental"
     *     )
     * )
     */
    public function stats(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)->first();
        if (!$athlete) {
            return response()->json([]);
        }

        $stats = AthleteStat::where('athlete_id', $athlete->id)
            ->orderBy('record_date', 'asc')
            ->get();

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/athlete/achievements",
     *     summary="Mendapatkan Riwayat Medali & Prestasi Atlet",
     *     tags={"Atlet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar Prestasi & Medali"
     *     )
     * )
     */
    public function achievements(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)->first();
        if (!$athlete) {
            return response()->json([]);
        }

        $achievements = $athlete->achievements()->orderBy('tanggal', 'desc')->get();

        return response()->json($achievements);
    }

    /**
     * @OA\Post(
     *     path="/athlete/ai-analytics",
     *     summary="Mengenerate Rekomendasi Latihan AI Berbasis OpenAI GPT",
     *     tags={"Atlet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Narasi Analisis AI Hasil Umpan Balik FastAPI"
     *     )
     * )
     */
    public function getAiAnalytics(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)->with('latestStat')->first();
        if (!$athlete || !$athlete->latestStat) {
            return response()->json(['message' => 'Data statistik evaluasi bulanan atlet belum tersedia.'], 400);
        }

        $latest = $athlete->latestStat;
        $prestasiCount = $athlete->achievements()->count();

        // Prepare request body for FastAPI AI Service
        $payload = [
            'nama' => $athlete->nama_lengkap,
            'nomor_induk_atlet' => $athlete->nomor_induk_atlet,
            'klub' => $athlete->klub,
            'posisi' => $athlete->kelas_tanding,
            'prestasi_count' => $prestasiCount,
            'stats' => [
                'tendangan' => (float)$latest->tendangan,
                'pukulan' => (float)$latest->pukulan,
                'akurasi' => (float)$latest->akurasi,
                'kecepatan' => (float)$latest->kecepatan,
                'endurance' => (float)$latest->endurance,
                'agility' => (float)$latest->agility,
                'flexibility' => (float)$latest->flexibility,
                'strength' => (float)$latest->strength,
                'disiplin' => (float)$latest->disiplin,
                'fokus' => (float)$latest->fokus,
                'leadership' => (float)$latest->leadership
            ]
        ];

        try {
            // URL of ai_service container inside the docker network
            // Falls back to localhost if running outside docker environment
            $aiServiceUrl = env('AI_SERVICE_URL', 'http://ai_service:8000/analyze');
            
            $response = Http::timeout(10)->post($aiServiceUrl, $payload);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'message' => 'Layanan AI Analytics mengembalikan respon kesalahan.',
                'error' => $response->body()
            ], 502);
            
        } catch (\Exception $e) {
            // Internal fallback: call local mock rules engine logic if FastAPI is down/unreachable
            return response()->json([
                'message' => 'Gagal menghubungi FastAPI AI Service (Offline/Timeout). Mengaktifkan modul fallback lokal.',
                'total_score' => round($athlete->calculateRankingScore(), 2),
                'engine' => 'PSAMS Laravel Local Fallback',
                'analysis' => $this->getLocalFallbackAnalysis($athlete, $latest, $prestasiCount)
            ]);
        }
    }

    private function getLocalFallbackAnalysis($athlete, $latest, $prestasiCount)
    {
        $score = $athlete->calculateRankingScore();
        $kelebihan = [];
        $kekurangan = [];
        
        if ($latest->flexibility > 80) $kelebihan[] = "- Fleksibilitas tubuh tinggi mendukung gerakan hadangan/smash salto.";
        if ($latest->tendangan > 80) $kelebihan[] = "- Akurasi umpan dan sepak kura yang konsisten.";
        if (empty($kelebihan)) $kelebihan[] = "- Performa teknik dasar cukup berimbang.";

        if ($latest->endurance < 75) $kekurangan[] = "- Ketahanan stamina menurun saat durasi latihan melebihi 90 menit.";
        if ($latest->fokus < 75) $kekurangan[] = "- Fokus bermain menurun ketika tertekan skor regu lawan.";
        if (empty($kekurangan)) $kekurangan[] = "- Konsistensi bertanding yang masih perlu ditingkatkan.";

        $rekomendasi = "Rekomendasi Latihan: Lakukan latihan kardiovaskular interval sprint 3x seminggu dan latihan beban kaki (leg press) untuk meningkatkan lompatan.";
        $prediksi = $score > 75 
            ? "Prediksi: Berpotensi besar mendapatkan medali pada turnamen Kejurda Jabar." 
            : "Prediksi: Membutuhkan peningkatan teknik sebelum masuk skuad utama.";

        return "1. KELEBIHAN:\n" . implode("\n", $kelebihan) . "\n\n2. KEKURANGAN:\n" . implode("\n", $kekurangan) . "\n\n3. REKOMENDASI:\n" . $rekomendasi . "\n\n4. PREDIKSI:\n" . $prediksi;
    }
}
