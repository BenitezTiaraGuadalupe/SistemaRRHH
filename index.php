<?php

/**
 * Punto de entrada del sistema.
 */
require_once __DIR__ . '/controllers/authController.php';
require_once __DIR__ . '/controllers/dashboardController.php';
require_once __DIR__ . '/controllers/solicitudesController.php';
require_once __DIR__ . '/controllers/usuariosController.php';
require_once __DIR__ . '/controllers/candidatosController.php';
require_once __DIR__ . '/controllers/ofertasController.php';
require_once __DIR__ . '/controllers/postulacionesController.php';

AuthController::iniciarSesion();

if (AuthController::estaLogueado()) {
    require_once __DIR__ . '/database.php';
    $pdo = $GLOBALS['pdo'];
    AuthController::refrescarPermisos($pdo);
}

$accion = isset($_GET['accion']) ? trim((string) $_GET['accion']) : '';
if ($accion === '') {
    if (AuthController::estaLogueado()) {
        header('Location: ' . AuthController::urlInicio());
        exit;
    }
    $accion = 'login';
}

if ($accion !== 'login' && !AuthController::estaLogueado()) {
    header('Location: index.php?accion=login');
    exit;
}

switch ($accion) {
    case 'login':
        (new AuthController())->handleLogin();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'dashboard':
        (new DashboardController())->index();
        break;
    case 'solicitudes':
        (new SolicitudesController())->index();
        break;
    case 'create':
        (new SolicitudesController())->create();
        break;
    case 'store':
        (new SolicitudesController())->store();
        break;
    case 'candidatos':
        (new CandidatosController())->index();
        break;
    case 'usuarios':
        (new UsuariosController())->index();
        break;
    case 'usuario_create':
        (new UsuariosController())->create();
        break;
    case 'usuario_store':
        (new UsuariosController())->store();
        break;
    case 'roles':
        (new UsuariosController())->roles();
        break;
    case 'roles_edit':
        (new UsuariosController())->rolesEdit();
        break;
    case 'roles_update':
        (new UsuariosController())->rolesUpdate();
        break;
    case 'ofertas':
        (new OfertasController())->index();
        break;
    case 'ofertas_create':
        (new OfertasController())->create();
        break;
    case 'ofertas_store':
        (new OfertasController())->store();
        break;
    case 'ofertas_edit':
        (new OfertasController())->edit();
        break;
    case 'ofertas_update':
        (new OfertasController())->update();
        break;
    case 'postulaciones':
        (new PostulacionesController())->index();
        break;
    case 'postulaciones_store':
        (new PostulacionesController())->store();
        break;
    default:
        (new DashboardController())->index();
        break;
}
