<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

function appBasePath(): string
{
    $configuredUrl = (string) env('APP_URL', '');
    $path = $configuredUrl !== '' ? (string) parse_url($configuredUrl, PHP_URL_PATH) : '';
    if ($path !== '') {
        return '/' . trim($path, '/');
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
	foreach (['/pages/', '/api/', '/components/'] as $marker) {
		$position = strpos($script, $marker);
		if ($position !== false) {
			return rtrim(substr($script, 0, $position), '/');
		}
	}

	$directory = str_replace('\\', '/', dirname($script));
	return $directory === '/' || $directory === '.' ? '' : rtrim($directory, '/');
}

function url(string $path = ''): string
{
    return rtrim(appBasePath(), '/') . '/' . ltrim($path, '/');
}

function absoluteUrl(string $path = ''): string
{
	$configuredUrl = trim((string) env('APP_URL', ''));
	if ($configuredUrl !== '') {
		return rtrim($configuredUrl, '/') . '/' . ltrim($path, '/');
	}

	$https = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
	$scheme = $https ? 'https' : 'http';
	$host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
	return $scheme . '://' . $host . url($path);
}

function assetsUrl(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function pagesUrl(string $path): string
{
    return url('pages/' . ltrim($path, '/'));
}

function componentsUrl(string $path): string
{
    return url('components/' . ltrim($path, '/'));
}

function scriptsUrl(string $path): string
{
    return url('scripts/' . ltrim($path, '/'));
}
