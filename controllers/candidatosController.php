<?php

/**
 * Gestión de candidatos.
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class CandidatosController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        AuthController::requerirPermiso('candidatos.ver');

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        $titulo = 'Candidatos';
        $candidatos = $this->listarCandidatos($pdo);

        include $this->viewsPath . '/candidatos/index.php';
    }

    private function listarCandidatos(PDO $pdo)
    {
        $sql = 'SELECT c.id, c.nombre, c.apellido, c.fecha_nac,
                       u.correo,
                       ci.nombre AS ciudad_nombre,
                       pr.nombre AS provincia_nombre
                FROM candidatos c
                INNER JOIN usuarios u ON u.id = c.usuarios_id
                LEFT JOIN ciudades ci ON ci.id = c.ciudades_id
                LEFT JOIN provincias pr ON pr.id = ci.provincias_id
                ORDER BY c.apellido ASC, c.nombre ASC';

        return $pdo->query($sql)->fetchAll();
    }
}
