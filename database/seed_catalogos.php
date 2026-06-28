<?php

/**
 * CLI: catálogos base + búsquedas demo para pruebas.
 * Idempotente: catálogos con ids fijos; búsquedas demo se recrean por nombre de puesto.
 *
 * Requisitos: migraciones, seed_roles, seed_georef, seed_entidades_demo.
 *
 * Uso: php database/seed_catalogos.php
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

require_once __DIR__ . '/../database.php';

$pdo->exec('SET NAMES utf8mb4');

function insertarCatalogo(PDO $pdo, $tabla, array $filas)
{
    $sql = "INSERT INTO {$tabla} (id, nombre) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)";
    $stmt = $pdo->prepare($sql);
    foreach ($filas as $fila) {
        $stmt->execute(array((int) $fila[0], $fila[1]));
    }
}

try {
    $pdo->beginTransaction();

    insertarCatalogo($pdo, 'estado_busqueda', array(
        array(1, 'Pendiente'),
        array(2, 'En proceso'),
        array(3, 'Cerrada'),
    ));

    insertarCatalogo($pdo, 'estado_ofertas', array(
        array(1, 'Activa'),
        array(2, 'Pausada'),
        array(3, 'Cerrada'),
    ));

    insertarCatalogo($pdo, 'modalidades', array(
        array(1, 'Presencial'),
        array(2, 'Remoto'),
        array(3, 'Híbrida'),
    ));

    insertarCatalogo($pdo, 'etapas', array(
        array(1, 'Recepción de CV'),
        array(2, 'Entrevista inicial'),
        array(3, 'Evaluación técnica'),
        array(4, 'Entrevista final'),
        array(5, 'Contratado'),
        array(6, 'Descartado'),
    ));

    $pdo->commit();
    echo "Catálogos listos: estado_busqueda, estado_ofertas, modalidades, etapas.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Error en catálogos: ' . $e->getMessage() . "\n");
    exit(1);
}

// --- Búsquedas demo (requiere empresas y georef) ---

$puestosDemo = array('Desarrollador Full Stack', 'Analista de Sistemas');
$inPuestos = array();
foreach ($puestosDemo as $p) {
    $inPuestos[] = $pdo->quote($p);
}
$inSql = implode(',', $inPuestos);

$pdo->exec(
    'DELETE hpb FROM habilidades_por_busqueda hpb
     INNER JOIN busquedas b ON b.id = hpb.busquedas_id
     WHERE b.nombre_puesto IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE db FROM detalle_busquedas db
     INNER JOIN busquedas b ON b.id = db.busquedas_id
     WHERE b.nombre_puesto IN (' . $inSql . ')'
);
$pdo->exec('DELETE FROM busquedas WHERE nombre_puesto IN (' . $inSql . ')');

function obtenerEmpresaId(PDO $pdo, $nombre)
{
    $stmt = $pdo->prepare('SELECT id FROM empresas WHERE nombre = ? LIMIT 1');
    $stmt->execute(array($nombre));
    $id = $stmt->fetchColumn();
    return $id === false ? null : (int) $id;
}

function obtenerCiudadDemo(PDO $pdo)
{
    $stmt = $pdo->prepare(
        'SELECT c.id, c.provincias_id, p.paises_id
         FROM ciudades c
         INNER JOIN provincias p ON p.id = c.provincias_id
         WHERE c.nombre LIKE ?
         LIMIT 1'
    );
    $stmt->execute(array('%Posadas%'));
    $row = $stmt->fetch();
    if ($row !== false) {
        return $row;
    }
    return $pdo->query(
        'SELECT c.id, c.provincias_id, p.paises_id
         FROM ciudades c
         INNER JOIN provincias p ON p.id = c.provincias_id
         ORDER BY c.id ASC
         LIMIT 1'
    )->fetch();
}

function obtenerOCrearHabilidad(PDO $pdo, $nombre)
{
    $stmt = $pdo->prepare('SELECT id FROM habilidades WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
    $stmt->execute(array($nombre));
    $id = $stmt->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }
    $ins = $pdo->prepare('INSERT INTO habilidades (nombre) VALUES (?)');
    $ins->execute(array($nombre));
    return (int) $pdo->lastInsertId();
}

$empresaAurora = obtenerEmpresaId($pdo, 'Aurora Tech');
$empresaMate = obtenerEmpresaId($pdo, 'Mate y Code');
$geo = obtenerCiudadDemo($pdo);

if ($empresaAurora === null || $empresaMate === null || $geo === false) {
    fwrite(STDERR, "Faltan empresas demo o ciudades. Ejecute: php database/seed.php\n");
    exit(1);
}

$ciudadId = (int) $geo['id'];
$provinciaId = (int) $geo['provincias_id'];
$paisId = (int) $geo['paises_id'];

$busquedasDemo = array(
    array(
        'nombre_puesto' => 'Desarrollador Full Stack',
        'empresas_id' => $empresaAurora,
        'estado_busqueda_id' => 1,
        'descripcion' => 'Desarrollo y mantenimiento de aplicaciones web con PHP y MySQL.',
        'cantidad_vacantes' => 2,
        'anios_experiencia' => 2,
        'modalidades_id' => 3,
        'habilidades' => array('PHP', 'MySQL', 'JavaScript'),
    ),
    array(
        'nombre_puesto' => 'Analista de Sistemas',
        'empresas_id' => $empresaMate,
        'estado_busqueda_id' => 2,
        'descripcion' => 'Relevamiento de requerimientos y documentación funcional.',
        'cantidad_vacantes' => 1,
        'anios_experiencia' => 1,
        'modalidades_id' => 2,
        'habilidades' => array('Análisis funcional', 'UML'),
    ),
);

try {
    $pdo->beginTransaction();

    $insBusqueda = $pdo->prepare(
        'INSERT INTO busquedas (nombre_puesto, empresas_id, estado_busqueda_id) VALUES (?, ?, ?)'
    );
    $insDetalle = $pdo->prepare(
        'INSERT INTO detalle_busquedas
            (busquedas_id, descripcion, cantidad_vacantes, anios_experiencia,
             modalidades_id, ciudades_id, provincias_id, paises_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $insHab = $pdo->prepare(
        'INSERT INTO habilidades_por_busqueda (busquedas_id, habilidades_id) VALUES (?, ?)'
    );

    foreach ($busquedasDemo as $demo) {
        $insBusqueda->execute(array(
            $demo['nombre_puesto'],
            $demo['empresas_id'],
            $demo['estado_busqueda_id'],
        ));
        $busquedaId = (int) $pdo->lastInsertId();

        $insDetalle->execute(array(
            $busquedaId,
            $demo['descripcion'],
            $demo['cantidad_vacantes'],
            $demo['anios_experiencia'],
            $demo['modalidades_id'],
            $ciudadId,
            $provinciaId,
            $paisId,
        ));

        foreach ($demo['habilidades'] as $nombreHab) {
            $habId = obtenerOCrearHabilidad($pdo, $nombreHab);
            $insHab->execute(array($busquedaId, $habId));
        }
    }

    $pdo->commit();
    echo "Búsquedas demo insertadas: Desarrollador Full Stack (Aurora Tech), Analista de Sistemas (Mate y Code).\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Error en búsquedas demo: ' . $e->getMessage() . "\n");
    exit(1);
}
