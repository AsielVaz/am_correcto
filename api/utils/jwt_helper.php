<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

final class JwtHelper
{
    public static function generar(array $payload, ?int $ttl = null): string
    {
        $now = time();
        $expiresAt = $now + ($ttl ?? (int) env('JWT_TTL', 3600));
        $claims = array_merge($payload, [
            'iss' => (string) env('APP_URL', 'am'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $expiresAt,
            'expire' => $expiresAt,
            'jti' => bin2hex(random_bytes(16)),
        ]);

        $header = self::encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $body = self::encode(json_encode($claims, JSON_THROW_ON_ERROR));
        $signature = self::encode(hash_hmac('sha256', $header . '.' . $body, self::secret(), true));
        return $header . '.' . $body . '.' . $signature;
    }

    public static function validar(string $jwt): array|false
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $signature] = $parts;
        $expected = self::encode(hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, self::secret(), true));
        if (!hash_equals($expected, $signature)) {
            return false;
        }

        try {
            $header = json_decode(self::decode($encodedHeader), true, 8, JSON_THROW_ON_ERROR);
            $payload = json_decode(self::decode($encodedPayload), true, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }

        $expiresAt = (int) ($payload['exp'] ?? $payload['expire'] ?? 0);
        if (($header['alg'] ?? null) !== 'HS256' || $expiresAt <= time() || (int) ($payload['nbf'] ?? 0) > time()) {
            return false;
        }

        return $payload;
    }

    private static function secret(): string
    {
        $secret = (string) env('JWT_SECRET', '');
        if (strlen($secret) < 32) {
            throw new RuntimeException('JWT_SECRET debe tener al menos 32 caracteres.');
        }
        return $secret;
    }

    private static function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function decode(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding !== 0) {
            $value .= str_repeat('=', 4 - $padding);
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if ($decoded === false) {
            throw new JsonException('JWT malformado');
        }
        return $decoded;
    }
}
