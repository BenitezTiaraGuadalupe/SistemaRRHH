<?php
/**
 * @var string $titulo
 * @var array  $roles
 * @var bool   $puedeAdministrar
 */
$pageTitle = 'Roles y permisos — TalentLink';
$activeMenu = 'usuarios';
$usuTab = 'roles';
$mensajeExito = isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : null;
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['mensaje_exito'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;

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
                <p class="tl-card-subtitle">Permisos asignados a cada rol del sistema</p>
            </div>
        </div>

        <?php include __DIR__ . '/_subnav.php'; ?>

        <?php if ($mensajeExito !== null) : ?>
            <div class="usu-alert usu-alert--ok" role="status"><?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($errorGeneral !== null) : ?>
            <div class="usu-alert usu-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php foreach ($roles as $rol) : ?>
            <article class="usu-rol-card">
                <div class="usu-rol-card-head">
                    <div>
                        <h3 class="usu-rol-card-title"><?php echo htmlspecialchars($rol['nombre_legible'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="tl-card-subtitle"><?php echo (int) $rol['total_permisos']; ?> permisos asignados</p>
                    </div>
                    <?php if ($puedeAdministrar) : ?>
                        <a class="usu-btn usu-btn--ghost"
                           href="index.php?accion=roles_edit&amp;rol_id=<?php echo (int) $rol['id']; ?>">
                            Editar permisos
                        </a>
                    <?php endif; ?>
                </div>
                <div class="usu-permisos-tags">
                    <?php if (empty($rol['permisos'])) : ?>
                        <span class="tl-cell-muted">Sin permisos</span>
                    <?php else : ?>
                        <?php foreach ($rol['permisos'] as $perm) : ?>
                            <span class="usu-perm-tag"><?php echo htmlspecialchars($perm, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
