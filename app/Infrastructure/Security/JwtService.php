<?php

namespace App\Infrastructure\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService
{
    private string $secret;
    private string $algorithm;
    private int $expiration;

    public function __construct(string $secret, string $algorithm, int $expiration)
    {
        $this->secret = $secret;
        $this->algorithm = $algorithm;
        $this->expiration = $expiration;
    }

    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expiration;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire
        ]);

        return JWT::encode($tokenPayload, $this->secret, $this->algorithm);
    }

    public function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (Exception $e) {
            return null;
        }
    }
}
