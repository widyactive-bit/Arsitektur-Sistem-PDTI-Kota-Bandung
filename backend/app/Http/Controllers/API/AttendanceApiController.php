<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/attendance/checkin",
     *     summary="Absensi Masuk Latihan (Check In)",
     *     tags={"Absensi"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"latitude","longitude","selfie"},
     *             @OA\Property(property="latitude", type="number", format="double", example=-6.90389),
     *             @OA\Property(property="longitude", type="number", format="double", example=107.61861),
     *             @OA\Property(property="selfie", type="string", description="Base64 image or file path", example="selfie_mock_1.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Check In Berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Check In successful"),
     *             @OA\Property(property="record", type="object")
     *         )
     *     )
     * )
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|string',
        ]);

        // Find associated athlete by user email
        $athlete = Athlete::where('email', $request->user()->email)->first();
        if (!$athlete) {
            return response()->json(['message' => 'Atlet tidak terdaftar di sistem database.'], 404);
        }

        // Check if already checked in and not checked out
        $existing = Attendance::where('athlete_id', $athlete->id)
            ->whereNull('checkout_time')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah melakukan check-in sebelumnya.',
                'record' => $existing
            ], 400);
        }

        $record = Attendance::create([
            'athlete_id' => $athlete->id,
            'checkin_time' => Carbon::now(),
            'checkout_time' => null,
            'duration' => null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'selfie' => $request->selfie,
        ]);

        return response()->json([
            'message' => 'Check In berhasil dicatat!',
            'record' => $record
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/attendance/checkout",
     *     summary="Absensi Keluar Latihan (Check Out & Hitung Durasi)",
     *     tags={"Absensi"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"latitude","longitude","selfie"},
     *             @OA\Property(property="latitude", type="number", format="double", example=-6.90389),
     *             @OA\Property(property="longitude", type="number", format="double", example=107.61861),
     *             @OA\Property(property="selfie", type="string", description="Base64 image or file path", example="selfie_checkout_mock.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Check Out Berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Check Out successful"),
     *             @OA\Property(property="duration_minutes", type="integer", example=150),
     *             @OA\Property(property="record", type="object")
     *         )
     *     )
     * )
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|string',
        ]);

        $athlete = Athlete::where('email', $request->user()->email)->first();
        if (!$athlete) {
            return response()->json(['message' => 'Atlet tidak terdaftar.'], 404);
        }

        // Fetch latest active check-in record
        $record = Attendance::where('athlete_id', $athlete->id)
            ->whereNull('checkout_time')
            ->latest('checkin_time')
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Gagal check-out. Anda belum melakukan check-in hari ini.'
            ], 400);
        }

        $checkoutTime = Carbon::now();
        $checkinTime = Carbon::parse($record->checkin_time);
        
        // Calculate duration in minutes
        $duration = $checkinTime->diffInMinutes($checkoutTime);

        $record->update([
            'checkout_time' => $checkoutTime,
            'duration' => $duration,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'selfie' => $request->selfie // Optional update or keep checkout photo
        ]);

        return response()->json([
            'message' => 'Check Out berhasil dicatat!',
            'duration_minutes' => $duration,
            'record' => $record
        ]);
    }

    /**
     * @OA\Get(
     *     path="/attendance/history",
     *     summary="Mendapatkan Riwayat Kehadiran Latihan",
     *     tags={"Absensi"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar Riwayat Kehadiran",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function history(Request $request)
    {
        $athlete = Athlete::where('email', $request->user()->email)->first();
        if (!$athlete) {
            return response()->json([]);
        }

        $history = Attendance::where('athlete_id', $athlete->id)
            ->orderBy('checkin_time', 'desc')
            ->get();

        return response()->json($history);
    }
}
