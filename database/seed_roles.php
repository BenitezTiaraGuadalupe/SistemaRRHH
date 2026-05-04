<?php

/**
 * CLI: inserta los roles base (admin, empresa, candidato) con ids fijos 1–3.
 *
 * Uso (desde la raíz del proyecto):
 *   php database/seed_roles.php
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

require_once __DIR__ . '/../database.php';

$pdo->exec('SET NAMES utf8mb4');

$sql = "INSERT INTO roles (id, nombre) VALUES
    (1, 'admin'),
    (2, 'empresa'),
    (3, 'candidato')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)";

try {
    $pdo->exec($sql);
    echo "Roles listos: admin (1), empresa (2), candidato (3).\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}
