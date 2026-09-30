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
    $marker = '/new/';
    $position = strpos($script, $marker);
    return $position === false ? '' : substr($script, 0, $position + strlen('/new'));
}

function url(string $path = ''): string
{
    return rtrim(appBasePath(), '/') . '/' . ltrim($path, '/');
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
