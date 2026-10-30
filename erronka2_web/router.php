<?php
// PHP zerbitzari integraturako; Apache-k docker/apache.conf erabiltzen du.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if (str_contains($path, "\0") || str_contains($path, '..')
    || str_starts_with($path, '/includes/')
    || in_array($path, ['/conf.php', '/router.php'], true)
    || (str_ends_with(strtolower($path), '.php') && $path !== '/index.php')) {
    http_response_code(404);
    exit;
}
if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}
return false;
