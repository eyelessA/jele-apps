<?php

namespace App\Services\User;

class JWTService
{
    public string $secret;
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generateJWT(array $payload): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        $base64Header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64Payload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);

        $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public function verifyJWT(string $token): array|bool
    {

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $header = json_decode(base64_decode(strtr($base64Header, '-_', '+/')), true);
        $payload = json_decode(base64_decode(strtr($base64Payload, '-_', '+/')), true);

        if (!$header || !$payload) {
            return false;
        }

        $validSignature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        $decodedSignature = base64_decode(strtr($base64Signature, '-_', '+/'));

        if (!hash_equals($validSignature, $decodedSignature)) {
            return false;
        }

        return $payload;
    }
}
