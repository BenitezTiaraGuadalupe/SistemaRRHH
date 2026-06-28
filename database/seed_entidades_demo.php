<?php

/**
 * CLI: 5 empresas + 5 candidatos + 3 personal_rrhh, cada uno con su usuario.
 * Roles: empresa (2), candidato (3), admin (1) según seed_roles.php.
 * Correos fijos *@fantasia.local: re-ejecutar borra y vuelve a insertar esos registros.
 *
 * Requisitos: migraciones aplicadas, seed_roles.php ejecutado, y ciudades cargadas
 * (ejecutar antes: php database/seed_georef.php).
 *
 * Uso: php database/seed_entidades_demo.php
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por linea de comandos.\n");
    exit(1);
}

require_once __DIR__ . '/../database.php';

$pdo->exec('SET NAMES utf8mb4');

$rolEmpresa = $pdo->query("SELECT id FROM roles WHERE nombre = 'empresa' LIMIT 1")->fetchColumn();
$rolCandidato = $pdo->query("SELECT id FROM roles WHERE nombre = 'candidato' LIMIT 1")->fetchColumn();
$rolAdmin = $pdo->query("SELECT id FROM roles WHERE nombre = 'admin' LIMIT 1")->fetchColumn();

if ($rolEmpresa === false || $rolCandidato === false || $rolAdmin === false) {
    fwrite(STDERR, "Faltan roles. Ejecute antes: php database/seed_roles.php\n");
    exit(1);
}

$rolEmpresa = (int) $rolEmpresa;
$rolCandidato = (int) $rolCandidato;
$rolAdmin = (int) $rolAdmin;

$rrhhCorreos = array(
    'rrhh.ana.gomez@talentlink.com',
    'rrhh.bruno.lopez@talentlink.com',
    'rrhh.carla.martinez@talentlink.com',
);

$empresasCorreos = array(
    'talento@auroratech.com',
    'contacto@mateycode.com',
    'rrhh@pampafoods.com',
    'jobs@andescargo.com',
    'empleos@rioplata.com',
);

$candidatosCorreos = array(
    'luna.perez@mail.com',
    'tomas.rojas@mail.com',
    'maria.garcia@mail.com',
    'juan.sosa@mail.com',
    'sofia.diaz@mail.com',
);

$correos = array_merge($rrhhCorreos, $empresasCorreos, $candidatosCorreos);

$inList = array();
foreach ($correos as $c) {
    $inList[] = $pdo->quote($c);
}
$inSql = implode(',', $inList);

// Búsquedas/ofertas/postulaciones de empresas demo (evita error FK al re-ejecutar seed)
$pdo->exec(
    'DELETE ppc FROM postulaciones_por_candidatos ppc
     INNER JOIN postulaciones p ON p.id = ppc.postulaciones_id
     INNER JOIN ofertas o ON o.id = p.ofertas_id
     INNER JOIN busquedas b ON b.id = o.busquedas_id
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE p FROM postulaciones p
     INNER JOIN ofertas o ON o.id = p.ofertas_id
     INNER JOIN busquedas b ON b.id = o.busquedas_id
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE o FROM ofertas o
     INNER JOIN busquedas b ON b.id = o.busquedas_id
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE hpb FROM habilidades_por_busqueda hpb
     INNER JOIN busquedas b ON b.id = hpb.busquedas_id
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE db FROM detalle_busquedas db
     INNER JOIN busquedas b ON b.id = db.busquedas_id
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE b FROM busquedas b
     INNER JOIN empresas e ON e.id = b.empresas_id
     INNER JOIN usuarios u ON u.id = e.usuarios_id
     WHERE u.correo IN (' . $inSql . ')'
);

$pdo->exec(
    'DELETE c FROM candidatos c INNER JOIN usuarios u ON c.usuarios_id = u.id WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE e FROM empresas e INNER JOIN usuarios u ON e.usuarios_id = u.id WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec(
    'DELETE p FROM personal_rrhh p INNER JOIN usuarios u ON p.usuarios_id = u.id WHERE u.correo IN (' . $inSql . ')'
);
$pdo->exec('DELETE FROM usuarios WHERE correo IN (' . $inSql . ')');

$passwordDemoPlano = 'fantasia123';
$passwordDemo = password_hash($passwordDemoPlano, PASSWORD_DEFAULT);

function obtenerCiudadDemoId(PDO $pdo)
{
    $id = $pdo->query('SELECT id FROM ciudades ORDER BY id ASC LIMIT 1')->fetchColumn();
    if ($id !== false) {
        return (string) $id;
    }
    return 0;
}

$ciudadId = obtenerCiudadDemoId($pdo);
if ($ciudadId === 0) {
    fwrite(STDERR, "No hay ciudades cargadas. Ejecute antes: php database/seed_georef.php\n");
    exit(1);
}

$insUsuario = $pdo->prepare(
    'INSERT INTO usuarios (roles_id, correo, password) VALUES (?, ?, ?)'
);
$insEmpresa = $pdo->prepare('INSERT INTO empresas (nombre, usuarios_id) VALUES (?, ?)');
$insCandidato = $pdo->prepare(
    'INSERT INTO candidatos (usuarios_id, nombre, apellido, fecha_nac, ciudades_id) VALUES (?, ?, ?, ?, ?)'
);
$insRrhh = $pdo->prepare('INSERT INTO personal_rrhh (usuarios_id, nombre, apellido) VALUES (?, ?, ?)');

try {
    $pdo->beginTransaction();

    $nombresEmpresas = array('Aurora Tech', 'Mate y Code', 'Pampa Foods', 'Andes Cargo', 'Río Plata SA');
    foreach ($empresasCorreos as $idx => $correo) {
        $insUsuario->execute(array($rolEmpresa, $correo, $passwordDemo));
        $uid = (int) $pdo->lastInsertId();
        $insEmpresa->execute(array($nombresEmpresas[$idx], $uid));
    }

    $nombresCand = array('Luna', 'Tomás', 'María', 'Juan', 'Sofía');
    $apellidosCand = array('Pérez', 'Rojas', 'García', 'Sosa', 'Díaz');
    $fechasNac = array(
        '1990-01-15 00:00:00',
        '1990-02-15 00:00:00',
        '1990-03-15 00:00:00',
        '1990-04-15 00:00:00',
        '1990-05-15 00:00:00',
    );
    foreach ($candidatosCorreos as $idx => $correo) {
        $insUsuario->execute(array($rolCandidato, $correo, $passwordDemo));
        $uid = (int) $pdo->lastInsertId();
        $insCandidato->execute(array(
            $uid,
            $nombresCand[$idx],
            $apellidosCand[$idx],
            $fechasNac[$idx],
            $ciudadId,
        ));
    }

    $nombresRrhh = array('Ana', 'Bruno', 'Carla');
    $apellidosRrhh = array('Gomez', 'Lopez', 'Martinez');
    foreach ($rrhhCorreos as $idx => $correo) {
        $insUsuario->execute(array($rolAdmin, $correo, $passwordDemo));
        $uid = (int) $pdo->lastInsertId();
        $insRrhh->execute(array($uid, $nombresRrhh[$idx], $apellidosRrhh[$idx]));
    }

    $pdo->commit();
    echo "Insertados: 5 empresas, 5 candidatos, 3 personal_rrhh y 13 usuarios (password: {$passwordDemoPlano}).\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}
