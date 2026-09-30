<?php

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}

if (!function_exists('loadEnvironment')) {
    function loadEnvironment(string $file): void
    {
        if (!is_file($file) || !is_readable($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if ($key === '' || getenv($key) !== false) {
                continue;
            }

            if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (!function_exists('appFilesystemPath')) {
    function appFilesystemPath(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);
        $root = str_replace('\\', '/', APP_ROOT);
        if (str_starts_with($normalized, $root)) {
            return $path;
        }
        return APP_ROOT . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
    }
}

loadEnvironment(APP_ROOT . DIRECTORY_SEPARATOR . '.env');

date_default_timezone_set((string) env('APP_TIMEZONE', 'America/Mexico_City'));
ini_set('display_errors', env('APP_DEBUG', false) ? '1' : '0');
ini_set('log_errors', '1');

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

