<?php
/**
 * Barra lateral del panel.
 * Espera: $activeMenu (string|null, opcional).
 */
require_once dirname(__DIR__, 2) . '/lib/view_helpers.php';

$activeMenu = isset($activeMenu) ? $activeMenu : null;
$appBase = app_web_base();
$menu = view_menu_items();
?>
<aside class="sidebar" aria-label="Menú lateral">
    <a class="brand" href="<?php echo htmlspecialchars($appBase . '/index.php?accion=dashboard', ENT_QUOTES, 'UTF-8'); ?>" aria-label="Inicio">
        <span class="brand-logo-wrap">
            <img class="brand-logo" src="<?php echo htmlspecialchars(app_url('public/images/logo.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="TalentLink">
        </span>
    </a>

    <?php if (!empty($menu['principal'])) : ?>
        <div class="section">PRINCIPAL</div>
        <nav class="menu" aria-label="Principal">
            <?php foreach ($menu['principal'] as $it) : ?>
                <a class="<?php echo $activeMenu === $it['key'] ? 'active' : ''; ?>"
                   href="<?php echo htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="menu-icon" aria-hidden="true"><?php echo view_icono($it['key']); ?></span>
                    <span class="menu-label"><?php echo htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php if (!empty($menu['reportes'])) : ?>
        <div class="section">REPORTES</div>
        <nav class="menu" aria-label="Reportes">
            <?php foreach ($menu['reportes'] as $it) : ?>
                <a class="<?php echo $activeMenu === $it['key'] ? 'active' : ''; ?>"
                   href="<?php echo htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="menu-icon" aria-hidden="true"><?php echo view_icono($it['key']); ?></span>
                    <span class="menu-label"><?php echo htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</aside>
