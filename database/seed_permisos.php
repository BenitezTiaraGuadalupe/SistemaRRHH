<?php

/**
 * CLI: catálogo base de permisos y su asignación a roles.
 * Idempotente: borra permisos_por_roles y permisos, y los vuelve a insertar.
 *
 * Roles esperados: admin (1), empresa (2), candidato (3) -> seed_roles.php.
 *
 * Uso: php database/seed_permisos.php
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

require_once __DIR__ . '/../database.php';

$pdo->exec('SET NAMES utf8mb4');

$rolAdmin = $pdo->query("SELECT id FROM roles WHERE nombre = 'admin' LIMIT 1")->fetchColumn();
$rolEmpresa = $pdo->query("SELECT id FROM roles WHERE nombre = 'empresa' LIMIT 1")->fetchColumn();
$rolCandidato = $pdo->query("SELECT id FROM roles WHERE nombre = 'candidato' LIMIT 1")->fetchColumn();

if ($rolAdmin === false || $rolEmpresa === false || $rolCandidato === false) {
    fwrite(STDERR, "Faltan roles. Ejecute antes: php database/seed_roles.php\n");
    exit(1);
}

$rolAdmin = (int) $rolAdmin;
$rolEmpresa = (int) $rolEmpresa;
$rolCandidato = (int) $rolCandidato;

// Catálogo completo de permisos del sistema.
$permisos = array(
    'dashboard.ver',
    'solicitudes.ver',
    'solicitudes.crear',
    'solicitudes.editar',
    'solicitudes.eliminar',
    'candidatos.ver',
    'candidatos.editar',
    'empresas.ver',
    'empresas.editar',
    'ofertas.ver',
    'ofertas.crear',
    'postulaciones.ver',
    'postulaciones.crear',
    'postulaciones.gestionar',
    'reportes.ver',
    'usuarios.ver',
    'usuarios.administrar',
);

// Asignación por rol. admin tiene todos.
$asignacion = array(
    $rolAdmin => $permisos,
    $rolEmpresa => array(
        'dashboard.ver',
        'solicitudes.crear',
        'empresas.editar',
        'ofertas.ver',
        'postulaciones.ver',
    ),
    $rolCandidato => array(
        'dashboard.ver',
        'candidatos.editar',
        'ofertas.ver',
        'postulaciones.ver',
        'postulaciones.crear',
    ),
);

try {
    $pdo->beginTransaction();

    $pdo->exec('DELETE FROM permisos_por_roles');
    $pdo->exec('DELETE FROM permisos');

    $insPermiso = $pdo->prepare('INSERT INTO permisos (nombre) VALUES (?)');
    $idsPermisos = array();
    foreach ($permisos as $nombre) {
        $insPermiso->execute(array($nombre));
        $idsPermisos[$nombre] = (int) $pdo->lastInsertId();
    }

    $insPpr = $pdo->prepare('INSERT INTO permisos_por_roles (permisos_id, roles_id) VALUES (?, ?)');
    $totalAsignaciones = 0;
    foreach ($asignacion as $rolId => $listaNombres) {
        foreach ($listaNombres as $nombre) {
            if (!isset($idsPermisos[$nombre])) {
                continue;
            }
            $insPpr->execute(array($idsPermisos[$nombre], $rolId));
            $totalAsignaciones++;
        }
    }

    $pdo->commit();

    $totalPermisos = count($permisos);
    echo "Permisos insertados: {$totalPermisos}. Asignaciones rol-permiso: {$totalAsignaciones}.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}
