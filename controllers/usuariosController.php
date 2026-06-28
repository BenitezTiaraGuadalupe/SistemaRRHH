<?php

/**
 * Gestión de usuarios, roles y permisos (solo administradores).
 */
require_once dirname(__DIR__) . '/controllers/authController.php';

class UsuariosController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function index()
    {
        $this->requerirAdmin('usuarios.ver');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $titulo = 'Usuarios del sistema';
        $usuarios = $this->listarUsuarios($pdo);
        $puedeAdministrar = AuthController::tienePermiso('usuarios.administrar');

        include $this->viewsPath . '/usuarios/index.php';
    }

    public function create()
    {
        $this->requerirAdmin('usuarios.administrar');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $titulo = 'Nuevo usuario';
        $roles = $pdo->query('SELECT id, nombre FROM roles ORDER BY id ASC')->fetchAll();
        $ciudades = $pdo->query(
            'SELECT c.id, c.nombre, p.nombre AS provincia_nombre
             FROM ciudades c
             INNER JOIN provincias p ON p.id = c.provincias_id
             ORDER BY p.nombre ASC, c.nombre ASC
             LIMIT 500'
        )->fetchAll();

        include $this->viewsPath . '/usuarios/create.php';
    }

    public function store()
    {
        $this->requerirAdmin('usuarios.administrar');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=usuario_create');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $datos = $this->extraerDatosPost();
        $errores = $this->validarAlta($pdo, $datos);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos['old'];
            header('Location: index.php?accion=usuario_create');
            exit;
        }

        try {
            $pdo->beginTransaction();

            $hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (roles_id, correo, password) VALUES (?, ?, ?)'
            );
            $stmt->execute(array($datos['roles_id'], $datos['correo'], $hash));
            $usuarioId = (int) $pdo->lastInsertId();

            $this->crearPerfilPorRol($pdo, $usuarioId, $datos);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['errores'] = array('_general' => 'No se pudo crear el usuario. Intentá nuevamente.');
            $_SESSION['old'] = $datos['old'];
            header('Location: index.php?accion=usuario_create');
            exit;
        }

        $_SESSION['mensaje_exito'] = 'Usuario creado correctamente.';
        header('Location: index.php?accion=usuarios');
        exit;
    }

    public function roles()
    {
        $this->requerirAdmin('usuarios.ver');

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $titulo = 'Roles y permisos';
        $roles = $this->listarRolesConPermisos($pdo);
        $puedeAdministrar = AuthController::tienePermiso('usuarios.administrar');

        include $this->viewsPath . '/usuarios/roles.php';
    }

    public function rolesEdit()
    {
        $this->requerirAdmin('usuarios.administrar');

        $rolId = isset($_GET['rol_id']) ? (int) $_GET['rol_id'] : 0;
        if ($rolId <= 0) {
            header('Location: index.php?accion=roles');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $rol = $this->obtenerRol($pdo, $rolId);
        if ($rol === null) {
            $_SESSION['errores'] = array('_general' => 'Rol no encontrado.');
            header('Location: index.php?accion=roles');
            exit;
        }

        $titulo = 'Permisos del rol: ' . $rol['nombre'];
        $permisos = $pdo->query('SELECT id, nombre FROM permisos ORDER BY nombre ASC')->fetchAll();
        $asignados = $this->permisosIdsDelRol($pdo, $rolId);

        include $this->viewsPath . '/usuarios/roles_edit.php';
    }

    public function rolesUpdate()
    {
        $this->requerirAdmin('usuarios.administrar');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=roles');
            exit;
        }

        $rolId = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0;
        if ($rolId <= 0) {
            header('Location: index.php?accion=roles');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        $pdo = $GLOBALS['pdo'];

        $rol = $this->obtenerRol($pdo, $rolId);
        if ($rol === null) {
            $_SESSION['errores'] = array('_general' => 'Rol no encontrado.');
            header('Location: index.php?accion=roles');
            exit;
        }

        $permIds = isset($_POST['permisos_ids']) && is_array($_POST['permisos_ids'])
            ? $_POST['permisos_ids'] : array();
        $permIdsLimpios = array();
        foreach ($permIds as $pid) {
            $i = (int) $pid;
            if ($i > 0) {
                $permIdsLimpios[] = $i;
            }
        }
        $permIdsLimpios = array_values(array_unique($permIdsLimpios));

        if (empty($permIdsLimpios)) {
            $_SESSION['errores'] = array('_general' => 'El rol debe tener al menos un permiso asignado.');
            header('Location: index.php?accion=roles_edit&rol_id=' . $rolId);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $del = $pdo->prepare('DELETE FROM permisos_por_roles WHERE roles_id = ?');
            $del->execute(array($rolId));

            $ins = $pdo->prepare(
                'INSERT INTO permisos_por_roles (permisos_id, roles_id) VALUES (?, ?)'
            );
            foreach ($permIdsLimpios as $permId) {
                $ins->execute(array($permId, $rolId));
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['errores'] = array('_general' => 'No se pudieron guardar los permisos.');
            header('Location: index.php?accion=roles_edit&rol_id=' . $rolId);
            exit;
        }

        $_SESSION['mensaje_exito'] = 'Permisos del rol actualizados. Los cambios aplican en el próximo inicio de sesión.';
        header('Location: index.php?accion=roles');
        exit;
    }

    private function requerirAdmin($permiso)
    {
        AuthController::requerirPermiso($permiso);
        AuthController::requerirRol('admin');
    }

    private function listarUsuarios(PDO $pdo)
    {
        $sql = 'SELECT u.id, u.correo, u.roles_id,
                       r.nombre AS rol_nombre,
                       p.nombre AS rrhh_nombre, p.apellido AS rrhh_apellido,
                       e.nombre AS empresa_nombre,
                       c.nombre AS cand_nombre, c.apellido AS cand_apellido
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.roles_id
                LEFT JOIN personal_rrhh p ON p.usuarios_id = u.id
                LEFT JOIN empresas e ON e.usuarios_id = u.id
                LEFT JOIN candidatos c ON c.usuarios_id = u.id
                ORDER BY u.id DESC';

        $filas = $pdo->query($sql)->fetchAll();
        foreach ($filas as &$f) {
            $f['nombre_display'] = $this->resolverNombreDisplay($f);
            $f['rol_legible'] = $this->rolLegible($f['rol_nombre']);
        }
        unset($f);

        return $filas;
    }

    private function resolverNombreDisplay(array $f)
    {
        if (!empty($f['rrhh_nombre']) || !empty($f['rrhh_apellido'])) {
            return trim($f['rrhh_nombre'] . ' ' . $f['rrhh_apellido']);
        }
        if (!empty($f['empresa_nombre'])) {
            return $f['empresa_nombre'];
        }
        if (!empty($f['cand_nombre']) || !empty($f['cand_apellido'])) {
            return trim($f['cand_nombre'] . ' ' . $f['cand_apellido']);
        }
        return $f['correo'];
    }

    private function rolLegible($rolNombre)
    {
        $map = array(
            'admin' => 'Personal RRHH',
            'empresa' => 'Empresa',
            'candidato' => 'Candidato',
        );
        $key = strtolower((string) $rolNombre);
        return isset($map[$key]) ? $map[$key] : ucfirst($rolNombre);
    }

    private function listarRolesConPermisos(PDO $pdo)
    {
        $sql = 'SELECT r.id, r.nombre,
                       COUNT(ppr.permisos_id) AS total_permisos
                FROM roles r
                LEFT JOIN permisos_por_roles ppr ON ppr.roles_id = r.id
                GROUP BY r.id, r.nombre
                ORDER BY r.id ASC';

        $roles = $pdo->query($sql)->fetchAll();
        foreach ($roles as &$rol) {
            $rol['nombre_legible'] = $this->rolLegible($rol['nombre']);
            $rol['permisos'] = $this->nombresPermisosDelRol($pdo, (int) $rol['id']);
        }
        unset($rol);

        return $roles;
    }

    private function nombresPermisosDelRol(PDO $pdo, $rolId)
    {
        $stmt = $pdo->prepare(
            'SELECT pe.nombre
             FROM permisos_por_roles ppr
             INNER JOIN permisos pe ON pe.id = ppr.permisos_id
             WHERE ppr.roles_id = ?
             ORDER BY pe.nombre ASC'
        );
        $stmt->execute(array($rolId));
        $out = array();
        foreach ($stmt->fetchAll() as $row) {
            $out[] = $row['nombre'];
        }
        return $out;
    }

    private function obtenerRol(PDO $pdo, $rolId)
    {
        $stmt = $pdo->prepare('SELECT id, nombre FROM roles WHERE id = ? LIMIT 1');
        $stmt->execute(array($rolId));
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    private function permisosIdsDelRol(PDO $pdo, $rolId)
    {
        $stmt = $pdo->prepare(
            'SELECT permisos_id FROM permisos_por_roles WHERE roles_id = ?'
        );
        $stmt->execute(array($rolId));
        $out = array();
        foreach ($stmt->fetchAll() as $row) {
            $out[] = (int) $row['permisos_id'];
        }
        return $out;
    }

    private function extraerDatosPost()
    {
        $datos = array(
            'roles_id' => isset($_POST['roles_id']) ? (int) $_POST['roles_id'] : 0,
            'correo' => trim((string) (isset($_POST['correo']) ? $_POST['correo'] : '')),
            'password' => (string) (isset($_POST['password']) ? $_POST['password'] : ''),
            'password_confirm' => (string) (isset($_POST['password_confirm']) ? $_POST['password_confirm'] : ''),
            'nombre' => trim((string) (isset($_POST['nombre']) ? $_POST['nombre'] : '')),
            'apellido' => trim((string) (isset($_POST['apellido']) ? $_POST['apellido'] : '')),
            'empresa_nombre' => trim((string) (isset($_POST['empresa_nombre']) ? $_POST['empresa_nombre'] : '')),
            'fecha_nac' => trim((string) (isset($_POST['fecha_nac']) ? $_POST['fecha_nac'] : '')),
            'ciudades_id' => isset($_POST['ciudades_id']) ? (int) $_POST['ciudades_id'] : 0,
        );

        $datos['old'] = $datos;
        unset($datos['old']['password'], $datos['old']['password_confirm']);
        $datos['old'] = array_merge($datos['old'], array(
            'password' => '',
            'password_confirm' => '',
        ));

        return $datos;
    }

    private function validarAlta(PDO $pdo, array $d)
    {
        $errs = array();

        if ($d['correo'] === '' || !filter_var($d['correo'], FILTER_VALIDATE_EMAIL)) {
            $errs['correo'] = 'Ingresá un correo válido.';
        } elseif (mb_strlen($d['correo']) > 100) {
            $errs['correo'] = 'El correo no puede superar 100 caracteres.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = ? LIMIT 1');
            $stmt->execute(array($d['correo']));
            if ($stmt->fetch() !== false) {
                $errs['correo'] = 'Ese correo ya está registrado.';
            }
        }

        if ($d['password'] === '' || mb_strlen($d['password']) < 6) {
            $errs['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($d['password'] !== $d['password_confirm']) {
            $errs['password_confirm'] = 'Las contraseñas no coinciden.';
        }

        $rol = $this->obtenerRol($pdo, $d['roles_id']);
        if ($rol === null) {
            $errs['roles_id'] = 'Seleccioná un rol válido.';
            return $errs;
        }

        $rolKey = strtolower($rol['nombre']);
        if ($rolKey === 'admin' || $rolKey === 'candidato') {
            if ($d['nombre'] === '') {
                $errs['nombre'] = 'El nombre es obligatorio.';
            }
            if ($d['apellido'] === '') {
                $errs['apellido'] = 'El apellido es obligatorio.';
            }
        }
        if ($rolKey === 'empresa') {
            if ($d['empresa_nombre'] === '') {
                $errs['empresa_nombre'] = 'El nombre de la empresa es obligatorio.';
            }
        }
        if ($rolKey === 'candidato') {
            if ($d['fecha_nac'] === '') {
                $errs['fecha_nac'] = 'La fecha de nacimiento es obligatoria.';
            }
            if ($d['ciudades_id'] <= 0) {
                $errs['ciudades_id'] = 'Seleccioná una ciudad.';
            }
        }

        return $errs;
    }

    private function crearPerfilPorRol(PDO $pdo, $usuarioId, array $d)
    {
        $stmtRol = $pdo->prepare(
            'SELECT nombre FROM roles WHERE id = ? LIMIT 1'
        );
        $stmtRol->execute(array($d['roles_id']));
        $rolNombre = strtolower((string) $stmtRol->fetchColumn());

        if ($rolNombre === 'admin') {
            $ins = $pdo->prepare(
                'INSERT INTO personal_rrhh (usuarios_id, nombre, apellido) VALUES (?, ?, ?)'
            );
            $ins->execute(array($usuarioId, $d['nombre'], $d['apellido']));
            return;
        }

        if ($rolNombre === 'empresa') {
            $ins = $pdo->prepare(
                'INSERT INTO empresas (nombre, usuarios_id) VALUES (?, ?)'
            );
            $ins->execute(array($d['empresa_nombre'], $usuarioId));
            return;
        }

        if ($rolNombre === 'candidato') {
            $fecha = $d['fecha_nac'] . ' 00:00:00';
            $ins = $pdo->prepare(
                'INSERT INTO candidatos (usuarios_id, nombre, apellido, fecha_nac, ciudades_id) VALUES (?, ?, ?, ?, ?)'
            );
            $ins->execute(array(
                $usuarioId,
                $d['nombre'],
                $d['apellido'],
                $fecha,
                $d['ciudades_id'],
            ));
        }
    }
}
