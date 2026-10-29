<?php

define('ARRUE_ENTRY', true);
ob_start();
require_once __DIR__ . '/includes/security.php';

require_once __DIR__ . '/conf.php';
require_once __DIR__ . '/includes/mysql.php';

function redirectToHome(): void
{
    header('Location: index.php');
    exit;
}

$action = $_GET['action'] ?? '';
if (!is_string($action)) {
    $action = '';
}

// Kontu ezabatuen edo pasahitz zaharreko saioak ez dira baliozkoak.
if (signedIn()) {
    $sessionUser = inputText($_SESSION, 'username');
    $statement = mysqli_prepare($conx, 'SELECT password FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 's', $sessionUser);
    mysqli_stmt_execute($statement);
    $currentAccount = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if (!$currentAccount || !hash_equals(inputText($_SESSION, 'credential_version'), hash('sha256', $currentAccount['password']))) {
        destroyLoginSession();
        redirectPage('index.php?action=login');
    }
}
$signedIn = signedIn();
$administrator = $signedIn && ($_SESSION['username'] ?? '') === 'admin@bdweb';

if ((in_array($action, ['updel', 'description'], true) && !$administrator) || ($action === 'account' && !$signedIn)) {
    redirectToHome();
}
if ($action === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        throw new RequestException('Saioa ixteko POST eskaera behar da.', 405);
    }
    destroyLoginSession();
    redirectToHome();
}

?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Arrue Hortz Sintetikoak: hortz sintetikoen diseinua, fabrikazioa eta banaketa, kliniken 3D diseinuetatik abiatuta.">
    <title><?php echo htmlspecialchars($enpresa, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="style.css?v=arrue-refine-3">
</head>
<body>
<header class="site-header">
    <a href="index.php" aria-label="Arrue · Hasiera"><img src="images/arrue-hortza.svg" class="site-logo" width="64" height="64" alt="Arrue Hortz Sintetikoak"></a>
    <div>
        <h1><?php echo htmlspecialchars($enpresa, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
        <p class="brand-tagline">Hortz-teknologia, kliniken zerbitzura.</p>
    </div>
</header>
<hr class="section-divider">
<nav class="site-navigation" aria-label="Nabigazio nagusia">
<?php if ($signedIn): ?>
    <h3>Ongi Etorri <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h3>
    <a href="index.php?action=account">Kontua</a> |
    <?php if ($administrator): ?>
        <a href="index.php?action=updel">Igo/Ezabatu</a> |
    <?php endif; ?>
    <form class="logout-form" action="index.php?action=logout" method="post">
        <?php echo csrfField(); ?><button type="submit">Saioa itxi</button>
    </form> |
<?php else: ?>
    <a href="index.php?action=register">Erregistratu</a> |
    <a href="index.php?action=login">Saioa hasi</a> |
<?php endif; ?>
    <a href="index.php">Hasiera</a> |
    <a href="about.html">Arrueri buruz</a>
</nav>
<form method="get" action="index.php" id="search" class="search-form">
    <input type="hidden" name="action" value="search">
    <label for="keyword">Bilatu produktuak:</label>
    <input type="text" id="keyword" name="keyword" value="">
    <input type="submit" name="search" value="Bilatu">
</form>
<hr class="section-divider">
<main class="page-content">
<?php
switch ($action) {
    case 'updel':
        include_once __DIR__ . '/includes/updel.php';
        break;
    case 'register':
        include_once __DIR__ . '/includes/register.php';
        break;
    case 'login':
        include_once __DIR__ . '/includes/login.php';
        break;
    case 'description':
        include_once __DIR__ . '/includes/description.php';
        break;
    case 'account':
        include_once __DIR__ . '/includes/account.php';
        break;
    default:
        include_once __DIR__ . '/includes/main.php';
}
?>
</main>
<hr class="section-divider">
<footer class="site-footer">
    <b>Arrue Hortz Sintetikoak</b>
    <p>Diseinutik fabrikaziora. Euskal Herritik Europara.</p>
    <a href="about.html">Enpresa</a> · <a href="about.html#pribatutasuna">Pribatutasuna</a> · <a href="about.html#irudiak">Irudien kredituak</a>
</footer>
</body>
</html>
