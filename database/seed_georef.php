<?php

/**
 * CLI: descarga provincias y localidades desde API Georef (Argentina) y las inserta
 * en las tablas `provincias` y `ciudades` (las localidades de Georef se guardan en `ciudades`).
 *
 * Si la API de localidades no responde, carga un conjunto mínimo de ciudades para desarrollo.
 *
 * Uso (desde la raíz del proyecto):
 *   php database/seed_georef.php
 *   php database/seed_georef.php --solo-provincias
 *   php database/seed_georef.php --solo-minimo
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
$soloMinimo = in_array('--solo-minimo', $argv, true);

/**
 * @return array|null
 */
function georefGetJson($url)
{
    $intentos = 2;
    for ($i = 1; $i <= $intentos; $i++) {
        $raw = false;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
            $raw = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($raw === false || $code < 200 || $code >= 300) {
                $raw = false;
            }
        }

        if ($raw === false) {
            $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 25,
                    'header' => "Accept: application/json\r\n",
                ),
            ));
            $raw = @file_get_contents($url, false, $ctx);
        }

        if ($raw !== false) {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                return $data;
            }
        }

        if ($i < $intentos) {
            sleep(1);
        }
    }

    return null;
}

/**
 * Ciudades básicas cuando la API de localidades no está disponible.
 */
function insertarCiudadesMinimas(PDO $pdo, $paisId)
{
    $ciudades = array(
        array('Posadas', 'Misiones'),
        array('Oberá', 'Misiones'),
        array('Córdoba', 'Córdoba'),
        array('Rosario', 'Santa Fe'),
        array('Mendoza', 'Mendoza'),
        array('La Plata', 'Buenos Aires'),
        array('Mar del Plata', 'Buenos Aires'),
        array('Buenos Aires', 'Ciudad Autónoma de Buenos Aires'),
    );

    $stmtProvId = $pdo->prepare('SELECT id FROM provincias WHERE nombre = ? AND paises_id = ? LIMIT 1');
    $stmtIns = $pdo->prepare(
        'INSERT INTO ciudades (nombre, provincias_id) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), provincias_id = VALUES(provincias_id)'
    );

    $insertadas = 0;
    $pdo->beginTransaction();
    try {
        foreach ($ciudades as $par) {
            $stmtProvId->execute(array($par[1], $paisId));
            $provId = $stmtProvId->fetchColumn();
            if ($provId === false) {
                continue;
            }
            $stmtIns->execute(array($par[0], (int) $provId));
            $insertadas++;
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return $insertadas;
}

$pdo->exec('SET NAMES utf8mb4');

$pdo->exec(
    "INSERT IGNORE INTO paises (id, nombre) VALUES (" . (int) $paisId . ", 'Argentina')"
);

if (!$soloMinimo) {
    echo "Descargando provincias...\n";
    $provData = georefGetJson($baseUrl . '/provincias.json');
    if ($provData === null || empty($provData['provincias']) || !is_array($provData['provincias'])) {
        fwrite(STDERR, "No se pudieron descargar provincias desde Georef.\n");
        exit(1);
    }

    $stmtProvIns = $pdo->prepare(
        'INSERT INTO provincias (nombre, paises_id) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), paises_id = VALUES(paises_id)'
    );

    $pdo->beginTransaction();
    try {
        foreach ($provData['provincias'] as $p) {
            $stmtProvIns->execute(array(
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
} else {
    echo "Omitiendo descarga de provincias (--solo-minimo).\n";
}

if ($soloProvincias) {
    echo "Listo (--solo-provincias).\n";
    exit(0);
}

if ($soloMinimo) {
    $n = insertarCiudadesMinimas($pdo, $paisId);
    echo "Ciudades mínimas cargadas: $n\n";
    echo "Listo (--solo-minimo).\n";
    exit(0);
}

echo "Descargando localidades (paginado)...\n";

$stmtProvId = $pdo->prepare('SELECT id FROM provincias WHERE nombre = ? AND paises_id = ? LIMIT 1');
$stmtLocIns = $pdo->prepare(
    'INSERT INTO ciudades (nombre, provincias_id) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), provincias_id = VALUES(provincias_id)'
);

$inicio = 0;
$total = null;
$paginasOk = 0;
$primeraPagina = georefGetJson($baseUrl . '/localidades?inicio=0&max=' . $maxPorPagina);

if ($primeraPagina === null || empty($primeraPagina['localidades'])) {
    echo "AVISO: la API de localidades no respondió. Usando ciudades mínimas para desarrollo.\n";
    $n = insertarCiudadesMinimas($pdo, $paisId);
    echo "Ciudades mínimas cargadas: $n\n";
    echo "Listo.\n";
    exit(0);
}

$page = $primeraPagina;

while (true) {
    if ($total === null && isset($page['total'])) {
        $total = (int) $page['total'];
    }

    if (empty($page['localidades']) || !is_array($page['localidades'])) {
        break;
    }

    $pdo->beginTransaction();
    try {
        foreach ($page['localidades'] as $loc) {
            if (empty($loc['provincia']['nombre'])) {
                continue;
            }
            $provNombre = $loc['provincia']['nombre'];
            $stmtProvId->execute(array($provNombre, $paisId));
            $provId = $stmtProvId->fetchColumn();
            if ($provId === false) {
                continue;
            }
            $stmtLocIns->execute(array(
                $loc['nombre'],
                (int) $provId,
            ));
        }
        $pdo->commit();
        $paginasOk++;
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

    $page = georefGetJson($baseUrl . '/localidades?inicio=' . $inicio . '&max=' . $maxPorPagina);
    if ($page === null) {
        echo "AVISO: falló una página intermedia; se conservó lo descargado hasta inicio=$inicio.\n";
        break;
    }
}

if ($paginasOk === 0) {
    $n = insertarCiudadesMinimas($pdo, $paisId);
    echo "Ciudades mínimas cargadas: $n\n";
}

echo "Listo.\n";
