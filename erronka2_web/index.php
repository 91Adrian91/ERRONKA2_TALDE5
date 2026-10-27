<?php

ob_start();
session_start();

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

$signedIn = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$administrator = $signedIn && ($_SESSION['username'] ?? '') === 'admin@bdweb';

if (($action === 'updel' && !$administrator) || ($action === 'account' && !$signedIn)) {
    redirectToHome();
}
if ($action === 'logout') {
    session_destroy();
    redirectToHome();
}

?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($enpresa, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <img src="logo.png" class="site-logo" width="115" height="110" alt="Enpresaren logotipoa">
    <div>
        <hr>
        <h1><?php echo htmlspecialchars($enpresa, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
        <hr>
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
    <a href="index.php?action=logout">Saioa itxi</a> |
<?php else: ?>
    <a href="index.php?action=register">Erregistratu</a> |
    <a href="index.php?action=login">Saioa hasi</a> |
<?php endif; ?>
    <a href="index.php">Hasiera</a>
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
    <b>Proiektua <?php echo htmlspecialchars($version, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></b>
</footer>
</body>
</html>
