<?php
/**
 * Layout principal: sidebar + navbar + main.
 * Espera del controlador:
 *   - $content     (string, ya renderizado con ob_start/ob_get_clean)
 *   - $pageTitle   (string, opcional)
 *   - $activeMenu  (string, opcional, key del item activo: dashboard, solicitudes, ofertas, etc.)
 *   - $stylesheet  (string, opcional)
 *
 * @var string      $content
 * @var string|null $pageTitle
 * @var string|null $activeMenu
 * @var string|null $stylesheet
 */
require_once dirname(__DIR__) . '/lib/paths.php';

$usuarioActual = function_exists('auth_usuario_actual') ? auth_usuario_actual() : null;
$activeMenu = isset($activeMenu) ? $activeMenu : null;
$appBase = app_web_base();

$menuPrincipal = array(
    array('key' => 'dashboard',     'label' => 'Dashboard',         'href' => $appBase . '/index.php?accion=index',  'permiso' => 'dashboard.ver'),
    array('key' => 'solicitudes',   'label' => 'Solicitudes',       'href' => $appBase . '/index.php?accion=index',  'permiso' => 'solicitudes.ver'),
    array('key' => 'ofertas',       'label' => 'Ofertas laborales', 'href' => '#',                                   'permiso' => 'ofertas.ver'),
    array('key' => 'candidatos',    'label' => 'Candidatos',        'href' => $appBase . '/candidatos/index.php',  'permiso' => 'candidatos.ver'),
    array('key' => 'postulaciones', 'label' => 'Postulaciones',     'href' => '#',                       'permiso' => 'postulaciones.ver'),
    array('key' => 'empresas',      'label' => 'Empresas',          'href' => '#',                       'permiso' => 'empresas.ver'),
);

$menuReportes = array(
    array('key' => 'reportes', 'label' => 'Estadísticas', 'href' => '#', 'permiso' => 'reportes.ver'),
    array('key' => 'usuarios', 'label' => 'Usuarios',     'href' => '#', 'permiso' => 'usuarios.ver'),
);

$puedeVer = function ($permiso) {
    return function_exists('auth_tiene_permiso') && auth_tiene_permiso($permiso);
};

$visiblesPrincipal = array_values(array_filter($menuPrincipal, function ($it) use ($puedeVer) {
    return $puedeVer($it['permiso']);
}));
$visiblesReportes = array_values(array_filter($menuReportes, function ($it) use ($puedeVer) {
    return $puedeVer($it['permiso']);
}));

$nombreVisible = '';
$rolVisible = '';
$iniciales = '';
if ($usuarioActual !== null) {
    $partes = array();
    if (!empty($usuarioActual['nombre'])) {
        $partes[] = $usuarioActual['nombre'];
    }
    if (!empty($usuarioActual['apellido'])) {
        $partes[] = $usuarioActual['apellido'];
    }
    $nombreVisible = trim(implode(' ', $partes));
    if ($nombreVisible === '') {
        $nombreVisible = (string) $usuarioActual['correo'];
    }
    $rolesLegibles = array(
        'admin' => 'Personal RRHH',
        'empresa' => 'Empresa',
        'candidato' => 'Candidato',
    );
    $rolKey = strtolower((string) $usuarioActual['rol_nombre']);
    $rolVisible = isset($rolesLegibles[$rolKey]) ? $rolesLegibles[$rolKey] : ucfirst($usuarioActual['rol_nombre']);
    foreach ($partes as $p) {
        $iniciales .= mb_substr($p, 0, 1, 'UTF-8');
    }
    if ($iniciales === '') {
        $iniciales = mb_substr($nombreVisible, 0, 1, 'UTF-8');
    }
    $iniciales = mb_strtoupper($iniciales, 'UTF-8');
}

$tituloNavbar = '';
if (isset($pageTitle)) {
    $tituloNavbar = preg_replace('/\s*—.*$/u', '', (string) $pageTitle);
}

