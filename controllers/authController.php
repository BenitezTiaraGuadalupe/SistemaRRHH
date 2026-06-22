<?php

/**
 * Login, sesión y permisos (todo en este controlador).
 */
class AuthController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarLogin();
            return;
        }
        $this->mostrarLogin();
    }

    public function logout()
    {
        self::cerrarSesion();
        header('Location: index.php?accion=login');
        exit;
    }

    private function mostrarLogin()
    {
        if (self::estaLogueado()) {
            header('Location: index.php?accion=dashboard');
            exit;
        }

        $pageTitle = 'Iniciar sesión — TalentLink';
        $error = isset($_GET['error']) ? $this->mensajeError((string) $_GET['error']) : null;

        include $this->viewsPath . '/auth/login.php';
    }

    private function procesarLogin()
    {
        $correo = isset($_POST['correo']) ? trim((string) $_POST['correo']) : '';
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

        if ($correo === '' || $password === '') {
            header('Location: index.php?accion=login&error=campos');
            exit;
        }

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        if (!self::login($pdo, $correo, $password)) {
            header('Location: index.php?accion=login&error=credenciales');
            exit;
        }

        header('Location: index.php?accion=dashboard');
        exit;
    }

    private function mensajeError($code)
    {
        switch ($code) {
            case 'credenciales':
                return 'Email o contraseña incorrectos.';
            case 'campos':
                return 'Por favor completá email y contraseña.';
            case 'sesion':
                return 'Tu sesión expiró, ingresá nuevamente.';
        }
        return 'No se pudo iniciar sesión.';
    }

    // --- Sesión y permisos (usados desde index.php y otros controladores) ---

    public static function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        if (headers_sent()) {
            return;
        }
        session_start();
    }

    public static function estaLogueado()
    {
        self::iniciarSesion();
        return isset($_SESSION['auth']) && is_array($_SESSION['auth']);
    }

    public static function usuario()
    {
        self::iniciarSesion();
        return self::estaLogueado() ? $_SESSION['auth'] : null;
    }

    public static function tienePermiso($nombre)
    {
        $u = self::usuario();
        if ($u === null) {
            return false;
        }
        return in_array($nombre, $u['permisos'], true);
    }

    public static function esRol($nombre)
    {
        $u = self::usuario();
        if ($u === null) {
            return false;
        }
        return strcasecmp((string) $u['rol_nombre'], (string) $nombre) === 0;
    }

    public static function requerirLogin()
    {
        if (self::estaLogueado()) {
            return;
        }
        header('Location: index.php?accion=login');
        exit;
    }

    public static function requerirPermiso($nombre)
    {
        self::requerirLogin();
        if (self::tienePermiso($nombre)) {
            return;
        }
        http_response_code(403);
        echo 'No tiene permisos para acceder a esta sección (' .
            htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ').';
        exit;
    }

    public static function requerirRol($nombre)
    {
        self::requerirLogin();
        if (self::esRol($nombre)) {
            return;
        }
        http_response_code(403);
        echo 'No tiene permisos para acceder a esta sección.';
        exit;
    }

    public static function login(PDO $pdo, $correo, $password)
    {
        self::iniciarSesion();

        $sql = 'SELECT u.id, u.correo, u.password, u.roles_id,
                       r.nombre AS rol_nombre,
                       COALESCE(c.nombre, p.nombre, e.nombre) AS nombre,
                       COALESCE(c.apellido, p.apellido) AS apellido
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.roles_id
                LEFT JOIN candidatos c ON c.usuarios_id = u.id
                LEFT JOIN personal_rrhh p ON p.usuarios_id = u.id
                LEFT JOIN empresas e ON e.usuarios_id = u.id
                WHERE u.correo = ?
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($correo));
        $row = $stmt->fetch();

        if ($row === false || !password_verify($password, $row['password'])) {
            return false;
        }

        $stmtPerm = $pdo->prepare(
            'SELECT pe.nombre FROM permisos_por_roles ppr
             INNER JOIN permisos pe ON pe.id = ppr.permisos_id
             WHERE ppr.roles_id = ?'
        );
        $stmtPerm->execute(array((int) $row['roles_id']));
        $permisos = array();
        foreach ($stmtPerm->fetchAll() as $r) {
            $permisos[] = $r['nombre'];
        }

        session_regenerate_id(true);
        $_SESSION['auth'] = array(
            'id' => (int) $row['id'],
            'correo' => $row['correo'],
            'rol_id' => (int) $row['roles_id'],
            'rol_nombre' => $row['rol_nombre'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'permisos' => $permisos,
        );

        return true;
    }

    public static function cerrarSesion()
    {
        self::iniciarSesion();
        $_SESSION = array();
        session_destroy();
    }

    public static function refrescarPermisos(PDO $pdo)
    {
        self::iniciarSesion();
        if (!self::estaLogueado()) {
            return;
        }

        $rolId = (int) $_SESSION['auth']['rol_id'];
        $stmtPerm = $pdo->prepare(
            'SELECT pe.nombre FROM permisos_por_roles ppr
             INNER JOIN permisos pe ON pe.id = ppr.permisos_id
             WHERE ppr.roles_id = ?'
        );
        $stmtPerm->execute(array($rolId));
        $permisos = array();
        foreach ($stmtPerm->fetchAll() as $r) {
            $permisos[] = $r['nombre'];
        }
        $_SESSION['auth']['permisos'] = $permisos;
    }

    public static function empresaDelUsuario(PDO $pdo)
    {
        $u = self::usuario();
        if ($u === null) {
            return null;
        }
        $stmt = $pdo->prepare('SELECT id FROM empresas WHERE usuarios_id = ? LIMIT 1');
        $stmt->execute(array((int) $u['id']));
        $id = $stmt->fetchColumn();
        return $id === false ? null : (int) $id;
    }
}
