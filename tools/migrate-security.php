<?php
// Lehendik dagoen laborategiko BD-an bakarrik: lehenik bikoiztuak egiaztatu.
$webRoot = getenv('APP_WEB_ROOT') ?: __DIR__ . '/../erronka2_web';
require $webRoot . '/conf.php';
require $webRoot . '/includes/mysql.php';
$duplicates = mysqli_fetch_assoc(mysqli_query($conx, 'SELECT COUNT(*) AS count FROM (SELECT username FROM users GROUP BY username HAVING COUNT(*) > 1) AS duplicates'));
if ((int) $duplicates['count'] > 0) {
    fwrite(STDERR, "Erabiltzaile bikoiztuak daude; berrikusi eskuz migrazioa egin aurretik.\n");
    exit(1);
}
$indexes = mysqli_fetch_all(mysqli_query($conx, 'SHOW INDEX FROM users'), MYSQLI_ASSOC);
$unique = array_filter($indexes, static fn ($index) => $index['Key_name'] === 'users_username_unique');
if (!$unique) {
    mysqli_query($conx, 'ALTER TABLE users ADD UNIQUE KEY users_username_unique (username)');
}
echo "users.username indize bakarra prest.\n";
