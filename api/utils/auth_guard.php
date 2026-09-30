<?php
require_once __DIR__ . '/jwt_helper.php';

define('ROLE_USUARIO', 'Usuario');
define('ROLE_CAPTURISTA', 'Capturista');
define('ROLE_ADMIN', 'Admin');

/**
 * Obtiene y valida el token JWT de la petición actual.
 * Devuelve el payload del token cuando es válido; en caso contrario termina la ejecución con el código HTTP adecuado.
 */
function requireAuth(): array
{
    static $cachedPayload = null;
    if ($cachedPayload !== null) {
        return $cachedPayload;
    }

    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authHeader = null;
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }

    $token = null;
    if ($authHeader && stripos($authHeader, 'Bearer ') === 0) {
        $token = trim(substr($authHeader, 7));
    }

    if (!$token && isset($_POST['token'])) {
        $token = $_POST['token'];
    }

    if (!$token) {
        respondWithError(401, 'Token ausente');
    }

    $payload = JwtHelper::validar($token);
    if (!$payload) {
        respondWithError(401, 'Token inválido o expirado');
    }

    $cachedPayload = $payload;
    return $cachedPayload;
}

function currentUserRole(): string
{
    $payload = requireAuth();
    return isset($payload['rol']) && is_string($payload['rol']) && $payload['rol'] !== ''
        ? $payload['rol']
        : ROLE_USUARIO;
}

function canCurrentUserWrite(): bool
{
    $role = currentUserRole();
    return $role === ROLE_CAPTURISTA || $role === ROLE_ADMIN;
}

function ensureRole(array $allowedRoles): void
{
    $role = currentUserRole();
    if (!in_array($role, $allowedRoles, true)) {
        respondWithError(403, 'No tienes permisos para realizar esta acción');
    }
}

function ensureCanWrite(): void
{
    if (!canCurrentUserWrite()) {
        respondWithError(403, 'Este usuario solo cuenta con permisos de lectura');
    }
}

function ensureAdmin(): void
{
    ensureRole([ROLE_ADMIN]);
}

function respondWithError(int $statusCode, string $message): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