/**
 * Devuelve un SVG inline para el menú (tamaño fijo por si falla el CSS).
 */
function layout_icono($key)
{
    $svgAttrs = 'width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $svgs = array(
        'dashboard'     => '<svg ' . $svgAttrs . '><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
        'solicitudes'   => '<svg ' . $svgAttrs . '><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>',
        'ofertas'       => '<svg ' . $svgAttrs . '><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/></svg>',
        'candidatos'    => '<svg ' . $svgAttrs . '><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>',
        'postulaciones' => '<svg ' . $svgAttrs . '><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>',
        'empresas'      => '<svg ' . $svgAttrs . '><rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h2M9 11h2M9 15h2M13 7h2M13 11h2M13 15h2"/></svg>',
        'reportes'      => '<svg ' . $svgAttrs . '><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>',
        'usuarios'      => '<svg ' . $svgAttrs . '><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6"/><circle cx="17" cy="9" r="2.5"/><path d="M21.5 18.5c0-2.2-2-4-4.5-4"/></svg>',
        'logout'        => '<svg ' . $svgAttrs . '><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>',
    );
    return isset($svgs[$key]) ? $svgs[$key] : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'TalentLink'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($stylesheet) ? htmlspecialchars($stylesheet, ENT_QUOTES, 'UTF-8') : htmlspecialchars(app_url('views/layoutStyles.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="app">
        <aside class="sidebar" aria-label="Menú lateral">
            <a class="brand" href="<?php echo htmlspecialchars($appBase . '/index.php?accion=index', ENT_QUOTES, 'UTF-8'); ?>" aria-label="Inicio">
                <span class="brand-logo-wrap">
                    <img class="brand-logo" src="<?php echo htmlspecialchars($appBase . '/public/images/logo.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="TalentLink">
                </span>
            </a>

            <?php if (!empty($visiblesPrincipal)) : ?>
                <div class="section">PRINCIPAL</div>
                <nav class="menu" aria-label="Principal">
                    <?php foreach ($visiblesPrincipal as $it) : ?>
                        <a class="<?php echo $activeMenu === $it['key'] ? 'active' : ''; ?>"
                           href="<?php echo htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="menu-icon" aria-hidden="true"><?php echo layout_icono($it['key']); ?></span>
                            <span class="menu-label"><?php echo htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php if (!empty($visiblesReportes)) : ?>
                <div class="section">REPORTES</div>
                <nav class="menu" aria-label="Reportes">
                    <?php foreach ($visiblesReportes as $it) : ?>
                        <a class="<?php echo $activeMenu === $it['key'] ? 'active' : ''; ?>"
                           href="<?php echo htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="menu-icon" aria-hidden="true"><?php echo layout_icono($it['key']); ?></span>
                            <span class="menu-label"><?php echo htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </aside>

        <div class="content">
            <nav class="navbar" aria-label="Barra superior">
                <div class="navbar-inner">
                    <div class="navbar-title">
                        <span class="page-eyebrow">Panel</span>
                        <?php if ($tituloNavbar !== '') : ?>
                            <span class="page-title"><?php echo htmlspecialchars($tituloNavbar, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($usuarioActual !== null) : ?>
                        <div class="session">
                            <div class="user-pill" title="<?php echo htmlspecialchars($usuarioActual['correo'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="user-info">
                                    <span class="user"><?php echo htmlspecialchars($nombreVisible, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="user-role"><?php echo htmlspecialchars($rolVisible, ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </div>
                            <a class="logout" href="<?php echo htmlspecialchars($appBase . '/index.php?accion=logout', ENT_QUOTES, 'UTF-8'); ?>" title="Cerrar sesión">
                                <span class="logout-icon" aria-hidden="true"><?php echo layout_icono('logout'); ?></span>
                                <span class="logout-text">Salir</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
            <main class="main">
                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>
