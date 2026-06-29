<?php
/**
 * @var string $titulo
 * @var array  $postulaciones
 */
$pageTitle = 'Mis postulaciones — TalentLink';
$activeMenu = 'postulaciones';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
unset($_SESSION['mensaje_exito']);
$total = count($postulaciones);

function pos_clase_etapa($nombre)
{
    $n = mb_strtolower((string) $nombre, 'UTF-8');
    if (strpos($n, 'contrat') !== false) {
        return 'pos-etapa--ok';
    }
    if (strpos($n, 'descart') !== false) {
        return 'pos-etapa--fail';
    }
    if (strpos($n, 'recep') !== false || strpos($n, 'cv') !== false) {
        return 'pos-etapa--inicio';
    }
    if (strpos($n, 'entrev') !== false || strpos($n, 'evalu') !== false || strpos($n, 'técnic') !== false) {
        return 'pos-etapa--proceso';
    }
    return 'pos-etapa--default';
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
    <link rel="stylesheet" href="views/postulaciones/postulaciones_styles.css">
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
                <p class="tl-card-subtitle">Seguimiento de tus postulaciones y etapa actual del proceso</p>
            </div>
            <?php if ($total > 0) : ?>
                <span class="tl-badge"><?php echo (int) $total; ?> <?php echo $total === 1 ? 'postulación' : 'postulaciones'; ?></span>
            <?php endif; ?>
        </div>

        <?php if ($mensajeExito !== null) : ?>
            <div class="sol-alert sol-alert--ok" role="status" style="margin: 0 22px 16px;">
                <?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($postulaciones)) : ?>
            <div class="tl-empty">
                <strong>Todavía no te postulaste a ninguna oferta</strong>
                Explorá las vacantes activas y enviá tu postulación.
                <div class="pos-empty-action">
                    <a class="sol-btn-new" href="index.php?accion=ofertas">Ver ofertas</a>
                </div>
            </div>
        <?php else : ?>
            <div class="pos-card-list">
                <?php foreach ($postulaciones as $post) : ?>
                    <article class="pos-card">
                        <div class="pos-card-info">
                            <h3 class="pos-card-title"><?php echo htmlspecialchars($post['nombre_puesto'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="pos-card-empresa"><?php echo htmlspecialchars($post['empresa_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pos-card-meta">
                                <?php
                                $detalle = array();
                                if (!empty($post['modalidad_nombre'])) {
                                    $detalle[] = $post['modalidad_nombre'];
                                }
                                if (!empty($post['oferta_estado_nombre'])) {
                                    $detalle[] = 'Oferta ' . mb_strtolower($post['oferta_estado_nombre'], 'UTF-8');
                                }
                                echo htmlspecialchars(!empty($detalle) ? implode(' · ', $detalle) : '—', ENT_QUOTES, 'UTF-8');
                                ?>
                            </p>
                        </div>
                        <div class="pos-card-etapa">
                            <span class="pos-card-etapa-label">Etapa del proceso</span>
                            <span class="pos-etapa <?php echo htmlspecialchars(pos_clase_etapa($post['etapa_nombre']), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($post['etapa_nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
