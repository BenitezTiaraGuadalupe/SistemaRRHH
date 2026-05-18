<?php

/**
 * Punto de entrada provisional. Replazable por rutas/front controller después.
 *
 * Acciones públicas: login.
 * Acciones privadas: requieren sesión activa (auth_requerir_login).
 */
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/controllers/authController.php';
require_once __DIR__ . '/controllers/solicitudesController.php';

auth_iniciar();

$accion = isset($_GET['accion']) ? (string) $_GET['accion'] : 'index';

$accionesPublicas = array('login');

if (!in_array($accion, $accionesPublicas, true) && !auth_logueado()) {
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
    case 'create':
        (new SolicitudesController())->create();
        break;
    case 'store':
        (new SolicitudesController())->store();
        break;
    case 'index':
    default:
        (new SolicitudesController())->index();
        break;
}
