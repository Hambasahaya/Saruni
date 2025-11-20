<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use RuntimeException;

class JwtService
{
    public function __construct(
        private ?string $secret = null,
        private string $algorithm = 'HS256',
    ) {
        $this->secret ??= Config::get('app.jwt_secret', env('JWT_SECRET'));
    }

    public function generateToken(int|string $userId, string $role, int $expirySeconds = 3600): string
    {
        $secret = $this->ensureSecret();

        $now = Date::now();
        $expiresAt = (clone $now)->addSeconds($expirySeconds);

        $claims = [
            'user_id' => $userId,
            'role' => $role,
            'iat' => $now->getTimestamp(),
            'exp' => $expiresAt->getTimestamp(),
            'jti' => (string) Str::uuid(),
        ];

        return JWT::encode($claims, $secret, $this->algorithm);
    }

    /**
     * @return array{user_id:int|string,role:string,iat:int,exp:int,jti:string}
     */
    public function decode(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->ensureSecret(), $this->algorithm));

        /** @var array $claims */
        $claims = json_decode(json_encode($decoded), true, flags: JSON_THROW_ON_ERROR);

        return $claims;
    }

    private function ensureSecret(): string
    {
        if (empty($this->secret)) {
            throw new RuntimeException('JWT gada di env.');
        }

        return $this->secret;
    }
}
