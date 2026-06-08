<?php
/**
 * Barra superior del panel.
 * Espera: $pageTitle (opcional).
 */
require_once dirname(__DIR__, 2) . '/lib/view_helpers.php';

$appBase = app_web_base();
$tituloNavbar = isset($pageTitle) ? view_titulo_navbar($pageTitle) : '';
$usuarioNav = view_usuario_navbar();
?>
<div class="content">
    <nav class="navbar" aria-label="Barra superior">
        <div class="navbar-inner">
            <div class="navbar-title">
                <span class="page-eyebrow">Panel</span>
                <?php if ($tituloNavbar !== '') : ?>
                    <span class="page-title"><?php echo htmlspecialchars($tituloNavbar, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($usuarioNav !== null) : ?>
                <div class="session">
                    <div class="user-pill" title="<?php echo htmlspecialchars($usuarioNav['correo'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars($usuarioNav['iniciales'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="user-info">
                            <span class="user"><?php echo htmlspecialchars($usuarioNav['nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($usuarioNav['rol'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    </div>
                    <a class="logout" href="<?php echo htmlspecialchars($appBase . '/index.php?accion=logout', ENT_QUOTES, 'UTF-8'); ?>" title="Cerrar sesión">
                        <span class="logout-icon" aria-hidden="true"><?php echo view_icono('logout'); ?></span>
                        <span class="logout-text">Salir</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <main class="main">
