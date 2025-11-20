<?php

namespace App\Http\Middleware;

use App\Models\Session;
use App\Services\JwtService;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use RuntimeException;
use Throwable;

class JwtAuthenticate
{
    public function __construct(private JwtService $jwtService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return $this->error('authorization header tidak ditemukan', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $claims = $this->jwtService->decode($token);
        } catch (ExpiredException) {
            return $this->error('token telah kedaluwarsa', Response::HTTP_UNAUTHORIZED);
        } catch (RuntimeException) {
            return $this->error('jwt secret tidak tersedia', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable) {
            return $this->error('token tidak valid', Response::HTTP_UNAUTHORIZED);
        }

        $session = Session::query()
            ->where('token', $token)
            ->first();

        if ($session === null || $session->trashed()) {
            return $this->error('sesi login tidak ditemukan atau sudah logout', Response::HTTP_UNAUTHORIZED);
        }

        $request->attributes->set('jwt_claims', $claims);
        $request->attributes->set('token', $token);
        $request->attributes->set('session', $session);
        $request->attributes->set('user_id', $session->user_id);
        $request->attributes->set('role', $session->role);

        return $next($request);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}
