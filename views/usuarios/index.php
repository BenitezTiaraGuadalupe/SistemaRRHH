<?php
/**
 * @var string $titulo
 * @var array  $usuarios
 * @var bool   $puedeAdministrar
 */
$pageTitle = 'Usuarios — TalentLink';
$activeMenu = 'usuarios';
$usuTab = 'usuarios';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['mensaje_exito'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;
$totalUsuarios = count($usuarios);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="views/partials/app_styles.css">
    <link rel="stylesheet" href="views/usuarios/usuarios_styles.css">
</head>
<body>
<div class="app">
<?php
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/navbar.php';
?>
<div class="tl-page">
    <div class="tl-card">
        <div class="tl-card-header">
            <div>
                <h2 class="tl-card-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="tl-card-subtitle">Cuentas registradas en la plataforma</p>
            </div>
            <div class="usu-header-actions">
                <?php if ($totalUsuarios > 0) : ?>
                    <span class="tl-badge"><?php echo (int) $totalUsuarios; ?> <?php echo $totalUsuarios === 1 ? 'usuario' : 'usuarios'; ?></span>
                <?php endif; ?>
                <?php if ($puedeAdministrar) : ?>
                    <a class="usu-btn usu-btn--primary" href="index.php?accion=usuario_create">Nuevo usuario</a>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/_subnav.php'; ?>

        <?php if ($mensajeExito !== null) : ?>
            <div class="usu-alert usu-alert--ok" role="status"><?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($errorGeneral !== null) : ?>
            <div class="usu-alert usu-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($usuarios)) : ?>
            <div class="tl-empty">
                <strong>No hay usuarios registrados</strong>
                Cuando se den de alta usuarios, aparecerán en este listado.
            </div>
        <?php else : ?>
            <div class="tl-table-wrap">
                <table class="tl-table">
                    <thead>
                        <tr>
                            <th>Nombre / Empresa</th>
                            <th>Correo</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u) : ?>
                            <tr>
                                <td class="tl-cell-name"><?php echo htmlspecialchars($u['nombre_display'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="usu-rol-tag"><?php echo htmlspecialchars($u['rol_legible'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
