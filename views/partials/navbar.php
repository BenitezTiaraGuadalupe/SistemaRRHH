<?php
/**
 * Barra superior del panel.
 * Variables opcionales: $pageTitle (ej. 'Dashboard — TalentLink').
 */
require_once dirname(__DIR__, 2) . '/controllers/authController.php';

$tituloNavbar = '';
if (isset($pageTitle)) {
    $tituloNavbar = preg_replace('/\s*—.*$/u', '', (string) $pageTitle);
}

$usuario = AuthController::usuario();
$nombreUsuario = '';
$rolUsuario = '';
$inicialesUsuario = '';
$correoUsuario = '';

if ($usuario !== null) {
    $partesNombre = array();
    if (!empty($usuario['nombre'])) {
        $partesNombre[] = $usuario['nombre'];
    }
    if (!empty($usuario['apellido'])) {
        $partesNombre[] = $usuario['apellido'];
    }

    $nombreUsuario = trim(implode(' ', $partesNombre));
    if ($nombreUsuario === '') {
        $nombreUsuario = (string) $usuario['correo'];
    }

    $rolesLegibles = array(
        'admin' => 'Personal RRHH',
        'empresa' => 'Empresa',
        'candidato' => 'Candidato',
    );
    $rolKey = strtolower((string) $usuario['rol_nombre']);
    $rolUsuario = isset($rolesLegibles[$rolKey]) ? $rolesLegibles[$rolKey] : ucfirst($usuario['rol_nombre']);

    foreach ($partesNombre as $parte) {
        $inicialesUsuario .= mb_substr($parte, 0, 1, 'UTF-8');
    }
    if ($inicialesUsuario === '') {
        $inicialesUsuario = mb_substr($nombreUsuario, 0, 1, 'UTF-8');
    }
    $inicialesUsuario = mb_strtoupper($inicialesUsuario, 'UTF-8');
    $correoUsuario = (string) $usuario['correo'];
}
?>
<div class="content">
    <nav class="navbar" aria-label="Barra superior">
        <div class="navbar-inner">
            <div class="navbar-title">
                <span class="page-eyebrow">Panel</span>
                <?php if ($tituloNavbar !== '') : ?>
                    <span class="page-title"><?php echo htmlspecialchars($tituloNavbar, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($usuario !== null) : ?>
                <div class="session">
                    <div class="user-pill" title="<?php echo htmlspecialchars($correoUsuario, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars($inicialesUsuario, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="user-info">
                            <span class="user"><?php echo htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($rolUsuario, ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    </div>
                    <a class="logout" href="index.php?accion=logout" title="Cerrar sesión">
                        <span class="logout-text">Salir</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <main class="main">
