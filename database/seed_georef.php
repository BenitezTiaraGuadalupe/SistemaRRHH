<?php

/**
 * CLI: descarga provincias y localidades desde API Georef (Argentina) y las inserta
 * en las tablas `provincias` y `ciudades` (las localidades de Georef se guardan en `ciudades`).
 *
 * Requisitos: migración base aplicada; idealmente sin filas en `ciudades`/`provincias`
 * que deban conservarse (re-ejecutar actualiza por id con ON DUPLICATE KEY UPDATE).
 *
 * Uso (desde la raíz del proyecto):
 *   php database/seed_georef.php
 *   php database/seed_georef.php --solo-provincias
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

require_once __DIR__ . '/../database.php';

$baseUrl = 'https://apis.datos.gob.ar/georef/api/v2.0';
$paisId = 1;
$maxPorPagina = 500;
$soloProvincias = in_array('--solo-provincias', $argv, true);

/**
 * @return array
 */
function georefGetJson($url)
{
    $ctx = stream_context_create(array(
        'http' => array(
            'timeout' => 180,
            'header' => "Accept: application/json\r\n",
        ),
    ));
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        throw new RuntimeException('No se pudo descargar: ' . $url);
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('Respuesta JSON invalida: ' . $url);
    }
    return $data;
}

function georefProvinciaId($id)
{
    return (int) $id;
}

function georefLocalidadId($id)
{
    // No casteamos a int: en algunos entornos puede desbordar y además puede haber ceros a la izquierda.
    return (string) $id;
}

$pdo->exec('SET NAMES utf8mb4');

$pdo->exec(
    "INSERT IGNORE INTO paises (id, nombre) VALUES (" . (int) $paisId . ", 'Argentina')"
);

echo "Descargando provincias...\n";
$provData = georefGetJson($baseUrl . '/provincias.json');
if (empty($provData['provincias']) || !is_array($provData['provincias'])) {
    fwrite(STDERR, "Respuesta de provincias sin clave 'provincias'.\n");
    exit(1);
}

$stmtProv = $pdo->prepare(
    'INSERT INTO provincias (id, nombre, paises_id) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), paises_id = VALUES(paises_id)'
);

$pdo->beginTransaction();
try {
    foreach ($provData['provincias'] as $p) {
        $stmtProv->execute(array(
            georefProvinciaId($p['id']),
            $p['nombre'],
            $paisId,
        ));
    }
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error provincias: ' . $e->getMessage() . "\n");
    exit(1);
}

echo 'Provincias cargadas: ' . count($provData['provincias']) . "\n";

if ($soloProvincias) {
    echo "Listo (--solo-provincias).\n";
    exit(0);
}

echo "Descargando localidades (paginado)...\n";

$stmtLoc = $pdo->prepare(
    'INSERT INTO ciudades (id, nombre, provincias_id) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), provincias_id = VALUES(provincias_id)'
);

$inicio = 0;
$total = null;

while (true) {
    $url = $baseUrl . '/localidades?inicio=' . $inicio . '&max=' . $maxPorPagina;
    $page = georefGetJson($url);

    if ($total === null && isset($page['total'])) {
        $total = (int) $page['total'];
    }

    if (empty($page['localidades']) || !is_array($page['localidades'])) {
        break;
    }

    $pdo->beginTransaction();
    try {
        foreach ($page['localidades'] as $loc) {
            if (empty($loc['provincia']['id'])) {
                continue;
            }
            $stmtLoc->execute(array(
                georefLocalidadId($loc['id']),
                $loc['nombre'],
                georefProvinciaId($loc['provincia']['id']),
            ));
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        fwrite(STDERR, 'Error localidades inicio=' . $inicio . ': ' . $e->getMessage() . "\n");
        exit(1);
    }

    $cant = count($page['localidades']);
    $inicio += $cant;
    if ($total !== null) {
        echo "  ... $inicio / $total\n";
    } else {
        echo "  ... $inicio\n";
    }

    if ($cant < $maxPorPagina) {
        break;
    }
    if ($total !== null && $inicio >= $total) {
        break;
    }
}

echo "Listo.\n";
