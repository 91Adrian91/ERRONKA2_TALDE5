<?php

final class ImageUploadException extends RuntimeException
{
}

function htmlText($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2097152) {
        throw new ImageUploadException('Irudia ezin izan da igo (gehienez 2 MB).');
    }
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($types[$mime])) {
        throw new ImageUploadException('Irudi-formatua ez da onartzen.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $types[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
        throw new ImageUploadException('Irudia ezin izan da gorde.');
    }
    return $name;
}
