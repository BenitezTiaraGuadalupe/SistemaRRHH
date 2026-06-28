<?php
/**
 * @var string $titulo
 * @var array  $solicitudes
 */
require_once dirname(__DIR__, 2) . '/controllers/authController.php';

$pageTitle = 'Solicitudes — TalentLink';
$activeMenu = 'solicitudes';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['mensaje_exito'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;
$totalSolicitudes = count($solicitudes);
$puedeCrear = AuthController::tienePermiso('solicitudes.crear');
$puedeGestionarOfertas = AuthController::tienePermiso('ofertas.crear');

function sol_formatear_ubicacion(array $s)
{
    $partes = array();
    if (!empty($s['ciudad_nombre'])) {
        $partes[] = $s['ciudad_nombre'];
    }
    if (!empty($s['provincia_nombre'])) {
        $partes[] = $s['provincia_nombre'];
    }
    if (empty($partes) && !empty($s['pais_nombre'])) {
        $partes[] = $s['pais_nombre'];
    }
    return implode(', ', $partes);
}

function sol_clase_estado($nombre)
{
    $n = mb_strtolower((string) $nombre, 'UTF-8');
    if (strpos($n, 'pend') !== false) {
        return 'sol-estado--pendiente';
    }
    if (strpos($n, 'proceso') !== false || strpos($n, 'curso') !== false) {
        return 'sol-estado--proceso';
    }
    if (strpos($n, 'cubier') !== false || strpos($n, 'cerrad') !== false || strpos($n, 'finaliz') !== false) {
        return 'sol-estado--cerrada';
    }
    if (strpos($n, 'cancel') !== false) {
        return 'sol-estado--cancelada';
    }
    return 'sol-estado--default';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="views/partials/app_styles.css">
    <link rel="stylesheet" href="views/solicitudes/solicitudes_styles.css">
</head>
<body>
<div class="app">
<?php
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/navbar.php';
?>
<div class="tl-page">
    <div class="tl-card">
        <div class="tl-card-header">
            <div>
                <h2 class="tl-card-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="tl-card-subtitle">Búsquedas de personal solicitadas por empresas cliente</p>
            </div>
            <div class="sol-header-actions">
                <?php if ($totalSolicitudes > 0) : ?>
                    <span class="tl-badge"><?php echo (int) $totalSolicitudes; ?> <?php echo $totalSolicitudes === 1 ? 'solicitud' : 'solicitudes'; ?></span>
                <?php endif; ?>
                <?php if ($puedeCrear) : ?>
                    <a class="sol-btn-new" href="index.php?accion=create">Nueva solicitud</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($mensajeExito !== null) : ?>
            <div class="sol-alert sol-alert--ok" role="status">
                <?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorGeneral !== null) : ?>
            <div class="sol-alert sol-alert--error" role="alert">
                <?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($errores['busquedas_id'])) : ?>
            <div class="sol-alert sol-alert--error" role="alert">
                <?php echo htmlspecialchars($errores['busquedas_id'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($solicitudes)) : ?>
            <div class="tl-empty">
                <strong>No hay solicitudes cargadas</strong>
                Cuando las empresas soliciten personal, las búsquedas aparecerán en este listado.
                <?php if ($puedeCrear) : ?>
                    <div class="sol-empty-action">
                        <a class="sol-btn-new" href="index.php?accion=create">Registrar primera solicitud</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="tl-table-wrap">
                <table class="tl-table">
                    <thead>
                        <tr>
                            <th>Puesto</th>
                            <th>Empresa</th>
                            <th>Vacantes</th>
                            <th>Experiencia</th>
                            <th>Modalidad</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <?php if ($puedeGestionarOfertas) : ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud) : ?>
                            <?php
                            $ubicacion = sol_formatear_ubicacion($solicitud);
                            $vacantes = isset($solicitud['cantidad_vacantes']) ? (int) $solicitud['cantidad_vacantes'] : 0;
                            $experiencia = isset($solicitud['anios_experiencia']) ? $solicitud['anios_experiencia'] : null;
                            $modalidad = isset($solicitud['modalidad_nombre']) ? $solicitud['modalidad_nombre'] : '';
                            $estado = isset($solicitud['estado_nombre']) ? $solicitud['estado_nombre'] : '';
                            ?>
                            <tr>
                                <td class="tl-cell-name"><?php echo htmlspecialchars($solicitud['nombre_puesto'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['empresa_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo $vacantes > 0 ? (int) $vacantes : '—'; ?></td>
                                <td class="tl-cell-muted">
                                    <?php
                                    if ($experiencia === null || $experiencia === '') {
                                        echo '—';
                                    } else {
                                        echo (int) $experiencia . ' ' . ((int) $experiencia === 1 ? 'año' : 'años');
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($modalidad !== '' ? $modalidad : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo htmlspecialchars($ubicacion !== '' ? $ubicacion : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ($estado !== '') : ?>
                                        <span class="sol-estado <?php echo htmlspecialchars(sol_clase_estado($estado), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php else : ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <?php if ($puedeGestionarOfertas) : ?>
                                    <td class="sol-acciones">
                                        <?php if (!empty($solicitud['oferta_id'])) : ?>
                                            <a class="sol-accion-link"
                                               href="index.php?accion=ofertas_edit&amp;id=<?php echo (int) $solicitud['oferta_id']; ?>&amp;desde=solicitudes">
                                                Editar oferta
                                            </a>
                                            <?php if (!empty($solicitud['oferta_estado_nombre'])) : ?>
                                                <span class="sol-accion-hint"><?php echo htmlspecialchars($solicitud['oferta_estado_nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a class="sol-accion-link sol-accion-link--primary"
                                               href="index.php?accion=ofertas_create&amp;busquedas_id=<?php echo (int) $solicitud['id']; ?>&amp;desde=solicitudes">
                                                Publicar oferta
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
