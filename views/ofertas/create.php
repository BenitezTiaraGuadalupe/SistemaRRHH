<?php
/**
 * @var string $titulo
 * @var array  $busquedas
 * @var array  $estadosOferta
 * @var int    $busquedaPreseleccionada
 */
$old = isset($_SESSION['old']) ? $_SESSION['old'] : array();
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['old'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;
$desdeSolicitudes = isset($_GET['desde']) && $_GET['desde'] === 'solicitudes';
if (!isset($busquedaPreseleccionada)) {
    $busquedaPreseleccionada = 0;
}
$publicarDesdeSolicitud = $desdeSolicitudes && $busquedaPreseleccionada > 0;
$busquedaDirecta = null;
if ($publicarDesdeSolicitud) {
    foreach ($busquedas as $b) {
        if ((int) $b['id'] === $busquedaPreseleccionada) {
            $busquedaDirecta = $b;
            break;
        }
    }
}
$puedeMostrarFormulario = !empty($busquedas) || $busquedaDirecta !== null;

$pageTitle = 'Publicar oferta — TalentLink';
$activeMenu = 'ofertas';

function ofe_old($key, $default = '')
{
    global $old;
    return isset($old[$key]) ? $old[$key] : $default;
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
    <div class="as-card">
        <div class="as-header">
            <div>
                <h2 class="as-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="as-subtitle">Seleccioná una búsqueda y definí el estado de la oferta</p>
            </div>
            <a class="ofe-link-back" href="<?php echo $desdeSolicitudes ? 'index.php?accion=solicitudes' : 'index.php?accion=ofertas'; ?>">Volver</a>
        </div>

        <?php if ($errorGeneral !== null) : ?>
            <div class="sol-alert sol-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!$puedeMostrarFormulario) : ?>
            <div class="tl-empty">
                <strong>No hay búsquedas disponibles</strong>
                Todas las búsquedas ya tienen oferta publicada o aún no hay solicitudes cargadas.
                <div class="ofe-empty-action">
                    <a class="sol-btn-new" href="index.php?accion=solicitudes">Ver solicitudes</a>
                </div>
            </div>
        <?php elseif (empty($estadosOferta)) : ?>
            <div class="sol-alert sol-alert--error" role="alert">
                Faltan los estados de oferta en el sistema. Ejecutá <code>php database/seed_catalogos.php</code>.
            </div>
        <?php else : ?>
            <form method="post" action="index.php?accion=ofertas_store">
                <?php if ($desdeSolicitudes) : ?>
                    <input type="hidden" name="volver_solicitudes" value="1">
                <?php endif; ?>
                <?php if ($publicarDesdeSolicitud && $busquedaDirecta !== null) : ?>
                    <input type="hidden" name="busquedas_id" value="<?php echo (int) $busquedaDirecta['id']; ?>">
                    <div class="as-form-group">
                        <label>Búsqueda</label>
                        <p class="ofe-hint">
                            #<?php echo (int) $busquedaDirecta['id']; ?> —
                            <?php echo htmlspecialchars($busquedaDirecta['nombre_puesto'] . ' (' . $busquedaDirecta['empresa_nombre'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                <?php else : ?>
                    <div class="as-form-group">
                        <label for="busquedas_id">Búsqueda *</label>
                        <select id="busquedas_id" name="busquedas_id" required>
                            <option value="">Seleccionar búsqueda...</option>
                            <?php $oldBusq = (int) ofe_old('busquedas_id', $busquedaPreseleccionada); ?>
                            <?php foreach ($busquedas as $b) : ?>
                                <option value="<?php echo (int) $b['id']; ?>" <?php echo $oldBusq === (int) $b['id'] ? 'selected' : ''; ?>>
                                    #<?php echo (int) $b['id']; ?> —
                                    <?php echo htmlspecialchars($b['nombre_puesto'] . ' (' . $b['empresa_nombre'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errores['busquedas_id'])) : ?>
                            <span class="as-error"><?php echo htmlspecialchars($errores['busquedas_id'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="as-form-group">
                    <label for="estado_ofertas_id">Estado de la oferta *</label>
                    <select id="estado_ofertas_id" name="estado_ofertas_id" required>
                        <?php $oldEst = (int) ofe_old('estado_ofertas_id', 1); ?>
                        <?php foreach ($estadosOferta as $est) : ?>
                            <option value="<?php echo (int) $est['id']; ?>" <?php echo $oldEst === (int) $est['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($est['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['estado_ofertas_id'])) : ?>
                        <span class="as-error"><?php echo htmlspecialchars($errores['estado_ofertas_id'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>

                <p class="ofe-hint">Solo se puede publicar una oferta por búsqueda. Los datos del puesto se toman de la solicitud.</p>

                <button type="submit" class="as-btn-submit">Publicar oferta</button>
            </form>
        <?php endif; ?>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
