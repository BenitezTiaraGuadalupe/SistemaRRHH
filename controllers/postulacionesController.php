<?php

/**
 * Postulaciones de candidatos a ofertas laborales.
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class PostulacionesController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        AuthController::requerirPermiso('postulaciones.ver');

        if (!AuthController::esRol('candidato')) {
            http_response_code(403);
            echo 'Esta sección está disponible solo para candidatos.';
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $candidatoId = AuthController::candidatoDelUsuario($pdo);
        if ($candidatoId === null) {
            http_response_code(403);
            echo 'Su cuenta no está vinculada a un perfil de candidato.';
            exit;
        }

        $titulo = 'Mis postulaciones';
        $postulaciones = $this->listarPorCandidato($pdo, $candidatoId);

        include $this->viewsPath . '/postulaciones/index.php';
    }

    public function store()
    {
        AuthController::requerirPermiso('postulaciones.crear');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $ofertaId = isset($_POST['oferta_id']) ? (int) $_POST['oferta_id'] : 0;
        if ($ofertaId <= 0) {
            header('Location: index.php?accion=ofertas');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $candidatoId = AuthController::candidatoDelUsuario($pdo);
        if ($candidatoId === null) {
            $_SESSION['errores'] = array('_general' => 'Su cuenta no está vinculada a un perfil de candidato.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        if (!$this->ofertaActiva($pdo, $ofertaId)) {
            $_SESSION['errores'] = array('_general' => 'La oferta no está disponible para postularse.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        if ($this->candidatoYaPostulado($pdo, $ofertaId, $candidatoId)) {
            $_SESSION['errores'] = array('_general' => 'Ya te postulaste a esta oferta.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $etapaId = $this->primeraEtapaId($pdo);
        if ($etapaId === null) {
            $_SESSION['errores'] = array('_general' => 'No hay etapas configuradas en el sistema.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO postulaciones (ofertas_id, etapas_id) VALUES (?, ?)'
            );
            $stmt->execute(array($ofertaId, $etapaId));
            $postulacionId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                'INSERT INTO postulaciones_por_candidatos (postulaciones_id, candidatos_id) VALUES (?, ?)'
            );
            $stmt->execute(array($postulacionId, $candidatoId));

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['errores'] = array('_general' => 'No se pudo registrar la postulación.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $_SESSION['mensaje_exito'] = 'Postulación enviada correctamente.';
        header('Location: index.php?accion=postulaciones');
        exit;
    }

    private function listarPorCandidato(PDO $pdo, $candidatoId)
    {
        $sql = 'SELECT p.id,
                       et.nombre AS etapa_nombre,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eo.nombre AS oferta_estado_nombre,
                       m.nombre AS modalidad_nombre
                FROM postulaciones p
                INNER JOIN postulaciones_por_candidatos ppc ON ppc.postulaciones_id = p.id
                INNER JOIN etapas et ON et.id = p.etapas_id
                INNER JOIN ofertas o ON o.id = p.ofertas_id
                INNER JOIN busquedas b ON b.id = o.busquedas_id
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                WHERE ppc.candidatos_id = ?
                ORDER BY p.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($candidatoId));
        return $stmt->fetchAll();
    }

    private function ofertaActiva(PDO $pdo, $ofertaId)
    {
        $sql = 'SELECT o.id FROM ofertas o
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                WHERE o.id = ? AND LOWER(eo.nombre) LIKE ?
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($ofertaId, '%activ%'));
        return $stmt->fetchColumn() !== false;
    }

    private function candidatoYaPostulado(PDO $pdo, $ofertaId, $candidatoId)
    {
        $sql = 'SELECT p.id FROM postulaciones p
                INNER JOIN postulaciones_por_candidatos pc ON pc.postulaciones_id = p.id
                WHERE p.ofertas_id = ? AND pc.candidatos_id = ?
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($ofertaId, $candidatoId));
        return $stmt->fetchColumn() !== false;
    }

    private function primeraEtapaId(PDO $pdo)
    {
        $id = $pdo->query('SELECT id FROM etapas ORDER BY id ASC LIMIT 1')->fetchColumn();
        return $id === false ? null : (int) $id;
    }
}
