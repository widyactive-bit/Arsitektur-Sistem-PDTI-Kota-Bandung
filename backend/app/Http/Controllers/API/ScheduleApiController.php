<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Athlete;
use Illuminate\Http\Request;

class ScheduleApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/schedules",
     *     summary="Mendapatkan Daftar Jadwal Latihan Terdaftar",
     *     tags={"Jadwal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar Jadwal Latihan"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)->first();
        
        // Return schedules matching athlete's club or coach
        $query = Schedule::with(['coach', 'club']);

        if ($athlete) {
            $query->where('klub_id', function($q) use ($athlete) {
                $q->select('id')->from('clubs')->where('nama_klub', $athlete->klub)->limit(1);
            })->orWhere('pelatih_id', $athlete->pelatih_id);
        }

        $schedules = $query->orderBy('tanggal', 'asc')->get();

        return response()->json($schedules);
    }
}
