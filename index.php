<?php

/**
 * Punto de entrada del sistema.
 */
require_once __DIR__ . '/controllers/authController.php';
require_once __DIR__ . '/controllers/dashboardController.php';
require_once __DIR__ . '/controllers/solicitudesController.php';
require_once __DIR__ . '/controllers/usuariosController.php';
require_once __DIR__ . '/controllers/candidatosController.php';

AuthController::iniciarSesion();

if (AuthController::estaLogueado()) {
    require_once __DIR__ . '/database.php';
    AuthController::refrescarPermisos($pdo);
}

$accion = isset($_GET['accion']) ? (string) $_GET['accion'] : 'dashboard';

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
    default:
        (new DashboardController())->index();
        break;
}
