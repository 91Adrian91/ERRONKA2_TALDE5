<?php

declare(strict_types=1);

// Ikuspegiaren proba isolatua: ez da MySQL edo HTTP zerbitzaririk erabiltzen.
$phase = $argv[1] ?? '';
if (!in_array($phase, ['aurretik', 'ondoren'], true)) {
    fwrite(STDERR, "Erabilera: php tools/login-xss-proof.php aurretik|ondoren\n");
    exit(1);
}

$_SESSION = [];
$_GET = [];
$_POST = [];
$_SERVER['PHP_SELF'] = '/index.php/"><script>document.getElementById("xss-egoera").textContent="XSS EXEKUTATU DA";</script><span data-x="';
ob_start();
try {
    require __DIR__ . '/../erronka2_web/includes/login.php';
    $view = (string) ob_get_contents();
} finally {
    ob_end_clean();
}

$html = '<!doctype html><html lang="eu"><meta charset="utf-8"><title>AUD-001 — ' . $phase . '</title>'
    . '<body><h1>AUD-001 — ' . $phase . '</h1>'
    . '<p>Saio-hasierako ikuspegiaren proba isolatua. Ez da web osoaren HTTP proba.</p>'
    . '<p id="xss-egoera" style="padding:20px;border:3px solid #555;font-size:24px">JavaScript injektatua EZ da exekutatu</p>'
    . '<p>PHP_SELF aldagaian komatxoak eta script etiketa sartu dira. Ez da kanpoko konexiorik egiten.</p>'
    . $view . '</body></html>';
$directory = __DIR__ . '/../docs/auditoria/ebidentziak/AUD-001';
if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}
$path = $directory . '/' . $phase . '.html';
file_put_contents($path, $html);
echo $path . PHP_EOL;
