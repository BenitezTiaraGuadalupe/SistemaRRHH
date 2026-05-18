<?php

/**
 * Controlador de autenticación: muestra el login, procesa credenciales y cierra sesión.
 * PHP plano: incluye vistas y delega validación a lib/auth.php.
 */
require_once dirname(__DIR__) . '/lib/auth.php';

class AuthController
{
    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
    }

    /**
     * Acción "login": GET muestra el formulario, POST procesa credenciales.
     */
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarLogin();
            return;
        }
        $this->mostrarLogin();
    }

    /**
     * Cierra la sesión y vuelve a la pantalla de login.
     */
    public function logout()
    {
        auth_logout();
        $this->redirigir('index.php?accion=login');
    }

    private function mostrarLogin()
    {
        if (auth_logueado()) {
            $this->redirigir('index.php?accion=index');
            return;
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
            $this->redirigir('index.php?accion=login&error=campos');
            return;
        }

        require_once dirname(__DIR__) . '/database.php';
        /** @var PDO $pdo */

        if (!auth_login($pdo, $correo, $password)) {
            $this->redirigir('index.php?accion=login&error=credenciales');
            return;
        }

        $this->redirigir('index.php?accion=index');
    }

    private function redirigir($url)
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        }
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
}
