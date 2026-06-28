<?php
/**
 * Barra lateral del panel.
 * Variables opcionales: $activeMenu (ej. 'dashboard', 'solicitudes').
 */
require_once dirname(__DIR__, 2) . '/controllers/authController.php';

$activeMenu = isset($activeMenu) ? $activeMenu : '';

$itemsPrincipal = array(
    array(
        'key' => 'dashboard',
        'label' => 'Dashboard',
        'href' => 'index.php?accion=dashboard',
        'permiso' => 'dashboard.ver',
    ),
    array(
        'key' => 'solicitudes',
        'label' => 'Solicitudes',
        'href' => 'index.php?accion=solicitudes',
        'permiso' => 'solicitudes.ver',
    ),
    array(
        'key' => 'solicitud_create',
        'label' => 'Nueva solicitud',
        'href' => 'index.php?accion=create',
        'permiso' => 'solicitudes.crear',
    ),
    array(
        'key' => 'ofertas',
        'label' => 'Ofertas laborales',
        'href' => 'index.php?accion=ofertas',
        'permiso' => 'ofertas.ver',
    ),
    array(
        'key' => 'candidatos',
        'label' => 'Candidatos',
        'href' => 'index.php?accion=candidatos',
        'permiso' => 'candidatos.ver',
    ),
    array(
        'key' => 'postulaciones',
        'label' => 'Postulaciones',
        'href' => '#',
        'permiso' => 'postulaciones.ver',
    ),
    array(
        'key' => 'empresas',
        'label' => 'Empresas',
        'href' => '#',
        'permiso' => 'empresas.ver',
    ),
);

$itemsReportes = array(
    array(
        'key' => 'reportes',
        'label' => 'Estadísticas',
        'href' => '#',
        'permiso' => 'reportes.ver',
    ),
    array(
        'key' => 'usuarios',
        'label' => 'Usuarios',
        'href' => 'index.php?accion=usuarios',
        'permiso' => 'usuarios.ver',
    ),
);
?>
<aside class="sidebar" aria-label="Menú lateral">
    <a class="brand" href="index.php?accion=dashboard" aria-label="Inicio">
        <span class="brand-logo-wrap">
            <img class="brand-logo" src="public/images/logo.jpg" alt="TalentLink">
        </span>
    </a>

    <div class="section">PRINCIPAL</div>
    <nav class="menu" aria-label="Principal">
        <?php foreach ($itemsPrincipal as $item) : ?>
            <?php
            if (!AuthController::tienePermiso($item['permiso'])) {
                continue;
            }
            $claseActiva = $activeMenu === $item['key'] ? 'active' : '';
            ?>
            <a class="<?php echo $claseActiva; ?>" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="menu-label"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="section">REPORTES</div>
    <nav class="menu" aria-label="Reportes">
        <?php foreach ($itemsReportes as $item) : ?>
            <?php
            if (!AuthController::tienePermiso($item['permiso'])) {
                continue;
            }
            $claseActiva = $activeMenu === $item['key'] ? 'active' : '';
            ?>
            <a class="<?php echo $claseActiva; ?>" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="menu-label"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
