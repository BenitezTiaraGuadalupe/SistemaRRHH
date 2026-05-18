<?php

/**
 * Helpers de autenticación y autorización (PHP plano).
 *
 * Uso típico (en un controlador):
 *   require_once __DIR__ . '/../lib/auth.php';
 *   require_once __DIR__ . '/../database.php';
 *   auth_iniciar();
 *   auth_requerir_login();
 *   auth_requerir_permiso('solicitudes.crear');
 *
 * Estructura guardada en $_SESSION['auth']:
 *   [
 *       'id'         => int,
 *       'correo'     => string,
 *       'rol_id'     => int,
 *       'rol_nombre' => string,
 *       'nombre'     => string|null,
 *       'apellido'   => string|null,
 *       'permisos'   => string[],
 *   ]
 */

if (!defined('AUTH_LOGIN_URL')) {
    define('AUTH_LOGIN_URL', 'index.php?accion=login');
}

/**
 * Inicia la sesión PHP con flags seguros si todavía no está activa.
 */
function auth_iniciar()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (headers_sent()) {
        return;
    }
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

/**
 * Intenta autenticar al usuario con (correo, password).
 * Devuelve true en éxito y deja $_SESSION['auth'] cargado.
 */
function auth_login(PDO $pdo, $correo, $password)
{
    auth_iniciar();

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

    if ($row === false) {
        return false;
    }
    if (!password_verify($password, $row['password'])) {
        return false;
    }

    $stmtPerm = $pdo->prepare(
        'SELECT pe.nombre
         FROM permisos_por_roles ppr
         INNER JOIN permisos pe ON pe.id = ppr.permisos_id
         WHERE ppr.roles_id = ?'
    );
    $stmtPerm->execute(array((int) $row['roles_id']));
    $permisos = array();
    foreach ($stmtPerm->fetchAll() as $r) {
        $permisos[] = $r['nombre'];
    }

    // Evita session fixation tras autenticar.
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

/**
 * Cierra la sesión del usuario actual.
 */
function auth_logout()
{
    auth_iniciar();
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * @return bool true si hay un usuario logueado en la sesión actual.
 */
function auth_logueado()
{
    auth_iniciar();
    return isset($_SESSION['auth']) && is_array($_SESSION['auth']);
}

/**
 * @return array|null Datos del usuario logueado, o null si no hay sesión.
 */
function auth_usuario_actual()
{
    auth_iniciar();
    return auth_logueado() ? $_SESSION['auth'] : null;
}

/**
 * @return bool true si el usuario logueado tiene el permiso indicado.
 */
function auth_tiene_permiso($nombre)
{
    $u = auth_usuario_actual();
    if ($u === null) {
        return false;
    }
    return in_array($nombre, $u['permisos'], true);
}

/**
 * Si no hay sesión activa, redirige a la pantalla de login y termina la ejecución.
 */
function auth_requerir_login($loginUrl = AUTH_LOGIN_URL)
{
    if (auth_logueado()) {
        return;
    }
    if (!headers_sent()) {
        header('Location: ' . $loginUrl);
    }
    exit;
}

/**
 * Exige que el usuario logueado tenga el permiso indicado.
 * Responde 403 y termina si no lo tiene.
 */
function auth_requerir_permiso($nombre)
{
    auth_requerir_login();
    if (auth_tiene_permiso($nombre)) {
        return;
    }
    if (!headers_sent()) {
        http_response_code(403);
    }
    echo 'No tiene permisos para acceder a esta sección (' .
        htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ').';
    exit;
}
