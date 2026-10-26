<?php

declare(strict_types=1);

$failed = false;
foreach (['erronka2_web', 'tools', 'tests'] as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../' . $directory));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            passthru(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()), $exitCode);
            $failed = $failed || $exitCode !== 0;
        }
    }
}
exit($failed ? 1 : 0);
