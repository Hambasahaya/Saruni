<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $claims = $request->attributes->get('jwt_claims');

        if (empty($claims) || !is_array($claims)) {
            return $this->forbidden('akses ditolak: user tidak ditemukan di context');
        }

        $userRole = $claims['role'] ?? $request->attributes->get('role');

        if (!is_string($userRole) || $userRole === '') {
            return $this->forbidden('akses ditolak: role tidak tersedia');
        }

        if (empty($allowedRoles)) {
            return $next($request);
        }

        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        $allowedRolesStr = implode(', ', $allowedRoles);

        return $this->forbidden("akses ditolak: role Anda ('{$userRole}') tidak diizinkan. Role yang diizinkan adalah: {$allowedRolesStr}");
    }

    private function forbidden(string $message): Response
    {
        return response()->json([
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }
}
