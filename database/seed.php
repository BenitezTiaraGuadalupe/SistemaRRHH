<?php

/**
 * CLI: ejecuta los seeders en orden coherente.
 *
 * Uso (desde la raíz del proyecto):
 *   php database/seed.php
 *   php database/seed.php --sin-georef
 *
 * Orden:
 *  - seed_roles.php
 *  - seed_georef.php (provincias + ciudades)
 *  - seed_entidades_demo.php (empresas + candidatos + personal_rrhh + usuarios)
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

$sinGeoref = in_array('--sin-georef', $argv, true);

$root = dirname(__DIR__);

$seeders = array(
    $root . '/database/seed_roles.php',
);

if (!$sinGeoref) {
    $seeders[] = $root . '/database/seed_georef.php';
}

$seeders[] = $root . '/database/seed_entidades_demo.php';

foreach ($seeders as $file) {
    if (!file_exists($file)) {
        fwrite(STDERR, "No existe seeder: {$file}\n");
        exit(1);
    }

    echo "==> " . basename($file) . "\n";
    $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($file);
    passthru($cmd, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Seeder falló: " . basename($file) . "\n");
        exit($code);
    }
}

echo "Seeds completos.\n";

