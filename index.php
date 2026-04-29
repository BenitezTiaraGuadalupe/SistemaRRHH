<?php

/**
 * Punto de entrada provisional. Replazable por rutas/front controller después.
 */
require_once __DIR__ . '/controllers/solicitudesController.php';

$controlador = new SolicitudesController();
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'index';

if ($accion === 'create') {
    $controlador->create();
} else {
    $controlador->index();
}
