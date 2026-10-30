<?php

// Laborategiko bi kontuen migrazioa bakarrik; lehenetsita ez du ezer aldatzen.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$webRoot = getenv('APP_WEB_ROOT') ?: __DIR__ . '/../erronka2_web';
require_once $webRoot . '/conf.php';
$connection = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);
$legacyHash = '5f4dcc3b5aa765d61d8327deb882cf99';
$users = mysqli_fetch_all(mysqli_query($connection, 'SELECT id, password FROM users'), MYSQLI_ASSOC);
$pending = array_filter($users, static fn ($user) => $user['password'] === $legacyHash);
if (($argv[1] ?? '') !== '--apply') {
    echo count($pending) . " laborategiko kontu migratzeko. Aldatzeko: --apply\n";
    exit;
}
foreach ($users as $user) {
    if (strlen($user['password']) === 32 && $user['password'] !== $legacyHash) {
        throw new UnexpectedValueException('Kontu ezezagunen pasahitzak banaka berrezarri behar dira.');
    }
}
mysqli_query($connection, 'ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
mysqli_begin_transaction($connection);
try {
    foreach ($pending as $user) {
        $hash = password_hash('password', PASSWORD_DEFAULT);
        $statement = mysqli_prepare($connection, 'UPDATE users SET password = ? WHERE id = ? AND password = ?');
        mysqli_stmt_bind_param($statement, 'sis', $hash, $user['id'], $legacyHash);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    mysqli_commit($connection);
} catch (Throwable $error) {
    mysqli_rollback($connection);
    throw $error;
}
echo count($pending) . " laborategiko kontu migratuta.\n";
