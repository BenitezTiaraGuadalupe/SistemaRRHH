<?php
/**
 * @var array $metricas  candidatos, busquedas, ofertas_activas, empresas
 */
require_once dirname(__DIR__, 2) . '/lib/paths.php';

$pageTitle = 'Dashboard — TalentLink';
$activeMenu = 'dashboard';
$moduleStylesheets = array(app_module_styles('dashboard'));

$nombreUsuario = '';
if (function_exists('auth_usuario_actual')) {
    $u = auth_usuario_actual();
    if ($u !== null) {
        $partes = array();
        if (!empty($u['nombre'])) {
            $partes[] = $u['nombre'];
        }
        if (!empty($u['apellido'])) {
            $partes[] = $u['apellido'];
        }
        $nombreUsuario = trim(implode(' ', $partes));
        if ($nombreUsuario === '') {
            $nombreUsuario = (string) $u['correo'];
        }
    }
}

$tarjetas = array(
    array(
        'valor' => $metricas['candidatos'],
        'etiqueta' => 'Candidatos',
        'descripcion' => 'Personas registradas en la plataforma',
    ),
    array(
        'valor' => $metricas['busquedas'],
        'etiqueta' => 'Solicitudes',
        'descripcion' => 'Búsquedas pedidas por empresas',
    ),
    array(
        'valor' => $metricas['ofertas_activas'],
        'etiqueta' => 'Ofertas activas',
        'descripcion' => 'Vacantes publicadas y vigentes',
    ),
    array(
        'valor' => $metricas['empresas'],
        'etiqueta' => 'Empresas',
        'descripcion' => 'Organizaciones registradas',
    ),
);

include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/side_bar.php';
include __DIR__ . '/../partials/navbar.php';
?>
<div class="tl-page">
    <div class="tl-dash-header">
        <h2 class="tl-dash-title">Resumen</h2>
        <?php if ($nombreUsuario !== '') : ?>
            <p class="tl-dash-greeting">Hola, <?php echo htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>

    <div class="tl-stats">
        <?php foreach ($tarjetas as $tarjeta) : ?>
            <article class="tl-stat-card">
                <p class="tl-stat-value"><?php echo (int) $tarjeta['valor']; ?></p>
                <h3 class="tl-stat-label"><?php echo htmlspecialchars($tarjeta['etiqueta'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="tl-stat-desc"><?php echo htmlspecialchars($tarjeta['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../partials/app_close.php'; ?>
