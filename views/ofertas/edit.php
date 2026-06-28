<?php
/**
 * @var string $titulo
 * @var array  $oferta
 * @var array  $estadosOferta
 */
$pageTitle = 'Editar oferta — TalentLink';
$activeMenu = 'ofertas';
$desdeSolicitudes = isset($_GET['desde']) && $_GET['desde'] === 'solicitudes';

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
                <p class="as-subtitle">Cambiar el estado de la oferta publicada</p>
            </div>
            <a class="ofe-link-back" href="<?php echo $desdeSolicitudes ? 'index.php?accion=solicitudes' : 'index.php?accion=ofertas'; ?>">Volver</a>
        </div>

        <dl class="ofe-resumen">
            <dt>Puesto</dt>
            <dd><?php echo htmlspecialchars($oferta['nombre_puesto'], ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt>Empresa</dt>
            <dd><?php echo htmlspecialchars($oferta['empresa_nombre'], ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt>Búsqueda</dt>
            <dd>#<?php echo (int) $oferta['busqueda_id']; ?></dd>
            <?php if (!empty($oferta['cantidad_vacantes'])) : ?>
                <dt>Vacantes</dt>
                <dd><?php echo (int) $oferta['cantidad_vacantes']; ?></dd>
            <?php endif; ?>
            <?php if (!empty($oferta['modalidad_nombre'])) : ?>
                <dt>Modalidad</dt>
                <dd><?php echo htmlspecialchars($oferta['modalidad_nombre'], ENT_QUOTES, 'UTF-8'); ?></dd>
            <?php endif; ?>
        </dl>

        <form method="post" action="index.php?accion=ofertas_update">
            <input type="hidden" name="id" value="<?php echo (int) $oferta['id']; ?>">
            <?php if ($desdeSolicitudes) : ?>
                <input type="hidden" name="volver_solicitudes" value="1">
            <?php endif; ?>

            <div class="as-form-group">
                <label for="estado_ofertas_id">Estado de la oferta</label>
                <select id="estado_ofertas_id" name="estado_ofertas_id" required>
                    <?php foreach ($estadosOferta as $est) : ?>
                        <option value="<?php echo (int) $est['id']; ?>"
                            <?php echo (int) $oferta['estado_ofertas_id'] === (int) $est['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($est['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="as-btn-submit">Guardar cambios</button>
        </form>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
