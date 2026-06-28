<?php

/**
 * Ofertas laborales publicadas a partir de búsquedas (solo personal RRHH gestiona).
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class OfertasController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        AuthController::requerirPermiso('ofertas.ver');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $esCandidato = AuthController::esRol('candidato');
        $titulo = $esCandidato ? 'Ofertas disponibles' : 'Ofertas laborales';
        $puedeGestionar = AuthController::tienePermiso('ofertas.crear');

        if ($esCandidato) {
            $ofertas = $this->listarOfertasFeed($pdo);
            include $this->viewsPath . '/ofertas/feed.php';
            return;
        }

        $ofertas = $this->listarOfertas($pdo);
        include $this->viewsPath . '/ofertas/index.php';
    }

    public function create()
    {
        $this->requerirRrhh('ofertas.crear');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $titulo = 'Publicar oferta';
        $busquedas = $this->listarBusquedasSinOferta($pdo);
        $estadosOferta = $pdo->query('SELECT id, nombre FROM estado_ofertas ORDER BY id ASC')->fetchAll();
        $busquedaPreseleccionada = isset($_GET['busquedas_id']) ? (int) $_GET['busquedas_id'] : 0;
        $desdeSolicitudes = isset($_GET['desde']) && $_GET['desde'] === 'solicitudes';

        if ($busquedaPreseleccionada > 0) {
            $busquedas = $this->asegurarBusquedaEnLista($pdo, $busquedas, $busquedaPreseleccionada);
        }

        include $this->viewsPath . '/ofertas/create.php';
    }

    public function store()
    {
        $this->requerirRrhh('ofertas.crear');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $volver = isset($_GET['busquedas_id']) ? '&busquedas_id=' . (int) $_GET['busquedas_id'] : '';
            header('Location: index.php?accion=ofertas_create' . $volver);
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $busquedaId = isset($_POST['busquedas_id']) ? (int) $_POST['busquedas_id'] : 0;
        $estadoId = isset($_POST['estado_ofertas_id']) ? (int) $_POST['estado_ofertas_id'] : 0;
        $desdeSolicitudes = isset($_POST['volver_solicitudes']) && $_POST['volver_solicitudes'] === '1';
        $errores = array();

        if ($busquedaId <= 0) {
            $errores['busquedas_id'] = 'Seleccioná una búsqueda.';
        }
        if ($estadoId <= 0) {
            $errores['estado_ofertas_id'] = 'Seleccioná un estado.';
        }

        $rrhhId = AuthController::personalRrhhDelUsuario($pdo);
        if ($rrhhId === null) {
            $errores['_general'] = 'Su usuario no está vinculado a un perfil de RRHH.';
        }

        if ($busquedaId > 0 && empty($errores['busquedas_id'])) {
            if (!$this->busquedaExiste($pdo, $busquedaId)) {
                $errores['busquedas_id'] = 'La búsqueda no existe.';
            } elseif ($this->busquedaTieneOferta($pdo, $busquedaId)) {
                $errores['busquedas_id'] = 'Esa búsqueda ya tiene una oferta publicada.';
            }
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirigirTrasErrorPublicar($busquedaId, $estadoId, $desdeSolicitudes);
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO ofertas (busquedas_id, personal_rrhh_id, estado_ofertas_id) VALUES (?, ?, ?)'
            );
            $stmt->execute(array($busquedaId, $rrhhId, $estadoId));
        } catch (Throwable $e) {
            $_SESSION['errores'] = array('_general' => 'No se pudo publicar la oferta.');
            $this->redirigirTrasErrorPublicar($busquedaId, $estadoId, $desdeSolicitudes);
        }

        $_SESSION['mensaje_exito'] = 'Oferta publicada correctamente.';
        header('Location: index.php?accion=ofertas');
        exit;
    }

    public function edit()
    {
        $this->requerirRrhh('ofertas.crear');

        $ofertaId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($ofertaId <= 0) {
            header('Location: index.php?accion=ofertas');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $oferta = $this->obtenerOferta($pdo, $ofertaId);
        if ($oferta === null) {
            $_SESSION['errores'] = array('_general' => 'Oferta no encontrada.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $titulo = 'Editar oferta';
        $estadosOferta = $pdo->query('SELECT id, nombre FROM estado_ofertas ORDER BY id ASC')->fetchAll();

        include $this->viewsPath . '/ofertas/edit.php';
    }

    public function update()
    {
        $this->requerirRrhh('ofertas.crear');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $ofertaId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $estadoId = isset($_POST['estado_ofertas_id']) ? (int) $_POST['estado_ofertas_id'] : 0;

        if ($ofertaId <= 0 || $estadoId <= 0) {
            header('Location: index.php?accion=ofertas');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        if ($this->obtenerOferta($pdo, $ofertaId) === null) {
            $_SESSION['errores'] = array('_general' => 'Oferta no encontrada.');
            header('Location: index.php?accion=ofertas');
            exit;
        }

        $stmt = $pdo->prepare('UPDATE ofertas SET estado_ofertas_id = ? WHERE id = ?');
        $stmt->execute(array($estadoId, $ofertaId));

        $_SESSION['mensaje_exito'] = 'Estado de la oferta actualizado.';
        if (isset($_POST['volver_solicitudes']) && $_POST['volver_solicitudes'] === '1') {
            header('Location: index.php?accion=solicitudes');
        } else {
            header('Location: index.php?accion=ofertas');
        }
        exit;
    }

    private function requerirRrhh($permiso)
    {
        AuthController::requerirPermiso($permiso);
        AuthController::requerirRol('admin');
    }

    private function listarOfertas(PDO $pdo)
    {
        $sql = 'SELECT o.id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eo.nombre AS estado_nombre,
                       CONCAT(pr.nombre, \' \', pr.apellido) AS referente_nombre,
                       db.cantidad_vacantes,
                       m.nombre AS modalidad_nombre
                FROM ofertas o
                INNER JOIN busquedas b ON b.id = o.busquedas_id
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                INNER JOIN personal_rrhh pr ON pr.id = o.personal_rrhh_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                WHERE 1=1';

        $params = array();

        if (AuthController::esRol('empresa')) {
            $empresaId = AuthController::empresaDelUsuario($pdo);
            if ($empresaId === null) {
                return array();
            }
            $sql .= ' AND b.empresas_id = ?';
            $params[] = $empresaId;
        } elseif (AuthController::esRol('candidato')) {
            $sql .= ' AND LOWER(eo.nombre) LIKE ?';
            $params[] = '%activ%';
        }

        $sql .= ' ORDER BY o.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function listarOfertasFeed(PDO $pdo)
    {
        $candidatoId = AuthController::candidatoDelUsuario($pdo);

        $sql = 'SELECT o.id,
                       b.id AS busqueda_id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       db.descripcion,
                       db.cantidad_vacantes,
                       db.anios_experiencia,
                       m.nombre AS modalidad_nombre,
                       ci.nombre AS ciudad_nombre,
                       pr.nombre AS provincia_nombre,
                       pa.nombre AS pais_nombre,
                       (SELECT GROUP_CONCAT(h.nombre ORDER BY h.nombre SEPARATOR \', \')
                        FROM habilidades_por_busqueda hpb
                        INNER JOIN habilidades h ON h.id = hpb.habilidades_id
                        WHERE hpb.busquedas_id = b.id) AS habilidades_nombres';

        if ($candidatoId !== null) {
            $sql .= ',
                       CASE WHEN EXISTS (
                           SELECT 1 FROM postulaciones po
                           INNER JOIN postulaciones_por_candidatos ppc ON ppc.postulaciones_id = po.id
                           WHERE po.ofertas_id = o.id AND ppc.candidatos_id = ?
                       ) THEN 1 ELSE 0 END AS ya_postulado';
        } else {
            $sql .= ', 0 AS ya_postulado';
        }

        $sql .= '
                FROM ofertas o
                INNER JOIN busquedas b ON b.id = o.busquedas_id
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                LEFT JOIN ciudades ci ON ci.id = db.ciudades_id
                LEFT JOIN provincias pr ON pr.id = db.provincias_id
                LEFT JOIN paises pa ON pa.id = db.paises_id
                WHERE LOWER(eo.nombre) LIKE ?
                ORDER BY o.id DESC';

        $params = array();
        if ($candidatoId !== null) {
            $params[] = $candidatoId;
        }
        $params[] = '%activ%';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function redirigirTrasErrorPublicar($busquedaId, $estadoId, $desdeSolicitudes)
    {
        $_SESSION['old'] = array(
            'busquedas_id' => $busquedaId,
            'estado_ofertas_id' => $estadoId,
        );

        if ($desdeSolicitudes) {
            header('Location: index.php?accion=solicitudes');
            exit;
        }

        $volver = $busquedaId > 0 ? '&busquedas_id=' . $busquedaId : '';
        header('Location: index.php?accion=ofertas_create' . $volver);
        exit;
    }

    private function asegurarBusquedaEnLista(PDO $pdo, array $busquedas, $busquedaId)
    {
        foreach ($busquedas as $b) {
            if ((int) $b['id'] === $busquedaId) {
                return $busquedas;
            }
        }

        if ($this->busquedaTieneOferta($pdo, $busquedaId)) {
            return $busquedas;
        }

        $extra = $this->obtenerBusquedaResumen($pdo, $busquedaId);
        if ($extra !== null) {
            array_unshift($busquedas, $extra);
        }

        return $busquedas;
    }

    private function obtenerBusquedaResumen(PDO $pdo, $busquedaId)
    {
        $sql = 'SELECT b.id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eb.nombre AS estado_nombre,
                       db.cantidad_vacantes,
                       m.nombre AS modalidad_nombre
                FROM busquedas b
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_busqueda eb ON eb.id = b.estado_busqueda_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                WHERE b.id = ?
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($busquedaId));
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    private function listarBusquedasSinOferta(PDO $pdo)
    {
        $sql = 'SELECT b.id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eb.nombre AS estado_nombre,
                       db.cantidad_vacantes,
                       m.nombre AS modalidad_nombre
                FROM busquedas b
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_busqueda eb ON eb.id = b.estado_busqueda_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                LEFT JOIN ofertas o ON o.busquedas_id = b.id
                WHERE o.id IS NULL
                ORDER BY b.id DESC';

        return $pdo->query($sql)->fetchAll();
    }

    private function busquedaExiste(PDO $pdo, $busquedaId)
    {
        $stmt = $pdo->prepare('SELECT id FROM busquedas WHERE id = ? LIMIT 1');
        $stmt->execute(array($busquedaId));
        return $stmt->fetchColumn() !== false;
    }

    private function busquedaTieneOferta(PDO $pdo, $busquedaId)
    {
        $stmt = $pdo->prepare('SELECT id FROM ofertas WHERE busquedas_id = ? LIMIT 1');
        $stmt->execute(array($busquedaId));
        return $stmt->fetchColumn() !== false;
    }

    private function obtenerOferta(PDO $pdo, $ofertaId)
    {
        $sql = 'SELECT o.id,
                       o.estado_ofertas_id,
                       b.id AS busqueda_id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eo.nombre AS estado_nombre,
                       db.descripcion,
                       db.cantidad_vacantes,
                       db.anios_experiencia,
                       m.nombre AS modalidad_nombre
                FROM ofertas o
                INNER JOIN busquedas b ON b.id = o.busquedas_id
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_ofertas eo ON eo.id = o.estado_ofertas_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                WHERE o.id = ?
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($ofertaId));
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
