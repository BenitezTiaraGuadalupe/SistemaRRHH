<?php
/**
 * @var string $titulo
 * @var array  $ofertas
 */
require_once dirname(__DIR__, 2) . '/controllers/authController.php';

$pageTitle = 'Ofertas — TalentLink';
$activeMenu = 'ofertas';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['mensaje_exito'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;
$totalOfertas = count($ofertas);

function ofe_feed_ubicacion(array $o)
{
    $partes = array();
    if (!empty($o['ciudad_nombre'])) {
        $partes[] = $o['ciudad_nombre'];
    }
    if (!empty($o['provincia_nombre'])) {
        $partes[] = $o['provincia_nombre'];
    }
    if (empty($partes) && !empty($o['pais_nombre'])) {
        $partes[] = $o['pais_nombre'];
    }
    return implode(', ', $partes);
}

function ofe_feed_experiencia($anios)
{
    if ($anios === null || $anios === '') {
        return 'Sin experiencia requerida';
    }
    $n = (int) $anios;
    return $n . ' ' . ($n === 1 ? 'año' : 'años') . ' de experiencia';
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
<div class="tl-page ofe-feed-page">
    <div class="ofe-feed-header">
        <div>
            <h2 class="tl-card-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="tl-card-subtitle">Explorá vacantes activas y postulate en un click</p>
        </div>
        <?php if ($totalOfertas > 0) : ?>
            <span class="tl-badge"><?php echo (int) $totalOfertas; ?> <?php echo $totalOfertas === 1 ? 'oferta' : 'ofertas'; ?></span>
        <?php endif; ?>
    </div>

    <?php if ($mensajeExito !== null) : ?>
        <div class="sol-alert sol-alert--ok" role="status"><?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($errorGeneral !== null) : ?>
        <div class="sol-alert sol-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (empty($ofertas)) : ?>
        <div class="tl-empty">
            <strong>No hay ofertas activas por ahora</strong>
            Cuando RRHH publique nuevas vacantes, las vas a ver acá.
        </div>
    <?php else : ?>
        <div class="ofe-feed-list">
            <?php foreach ($ofertas as $oferta) : ?>
                <?php
                $ubicacion = ofe_feed_ubicacion($oferta);
                $yaPostulado = !empty($oferta['ya_postulado']);
                ?>
                <article class="ofe-card">
                    <div class="ofe-card-main">
                        <div class="ofe-card-head">
                            <div>
                                <h3 class="ofe-card-title"><?php echo htmlspecialchars($oferta['nombre_puesto'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="ofe-card-empresa"><?php echo htmlspecialchars($oferta['empresa_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <span class="ofe-estado ofe-estado--activa">Activa</span>
                        </div>

                        <?php if (!empty($oferta['descripcion'])) : ?>
                            <p class="ofe-card-desc"><?php echo htmlspecialchars($oferta['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>

                        <dl class="ofe-card-meta">
                            <div class="ofe-card-meta-item">
                                <dt>Vacantes</dt>
                                <dd><?php echo !empty($oferta['cantidad_vacantes']) ? (int) $oferta['cantidad_vacantes'] : '—'; ?></dd>
                            </div>
                            <div class="ofe-card-meta-item">
                                <dt>Modalidad</dt>
                                <dd><?php echo htmlspecialchars(!empty($oferta['modalidad_nombre']) ? $oferta['modalidad_nombre'] : '—', ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <div class="ofe-card-meta-item">
                                <dt>Experiencia</dt>
                                <dd><?php echo htmlspecialchars(ofe_feed_experiencia(isset($oferta['anios_experiencia']) ? $oferta['anios_experiencia'] : null), ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <div class="ofe-card-meta-item">
                                <dt>Ubicación</dt>
                                <dd><?php echo htmlspecialchars($ubicacion !== '' ? $ubicacion : '—', ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                        </dl>

                        <?php if (!empty($oferta['habilidades_nombres'])) : ?>
                            <div class="ofe-card-tags">
                                <?php foreach (explode(', ', $oferta['habilidades_nombres']) as $hab) : ?>
                                    <?php if (trim($hab) !== '') : ?>
                                        <span class="ofe-tag"><?php echo htmlspecialchars(trim($hab), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ofe-card-action">
                        <?php if ($yaPostulado) : ?>
                            <span class="ofe-btn ofe-btn--done">Ya postulado</span>
                        <?php else : ?>
                            <form method="post" action="index.php?accion=postulaciones_store">
                                <input type="hidden" name="oferta_id" value="<?php echo (int) $oferta['id']; ?>">
                                <button type="submit" class="ofe-btn ofe-btn--primary">Postularme</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
    </main>
</div>
</div>
</body>
</html>
