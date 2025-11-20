<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AdminRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\GuruRole;
use App\Models\Session;
use App\Models\Siswa;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwtService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];

        if ($admin = Admin::where('email', $email)->first()) {
            return $this->attemptAuthentication($admin->id, $password, $admin->password, 'admin');
        }

        if ($guru = Guru::where('email', $email)->first()) {
            $role = GuruRole::where('guru_id', $guru->id)
                ->where('role', 'wali_kelas')
                ->exists() ? 'wali_kelas' : 'guru';

            return $this->attemptAuthentication($guru->id, $password, $guru->password, $role);
        }

        if ($siswa = Siswa::where('email', $email)->first()) {
            return $this->attemptAuthentication($siswa->id, $password, $siswa->password, 'siswa');
        }

        return $this->error('email tidak ditemukan', Response::HTTP_UNAUTHORIZED);
    }

    public function registerAdmin(AdminRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $admin = Admin::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'registrasi admin berhasil',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'nama' => $admin->nama,
                    'email' => $admin->email,
                ],
            ],
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return $this->error('token tidak ditemukan', Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        if ($userId === null) {
            return $this->error('user_id tidak ditemukan', Response::HTTP_UNAUTHORIZED);
        }

        if ($role === null) {
            return $this->error('role tidak ditemukan', Response::HTTP_UNAUTHORIZED);
        }

        Session::withTrashed()->where('token', $token)->forceDelete();

        return response()->json([
            'message' => 'logout berhasil',
        ], Response::HTTP_OK);
    }

    private function attemptAuthentication(int $userId, string $plainPassword, string $hashedPassword, string $role): JsonResponse
    {
        if (!Hash::check($plainPassword, $hashedPassword)) {
            return $this->error('password salah', Response::HTTP_UNAUTHORIZED);
        }

        return $this->issueTokenResponse($userId, $role, Response::HTTP_OK, 'login berhasil');
    }

    private function issueTokenResponse(
        int $userId,
        string $role,
        int $status,
        string $message,
        array $extraData = []
    ): JsonResponse {
        try {
            $token = $this->jwtService->generateToken($userId, $role);
        } catch (Throwable $exception) {
            return $this->error('gagal membuat token', Response::HTTP_INTERNAL_SERVER_ERROR, $exception);
        }

        Session::create([
            'user_id' => $userId,
            'token' => $token,
            'role' => $role,
        ]);

        return response()->json([
            'message' => $message,
            'data' => array_merge([
                'token' => $token,
                'role' => $role,
            ], $extraData),
        ], $status);
    }

    private function error(string $message, int $status, ?Throwable $exception = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => $exception?->getMessage(),
        ], $status);
    }
}
