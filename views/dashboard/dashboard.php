<?php
/**
 * @var array $metricas  candidatos, busquedas, ofertas_activas, empresas
 * @var array|null $usuario
 */
$pageTitle = 'Dashboard — TalentLink';
$activeMenu = 'dashboard';

$nombreUsuario = '';
if ($usuario !== null) {
    $partes = array();
    if (!empty($usuario['nombre'])) {
        $partes[] = $usuario['nombre'];
    }
    if (!empty($usuario['apellido'])) {
        $partes[] = $usuario['apellido'];
    }
    $nombreUsuario = trim(implode(' ', $partes));
    if ($nombreUsuario === '') {
        $nombreUsuario = (string) $usuario['correo'];
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="views/partials/app_styles.css">
    <link rel="stylesheet" href="views/dashboard/dashboard_styles.css">
</head>
<body>
<div class="app">
<?php
include __DIR__ . '/../partials/sidebar.php';
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
    </main>
</div>
</div>
</body>
</html>
