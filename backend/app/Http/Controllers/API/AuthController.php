<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="PSAMS REST API Documentation",
 *     version="1.0.0",
 *     description="Dokumentasi API untuk PSTI Sport Analytics & Management System (PSAMS) Kota Bandung"
 * )
 * @OA\Server(
 *     url="/api",
 *     description="API Base URL"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
      *     path="/auth/register",
      *     summary="Registrasi Pengguna Baru",
      *     tags={"Autentikasi"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"name","email","password","role"},
      *             @OA\Property(property="name", type="string", example="Muhammad Rafli"),
      *             @OA\Property(property="email", type="string", format="email", example="rafli@psti.bandung.go.id"),
      *             @OA\Property(property="password", type="string", format="password", example="password123"),
      *             @OA\Property(property="role", type="string", enum={"Super Admin", "Pengurus", "Pelatih", "Atlet", "Wasit"}, example="Atlet")
      *         )
      *     ),
      *     @OA\Response(
      *         response=201,
      *         description="Registrasi Berhasil",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="User registered successfully"),
      *             @OA\Property(property="token", type="string", example="1|token_hash_here")
      *         )
      *     )
      * )
      */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Super Admin,Pengurus,Pelatih,Atlet,Wasit',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Post(
      *     path="/auth/login",
      *     summary="Masuk ke Sistem (Mendapatkan Token Sanctum)",
      *     tags={"Autentikasi"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"email","password"},
      *             @OA\Property(property="email", type="string", format="email", example="atlet@psti.bandung.go.id"),
      *             @OA\Property(property="password", type="string", format="password", example="password123")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Login Berhasil",
      *         @OA\JsonContent(
      *             @OA\Property(property="token", type="string", example="1|token_hash_here"),
      *             @OA\Property(property="user", type="object")
      *         )
      *     ),
      *     @OA\Response(
      *         response=422,
      *         description="Validasi Gagal / Password Salah"
      *     )
      * )
      */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
      *     path="/auth/logout",
      *     summary="Keluar dari Sistem (Menghapus Token)",
      *     tags={"Autentikasi"},
      *     security={{"bearerAuth":{}}},
      *     @OA\Response(
      *         response=200,
      *         description="Logout Berhasil",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Logged out successfully")
      *         )
      *     )
      * )
      */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * @OA\Get(
      *     path="/auth/profile",
      *     summary="Mendapatkan Informasi Profil Pengguna Aktif",
      *     tags={"Autentikasi"},
      *     security={{"bearerAuth":{}}},
      *     @OA\Response(
      *         response=200,
      *         description="Profil Pengguna",
      *         @OA\JsonContent(
      *             @OA\Property(property="id", type="integer", example=1),
      *             @OA\Property(property="name", type="string", example="Muhammad Rafli"),
      *             @OA\Property(property="email", type="string", example="atlet@psti.bandung.go.id"),
      *             @OA\Property(property="role", type="string", example="Atlet")
      *         )
      *     )
      * )
      */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
