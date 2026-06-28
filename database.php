<?php

/**
 * Conexión PDO reutilizable. Incluir con require_once después de cargar config.
 */

require_once __DIR__ . '/config.php';

if (!isset($GLOBALS['pdo'])) {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $GLOBALS['pdo'] = new PDO($dsn, DB_USER, DB_PASS, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ));
    } catch (PDOException $e) {
        die('Error de conexión a la base de datos.');
    }
}

$pdo = $GLOBALS['pdo'];
