<?php
/**
 * @var string $titulo
 * @var array  $ofertas
 * @var bool   $puedeGestionar
 */
require_once dirname(__DIR__, 2) . '/controllers/authController.php';

$pageTitle = 'Ofertas — TalentLink';
$activeMenu = 'ofertas';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['mensaje_exito'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;
$totalOfertas = count($ofertas);

function ofe_clase_estado($nombre)
{
    $n = mb_strtolower((string) $nombre, 'UTF-8');
    if (strpos($n, 'activ') !== false) {
        return 'ofe-estado--activa';
    }
    if (strpos($n, 'paus') !== false) {
        return 'ofe-estado--pausada';
    }
    if (strpos($n, 'cerrad') !== false) {
        return 'ofe-estado--cerrada';
    }
    return 'ofe-estado--default';
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
    <link rel="stylesheet" href="views/ofertas/ofertas_styles.css">
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
                <p class="tl-card-subtitle">Vacantes publicadas a partir de búsquedas registradas</p>
            </div>
            <div class="sol-header-actions">
                <?php if ($totalOfertas > 0) : ?>
                    <span class="tl-badge"><?php echo (int) $totalOfertas; ?> <?php echo $totalOfertas === 1 ? 'oferta' : 'ofertas'; ?></span>
                <?php endif; ?>
                <?php if ($puedeGestionar) : ?>
                    <a class="sol-btn-new" href="index.php?accion=ofertas_create">Publicar oferta</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($mensajeExito !== null) : ?>
            <div class="sol-alert sol-alert--ok" role="status"><?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($errorGeneral !== null) : ?>
            <div class="sol-alert sol-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($ofertas)) : ?>
            <div class="tl-empty">
                <strong>No hay ofertas publicadas</strong>
                <?php if ($puedeGestionar) : ?>
                    Publicá una oferta eligiendo una búsqueda existente.
                <?php elseif (AuthController::esRol('candidato')) : ?>
                    Cuando RRHH publique vacantes activas, aparecerán aquí.
                <?php else : ?>
                    Cuando se publiquen ofertas de su empresa, aparecerán aquí.
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
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <?php if ($puedeGestionar) : ?>
                                <th>Referente RRHH</th>
                                <th></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ofertas as $oferta) : ?>
                            <tr>
                                <td class="tl-cell-name"><?php echo htmlspecialchars($oferta['nombre_puesto'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($oferta['empresa_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo !empty($oferta['cantidad_vacantes']) ? (int) $oferta['cantidad_vacantes'] : '—'; ?></td>
                                <td><?php echo htmlspecialchars(!empty($oferta['modalidad_nombre']) ? $oferta['modalidad_nombre'] : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <span class="ofe-estado <?php echo htmlspecialchars(ofe_clase_estado($oferta['estado_nombre']), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($oferta['estado_nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <?php if ($puedeGestionar) : ?>
                                    <td class="tl-cell-muted"><?php echo htmlspecialchars($oferta['referente_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <a class="ofe-link-edit" href="index.php?accion=ofertas_edit&amp;id=<?php echo (int) $oferta['id']; ?>">Editar estado</a>
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
