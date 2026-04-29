<?php

/**
 * Gestión de solicitudes (ej. vacantes pedidas desde RRHH).
 * PHP plano: incluye vistas y asigna $content antes del layout.
 */
class SolicitudesController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        $pageTitle = 'Solicitudes — TalentLink';
        $titulo = 'Solicitudes';
        $solicitudes = array();

        ob_start();
        include $this->viewsPath . '/solicitudes/index.php';
        $content = ob_get_clean();

        include $this->viewsPath . '/layout.php';
    }

    public function create()
    {
        $pageTitle = 'Nueva solicitud — TalentLink';
        $titulo = 'Nueva solicitud';

        ob_start();
        include $this->viewsPath . '/solicitudes/createSolicitudes.php';
        $content = ob_get_clean();

        include $this->viewsPath . '/layout.php';
    }
}
