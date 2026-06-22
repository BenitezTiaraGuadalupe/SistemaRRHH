<?php

/**
 * Gestión de solicitudes (ej. vacantes pedidas desde RRHH).
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class SolicitudesController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        AuthController::requerirPermiso('solicitudes.ver');

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        $titulo = 'Solicitudes de personal';
        $solicitudes = $this->listarSolicitudes($pdo);

        include $this->viewsPath . '/solicitudes/index.php';
    }

    private function listarSolicitudes(PDO $pdo)
    {
        $sql = 'SELECT b.id,
                       b.nombre_puesto,
                       e.nombre AS empresa_nombre,
                       eb.nombre AS estado_nombre,
                       db.cantidad_vacantes,
                       db.anios_experiencia,
                       m.nombre AS modalidad_nombre,
                       ci.nombre AS ciudad_nombre,
                       pr.nombre AS provincia_nombre,
                       pa.nombre AS pais_nombre
                FROM busquedas b
                INNER JOIN empresas e ON e.id = b.empresas_id
                INNER JOIN estado_busqueda eb ON eb.id = b.estado_busqueda_id
                LEFT JOIN detalle_busquedas db ON db.busquedas_id = b.id
                LEFT JOIN modalidades m ON m.id = db.modalidades_id
                LEFT JOIN ciudades ci ON ci.id = db.ciudades_id
                LEFT JOIN provincias pr ON pr.id = db.provincias_id
                LEFT JOIN paises pa ON pa.id = db.paises_id
                ORDER BY b.id DESC';

        return $pdo->query($sql)->fetchAll();
    }

    public function create()
    {
        AuthController::requerirPermiso('solicitudes.crear');

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        $titulo = 'Nueva solicitud';
        $esEmpresa = AuthController::esRol('empresa');
        $empresaUsuario = null;

        if ($esEmpresa) {
            $empresaId = AuthController::empresaDelUsuario($pdo);
            if ($empresaId === null) {
                http_response_code(403);
                echo 'Su cuenta de empresa no está vinculada a una organización.';
                exit;
            }
            $stmt = $pdo->prepare('SELECT id, nombre FROM empresas WHERE id = ? LIMIT 1');
            $stmt->execute(array($empresaId));
            $empresaUsuario = $stmt->fetch();
            $empresas = $empresaUsuario !== false ? array($empresaUsuario) : array();
        } else {
            $empresas = $pdo->query('SELECT id, nombre FROM empresas ORDER BY nombre ASC')->fetchAll();
        }

        $estadosBusqueda = $pdo->query('SELECT id, nombre FROM estado_busqueda ORDER BY nombre ASC')->fetchAll();
        $modalidades = $pdo->query('SELECT id, nombre FROM modalidades ORDER BY nombre ASC')->fetchAll();
        $paises = $pdo->query('SELECT id, nombre FROM paises ORDER BY nombre ASC')->fetchAll();
        $provincias = $pdo->query('SELECT id, nombre, paises_id FROM provincias ORDER BY nombre ASC')->fetchAll();
        $ciudades = $pdo->query('SELECT id, nombre, provincias_id FROM ciudades ORDER BY nombre ASC')->fetchAll();
        $habilidades = $pdo->query('SELECT id, nombre FROM habilidades ORDER BY nombre ASC')->fetchAll();

        include $this->viewsPath . '/solicitudes/createSolicitudes.php';
    }

    public function store()
    {
        AuthController::requerirPermiso('solicitudes.crear');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=create');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        $datos = $this->extraerDatosPost();
        $datos = $this->aplicarRestriccionEmpresa($pdo, $datos);
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos['old'];
            header('Location: index.php?accion=create');
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO busquedas (nombre_puesto, empresas_id, estado_busqueda_id) VALUES (?, ?, ?)'
            );
            $stmt->execute(array(
                $datos['nombre_puesto'],
                $datos['empresas_id'],
                $datos['estado_busqueda_id'],
            ));
            $busquedaId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                'INSERT INTO detalle_busquedas
                    (busquedas_id, descripcion, cantidad_vacantes, anios_experiencia,
                     modalidades_id, ciudades_id, provincias_id, paises_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute(array(
                $busquedaId,
                $datos['descripcion'],
                $datos['cantidad_vacantes'],
                $datos['anios_experiencia'],
                $datos['modalidades_id'],
                $datos['ciudades_id'],
                $datos['provincias_id'],
                $datos['paises_id'],
            ));

            $habIds = $datos['habilidades_ids'];

            if (!empty($datos['habilidades_nuevas'])) {
                $selHab = $pdo->prepare(
                    'SELECT id FROM habilidades WHERE LOWER(nombre) = LOWER(?) LIMIT 1'
                );
                $insHab = $pdo->prepare('INSERT INTO habilidades (nombre) VALUES (?)');
                foreach ($datos['habilidades_nuevas'] as $nombre) {
                    $selHab->execute(array($nombre));
                    $existente = $selHab->fetchColumn();
                    if ($existente !== false) {
                        $habIds[] = (int) $existente;
                    } else {
                        $insHab->execute(array($nombre));
                        $habIds[] = (int) $pdo->lastInsertId();
                    }
                }
            }

            $habIds = array_values(array_unique($habIds));
            if (!empty($habIds)) {
                $insVin = $pdo->prepare(
                    'INSERT INTO habilidades_por_busqueda (busquedas_id, habilidades_id) VALUES (?, ?)'
                );
                foreach ($habIds as $hid) {
                    $insVin->execute(array($busquedaId, $hid));
                }
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['errores'] = array('_general' => 'No se pudo registrar la solicitud. Intentá nuevamente.');
            $_SESSION['old'] = $datos['old'];
            header('Location: index.php?accion=create');
            exit;
        }

        $_SESSION['mensaje_exito'] = 'Solicitud registrada correctamente.';
        if (AuthController::tienePermiso('solicitudes.ver')) {
            header('Location: index.php?accion=solicitudes');
        } else {
            header('Location: index.php?accion=dashboard');
        }
        exit;
    }

    /**
     * Si el usuario es empresa, fuerza empresas_id a su propia organización.
     */
    private function aplicarRestriccionEmpresa(PDO $pdo, array $datos)
    {
        if (!AuthController::esRol('empresa')) {
            return $datos;
        }

        $empresaId = AuthController::empresaDelUsuario($pdo);
        if ($empresaId !== null) {
            $datos['empresas_id'] = $empresaId;
            $datos['old']['empresas_id'] = $empresaId;
        }

        return $datos;
    }

    private function extraerDatosPost()
    {
        $habIds = isset($_POST['habilidades_ids']) && is_array($_POST['habilidades_ids'])
            ? $_POST['habilidades_ids'] : array();
        $habNuevas = isset($_POST['habilidades_nuevas']) && is_array($_POST['habilidades_nuevas'])
            ? $_POST['habilidades_nuevas'] : array();

        $habIdsLimpias = array();
        foreach ($habIds as $v) {
            $i = (int) $v;
            if ($i > 0) {
                $habIdsLimpias[] = $i;
            }
        }

        $habNuevasLimpias = array();
        foreach ($habNuevas as $v) {
            $s = trim((string) $v);
            if ($s !== '') {
                $habNuevasLimpias[] = $s;
            }
        }

        $descripcion = trim((string) (isset($_POST['descripcion']) ? $_POST['descripcion'] : ''));

        $datos = array(
            'nombre_puesto' => trim((string) (isset($_POST['nombre_puesto']) ? $_POST['nombre_puesto'] : '')),
            'empresas_id' => $this->parseIntOrNull(isset($_POST['empresas_id']) ? $_POST['empresas_id'] : null),
            'estado_busqueda_id' => $this->parseIntOrNull(isset($_POST['estado_busqueda_id']) ? $_POST['estado_busqueda_id'] : null),
            'cantidad_vacantes' => $this->parseIntOrNull(isset($_POST['cantidad_vacantes']) ? $_POST['cantidad_vacantes'] : null),
            'anios_experiencia' => $this->parseIntOrNull(isset($_POST['anios_experiencia']) ? $_POST['anios_experiencia'] : null),
            'modalidades_id' => $this->parseIntOrNull(isset($_POST['modalidades_id']) ? $_POST['modalidades_id'] : null),
            'paises_id' => $this->parseIntOrNull(isset($_POST['paises_id']) ? $_POST['paises_id'] : null),
            'provincias_id' => $this->parseIntOrNull(isset($_POST['provincias_id']) ? $_POST['provincias_id'] : null),
            'ciudades_id' => $this->parseIntOrNull(isset($_POST['ciudades_id']) ? $_POST['ciudades_id'] : null),
            'descripcion' => $descripcion === '' ? null : $descripcion,
            'habilidades_ids' => $habIdsLimpias,
            'habilidades_nuevas' => $habNuevasLimpias,
        );

        $datos['old'] = array(
            'nombre_puesto' => $datos['nombre_puesto'],
            'empresas_id' => $datos['empresas_id'],
            'estado_busqueda_id' => $datos['estado_busqueda_id'],
            'cantidad_vacantes' => $datos['cantidad_vacantes'],
            'anios_experiencia' => $datos['anios_experiencia'],
            'modalidades_id' => $datos['modalidades_id'],
            'paises_id' => $datos['paises_id'],
            'provincias_id' => $datos['provincias_id'],
            'ciudades_id' => $datos['ciudades_id'],
            'descripcion' => $datos['descripcion'] === null ? '' : $datos['descripcion'],
            'habilidades_ids' => $datos['habilidades_ids'],
            'habilidades_nuevas' => $datos['habilidades_nuevas'],
        );

        return $datos;
    }

    private function parseIntOrNull($v)
    {
        if ($v === null || $v === '') {
            return null;
        }
        return (int) $v;
    }

    private function validar(array $d)
    {
        $errs = array();

        if ($d['nombre_puesto'] === '' || mb_strlen($d['nombre_puesto']) > 100) {
            $errs['nombre_puesto'] = 'El puesto es obligatorio (máx. 100 caracteres).';
        }
        if ($d['empresas_id'] === null || $d['empresas_id'] <= 0) {
            $errs['empresas_id'] = 'Seleccioná una empresa.';
        }
        if ($d['estado_busqueda_id'] === null || $d['estado_busqueda_id'] <= 0) {
            $errs['estado_busqueda_id'] = 'Seleccioná un estado.';
        }
        if ($d['cantidad_vacantes'] === null || $d['cantidad_vacantes'] < 1) {
            $errs['cantidad_vacantes'] = 'La cantidad de vacantes debe ser 1 o más.';
        }
        if ($d['anios_experiencia'] !== null && $d['anios_experiencia'] < 0) {
            $errs['anios_experiencia'] = 'Los años de experiencia no pueden ser negativos.';
        }
        if ($d['modalidades_id'] === null || $d['modalidades_id'] <= 0) {
            $errs['modalidades_id'] = 'Seleccioná una modalidad.';
        }
        if ($d['descripcion'] !== null && mb_strlen($d['descripcion']) > 500) {
            $errs['descripcion'] = 'La descripción no puede superar los 500 caracteres.';
        }

        return $errs;
    }
}
