<?php

/**
 * Helpers compartidos para partials de vistas (menú, íconos, sesión).
 */
require_once __DIR__ . '/paths.php';

/**
 * SVG inline para el menú (tamaño fijo por si falla el CSS).
 */
function view_icono($key)
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

/**
 * @return array{principal: array, reportes: array}
 */
function view_menu_items()
{
    $appBase = app_web_base();

    $principal = array(
        array('key' => 'dashboard',     'label' => 'Dashboard',         'href' => $appBase . '/index.php?accion=dashboard',   'permiso' => 'dashboard.ver'),
        array('key' => 'solicitudes',   'label' => 'Solicitudes',       'href' => $appBase . '/index.php?accion=solicitudes', 'permiso' => 'solicitudes.ver'),
        array('key' => 'ofertas',       'label' => 'Ofertas laborales', 'href' => '#',                                   'permiso' => 'ofertas.ver'),
        array('key' => 'candidatos',    'label' => 'Candidatos',        'href' => $appBase . '/candidatos/index.php',  'permiso' => 'candidatos.ver'),
        array('key' => 'postulaciones', 'label' => 'Postulaciones',     'href' => '#',                       'permiso' => 'postulaciones.ver'),
        array('key' => 'empresas',      'label' => 'Empresas',          'href' => '#',                       'permiso' => 'empresas.ver'),
    );

    $reportes = array(
        array('key' => 'reportes', 'label' => 'Estadísticas', 'href' => '#', 'permiso' => 'reportes.ver'),
        array('key' => 'usuarios', 'label' => 'Usuarios',     'href' => '#', 'permiso' => 'usuarios.ver'),
    );

    $puedeVer = function ($permiso) {
        return function_exists('auth_tiene_permiso') && auth_tiene_permiso($permiso);
    };

    return array(
        'principal' => array_values(array_filter($principal, function ($it) use ($puedeVer) {
            return $puedeVer($it['permiso']);
        })),
        'reportes' => array_values(array_filter($reportes, function ($it) use ($puedeVer) {
            return $puedeVer($it['permiso']);
        })),
    );
}

/**
 * Datos del usuario para la navbar.
 *
 * @return array{nombre: string, rol: string, iniciales: string, correo: string}|null
 */
function view_usuario_navbar()
{
    if (!function_exists('auth_usuario_actual')) {
        return null;
    }
    $usuario = auth_usuario_actual();
    if ($usuario === null) {
        return null;
    }

    $partes = array();
    if (!empty($usuario['nombre'])) {
        $partes[] = $usuario['nombre'];
    }
    if (!empty($usuario['apellido'])) {
        $partes[] = $usuario['apellido'];
    }

    $nombre = trim(implode(' ', $partes));
    if ($nombre === '') {
        $nombre = (string) $usuario['correo'];
    }

    $rolesLegibles = array(
        'admin' => 'Personal RRHH',
        'empresa' => 'Empresa',
        'candidato' => 'Candidato',
    );
    $rolKey = strtolower((string) $usuario['rol_nombre']);
    $rol = isset($rolesLegibles[$rolKey]) ? $rolesLegibles[$rolKey] : ucfirst($usuario['rol_nombre']);

    $iniciales = '';
    foreach ($partes as $p) {
        $iniciales .= mb_substr($p, 0, 1, 'UTF-8');
    }
    if ($iniciales === '') {
        $iniciales = mb_substr($nombre, 0, 1, 'UTF-8');
    }

    return array(
        'nombre' => $nombre,
        'rol' => $rol,
        'iniciales' => mb_strtoupper($iniciales, 'UTF-8'),
        'correo' => (string) $usuario['correo'],
    );
}

/**
 * Título corto para la navbar (sin sufijo "— TalentLink").
 */
function view_titulo_navbar($pageTitle)
{
    return preg_replace('/\s*—.*$/u', '', (string) $pageTitle);
}

/**
 * @return string[]
 */
function view_stylesheets(array $moduleStylesheets = array())
{
    return array_merge(
        array(app_url('views/partials/app_styles.css')),
        $moduleStylesheets
    );
}
