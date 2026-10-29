<?php
require_once __DIR__ . '/helpers.php';

set_exception_handler(static function (Throwable $error): void {
    $status = $error instanceof RequestException ? $error->status : 500;
    if ($error instanceof ImageUploadException) {
        $status = 400;
    } elseif ($error instanceof mysqli_sql_exception) {
        $status = $error->getCode() === 1062 ? 409 : 503;
    }
    if (ob_get_level() > 0) {
        ob_clean();
    }
    http_response_code($status);
    error_log('Arrue request rejected: ' . get_class($error) . ' status=' . $status);
    $message = $error instanceof RequestException || $error instanceof ImageUploadException
        ? $error->getMessage() : 'Eskaera ezin izan da osatu. Saiatu berriro.';
    echo '<!doctype html><html lang="eu"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>Arrue · Eskaera</title><link rel="stylesheet" href="style.css?v=arrue-refine-3">'
        . '<main class="compact-form"><h1>Eskaera ezin izan da osatu</h1><p>' . htmlText($message)
        . '</p><a href="index.php">Itzuli hasierara</a></main></html>';
});

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
header('Cache-Control: no-store');

ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_name('ARRUESESSID');
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
// Ez fidatu bezeroak bidalitako X-Forwarded-Proto goiburuaz.
$secureCookie = $https || getenv('SESSION_COOKIE_SECURE') === '1';
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $secureCookie, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

if (isset($_SESSION['admin']) && sessionExpired($_SESSION, time(),
    max(1, (int) (getenv('SESSION_IDLE_SECONDS') ?: 1800)),
    max(1, (int) (getenv('SESSION_ABSOLUTE_SECONDS') ?: 28800)))) {
    destroyLoginSession();
    redirectPage('index.php?action=login');
}
if (isset($_SESSION['admin'])) {
    $_SESSION['last_seen'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST);
} elseif (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
    header('Allow: GET, HEAD, POST');
    throw new RequestException('Metodoa ez da onartzen.', 405);
}
