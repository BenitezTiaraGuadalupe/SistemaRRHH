<?php

/**
 * Panel principal con métricas resumidas.
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class DashboardController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        AuthController::requerirPermiso('dashboard.ver');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $metricas = $this->obtenerMetricas($pdo);
        $usuario = AuthController::usuario();

        include $this->viewsPath . '/dashboard/dashboard.php';
    }

    private function obtenerMetricas(PDO $pdo)
    {
        return array(
            'candidatos' => (int) $pdo->query('SELECT COUNT(*) FROM candidatos')->fetchColumn(),
            'busquedas' => (int) $pdo->query('SELECT COUNT(*) FROM busquedas')->fetchColumn(),
            'ofertas_activas' => $this->contarOfertasActivas($pdo),
            'empresas' => (int) $pdo->query('SELECT COUNT(*) FROM empresas')->fetchColumn(),
        );
    }

    private function contarOfertasActivas(PDO $pdo)
    {
        $sql = 'SELECT COUNT(*) FROM ofertas o
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                WHERE LOWER(eo.nombre) LIKE ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array('%activ%'));
        return (int) $stmt->fetchColumn();
    }
}
