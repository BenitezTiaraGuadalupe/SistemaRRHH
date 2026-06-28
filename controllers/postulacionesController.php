<?php

/**
 * Postulaciones de candidatos a ofertas laborales.
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class PostulacionesController
{
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
        header('Location: index.php?accion=ofertas');
        exit;
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
