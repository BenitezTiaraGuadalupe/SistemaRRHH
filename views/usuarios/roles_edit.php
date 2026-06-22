<?php
/**
 * @var string $titulo
 * @var array  $rol
 * @var array  $permisos
 * @var array  $asignados
 */
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;

$pageTitle = 'Editar permisos — TalentLink';
$activeMenu = 'usuarios';
$usuTab = 'roles';

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
                <p class="tl-card-subtitle">Marcá los permisos que tendrá este rol</p>
            </div>
        </div>

        <?php include __DIR__ . '/_subnav.php'; ?>

        <?php if ($errorGeneral !== null) : ?>
            <div class="usu-alert usu-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?accion=roles_update">
            <input type="hidden" name="rol_id" value="<?php echo (int) $rol['id']; ?>">

            <div class="usu-permisos-grid">
                <?php foreach ($permisos as $perm) : ?>
                    <?php $checked = in_array((int) $perm['id'], $asignados, true); ?>
                    <label class="usu-permiso-item">
                        <input type="checkbox" name="permisos_ids[]" value="<?php echo (int) $perm['id']; ?>"
                            <?php echo $checked ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($perm['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="usu-form-actions">
                <button type="submit" class="usu-btn usu-btn--primary">Guardar permisos</button>
                <a class="usu-btn usu-btn--ghost" href="index.php?accion=roles">Cancelar</a>
            </div>
        </form>
    </div>
</div>
    </main>
</div>
</div>
</body>
</html>
