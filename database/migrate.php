<?php

/**
 * CLI: ejecuta cada .sql nuevo en database/migrations/ una sola vez.
 * Uso (desde la raíz del proyecto): php database/migrate.php
 */

require_once __DIR__ . '/../database.php';

$migrationsPath = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

/**
 * @return string[]
 */
//Busca los archivos .sql en el directorio de migraciones, los ordena alfabéticamente y los devuelve en un array.
 function sqlFilesInOrder(string $migrationsPath)
{
    $files = glob($migrationsPath . DIRECTORY_SEPARATOR . '*.sql');
    if ($files === false) {
        return array();
    }
    sort($files, SORT_STRING);
    return $files;
}

//Verifica si la tabla existe en la base de datos.
function tableExists(PDO $pdo, string $tableName)
{
    $sql = 'SELECT COUNT(*) FROM information_schema.tables
            WHERE table_schema = DATABASE() AND table_name = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tableName));
    return (int) $stmt->fetchColumn() > 0;
}

//Verifica si la migración ya se ha aplicado.
function isMigrationApplied(PDO $pdo, string $fileName)
{
    if (!tableExists($pdo, 'migrations')) {
        return false;
    }
    $stmt = $pdo->prepare('SELECT 1 FROM migrations WHERE migration = ? LIMIT 1');
    $stmt->execute(array($fileName));
    return (bool) $stmt->fetchColumn();
}

/**
 * Parte el script en sentencias por ; (suficiente para DDL típico de Workbench).
 * No soporta DELIMITER / procedimientos con ; internos; eso iría en otro flujo.
 *
 * @return string[]
 */
//Parte el script en sentencias por ; (suficiente para DDL típico de Workbench).
function splitSqlStatements(string $sql)
{
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql);
    $parts = preg_split('/;\s*\R?/', trim($sql));
    $out = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') {
            $out[] = $p;
        }
    }
    return $out;
}

$files = sqlFilesInOrder($migrationsPath);

if (count($files) === 0) {
    echo "No hay archivos .sql en database/migrations/\n";
    exit(0);
}

foreach ($files as $path) {
    $name = basename($path);

    if (isMigrationApplied($pdo, $name)) {
        echo "[omitir] $name\n";
        continue;
    }

    $body = file_get_contents($path);
    if ($body === false) {
        echo "[error] No se pudo leer: $name\n";
        exit(1);
    }

    $statements = splitSqlStatements($body);

    try {
        foreach ($statements as $stmt) {
            $pdo->exec($stmt);
        }
        $ins = $pdo->prepare('INSERT INTO migrations (migration) VALUES (?)');
        $ins->execute(array($name));
        echo "[ok] $name\n";
    } catch (Throwable $e) {
        echo "[error] $name — " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Listo.\n";
