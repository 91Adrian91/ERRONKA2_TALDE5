<?php

// Zuzeneko HTTP sarbidea ez da aplikazioaren sarrera-bidea.
if (PHP_SAPI !== 'cli' && !defined('ARRUE_ENTRY')) {
    http_response_code(404);
    exit;
}

final class RequestException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 400)
    {
        parent::__construct($message);
    }
}

final class ImageUploadException extends RuntimeException
{
}

function htmlText($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function inputText(array $values, string $key, string $default = ''): string
{
    $value = $values[$key] ?? $default;
    if (!is_string($value)) {
        throw new RequestException('Sarreraren formatua ez da zuzena.');
    }
    return $value;
}

function validPassword(string $password): bool
{
    // Bcrypt-ek 72 byte baino gehiago mozten ditu; ez onartu mozketa isilik.
    return strlen($password) >= 8 && strlen($password) <= 72;
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlText(csrfToken()) . '">';
}

function verifyCsrf(array $input): void
{
    $token = inputText($input, 'csrf_token');
    if ($token === '' || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        throw new RequestException('Eskaera ez da baliozkoa. Kargatu orria berriro.', 403);
    }
}

function sessionExpired(array $session, int $now, int $idleSeconds, int $absoluteSeconds): bool
{
    return !isset($session['created_at'], $session['last_seen'])
        || $now - $session['last_seen'] >= $idleSeconds
        || $now - $session['created_at'] >= $absoluteSeconds;
}

function destroyLoginSession(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $cookie['path'],
            'secure' => $cookie['secure'], 'httponly' => true, 'samesite' => 'Lax',
        ]);
        session_destroy();
    }
}

function requireUniqueUser(mysqli $connection, string $username): void
{
    $statement = mysqli_prepare($connection, 'SELECT id FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 's', $username);
    mysqli_stmt_execute($statement);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if ($exists) {
        throw new RequestException('Kontua ezin izan da sortu datu horiekin.', 409);
    }
}

function signedIn(): bool
{
    return isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
}

function administrator(): bool
{
    return signedIn() && ($_SESSION['username'] ?? '') === 'admin@bdweb';
}

function redirectPage(string $page): void
{
    header('Location: ' . $page);
    exit;
}

function requireAccount(bool $adminOnly = false): void
{
    if (!signedIn() || ($adminOnly && !administrator())) {
        redirectPage('index.php');
    }
}

function imageUpload(string $field, string $directory): string
{
    $file = $_FILES[$field] ?? null;
    if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if (!is_array($file) || !isset($file['error'], $file['size'], $file['tmp_name'])
        || !is_int($file['error']) || $file['error'] !== UPLOAD_ERR_OK
        || $file['size'] > 2097152 || !is_string($file['tmp_name'])
        || !is_uploaded_file($file['tmp_name'])) {
        throw new ImageUploadException('Irudia ezin izan da igo (gehienez 2 MB).');
    }
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $dimensions = @getimagesize($file['tmp_name']);
    if (!isset($types[$mime]) || $dimensions === false
        || $dimensions[0] > 10000 || $dimensions[1] > 10000
        || $dimensions[0] * $dimensions[1] > 25000000) {
        throw new ImageUploadException('Irudi-formatua ez da onartzen.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $types[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
        throw new ImageUploadException('Irudia ezin izan da gorde.');
    }
    return $name;
}
