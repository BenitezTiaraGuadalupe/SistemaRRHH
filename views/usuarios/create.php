<?php
/**
 * @var string $titulo
 * @var array  $roles
 * @var array  $ciudades
 */
$old = isset($_SESSION['old']) ? $_SESSION['old'] : array();
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : array();
unset($_SESSION['old'], $_SESSION['errores']);
$errorGeneral = isset($errores['_general']) ? $errores['_general'] : null;

$pageTitle = 'Nuevo usuario — TalentLink';
$activeMenu = 'usuarios';
$usuTab = 'usuarios';

function usu_old($key, $default = '')
{
    global $old;
    return isset($old[$key]) ? $old[$key] : $default;
}
function usu_err(array $e, $k) { return isset($e[$k]) ? ' usu-input-error' : ''; }
function usu_msg(array $e, $k) {
    if (!isset($e[$k])) return '';
    return '<span class="usu-error">' . htmlspecialchars($e[$k], ENT_QUOTES, 'UTF-8') . '</span>';
}

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
                <p class="tl-card-subtitle">Alta de cuenta con rol y perfil asociado</p>
            </div>
        </div>

        <?php include __DIR__ . '/_subnav.php'; ?>

        <?php if ($errorGeneral !== null) : ?>
            <div class="usu-alert usu-alert--error" role="alert"><?php echo htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form class="usu-form" method="post" action="index.php?accion=usuario_store">
            <div class="usu-form-group">
                <label for="roles_id">Rol</label>
                <select id="roles_id" name="roles_id" required class="<?php echo trim(usu_err($errores, 'roles_id')); ?>">
                    <?php $oldRol = (int) usu_old('roles_id', 1); ?>
                    <?php foreach ($roles as $rol) : ?>
                        <option value="<?php echo (int) $rol['id']; ?>" <?php echo $oldRol === (int) $rol['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucfirst($rol['nombre']), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo usu_msg($errores, 'roles_id'); ?>
            </div>

            <div class="usu-form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required maxlength="100"
                       value="<?php echo htmlspecialchars((string) usu_old('correo'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="<?php echo trim(usu_err($errores, 'correo')); ?>">
                <?php echo usu_msg($errores, 'correo'); ?>
            </div>

            <div class="usu-form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required minlength="6"
                       class="<?php echo trim(usu_err($errores, 'password')); ?>">
                <?php echo usu_msg($errores, 'password'); ?>
            </div>

            <div class="usu-form-group">
                <label for="password_confirm">Confirmar contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                       class="<?php echo trim(usu_err($errores, 'password_confirm')); ?>">
                <?php echo usu_msg($errores, 'password_confirm'); ?>
            </div>

            <div id="campos-admin-candidato" class="usu-perfil-campos visible">
                <div class="usu-form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" maxlength="45"
                           value="<?php echo htmlspecialchars((string) usu_old('nombre'), ENT_QUOTES, 'UTF-8'); ?>"
                           class="<?php echo trim(usu_err($errores, 'nombre')); ?>">
                    <?php echo usu_msg($errores, 'nombre'); ?>
                </div>
                <div class="usu-form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" maxlength="45"
                           value="<?php echo htmlspecialchars((string) usu_old('apellido'), ENT_QUOTES, 'UTF-8'); ?>"
                           class="<?php echo trim(usu_err($errores, 'apellido')); ?>">
                    <?php echo usu_msg($errores, 'apellido'); ?>
                </div>
            </div>

            <div id="campos-empresa" class="usu-perfil-campos">
                <div class="usu-form-group">
                    <label for="empresa_nombre">Nombre de la empresa</label>
                    <input type="text" id="empresa_nombre" name="empresa_nombre" maxlength="100"
                           value="<?php echo htmlspecialchars((string) usu_old('empresa_nombre'), ENT_QUOTES, 'UTF-8'); ?>"
                           class="<?php echo trim(usu_err($errores, 'empresa_nombre')); ?>">
                    <?php echo usu_msg($errores, 'empresa_nombre'); ?>
                </div>
            </div>

            <div id="campos-candidato-extra" class="usu-perfil-campos">
                <div class="usu-form-group">
                    <label for="fecha_nac">Fecha de nacimiento</label>
                    <input type="date" id="fecha_nac" name="fecha_nac"
                           value="<?php echo htmlspecialchars((string) usu_old('fecha_nac'), ENT_QUOTES, 'UTF-8'); ?>"
                           class="<?php echo trim(usu_err($errores, 'fecha_nac')); ?>">
                    <?php echo usu_msg($errores, 'fecha_nac'); ?>
                </div>
                <div class="usu-form-group">
                    <label for="ciudades_id">Ciudad</label>
                    <select id="ciudades_id" name="ciudades_id" class="<?php echo trim(usu_err($errores, 'ciudades_id')); ?>">
                        <option value="">Seleccionar...</option>
                        <?php $oldCiu = (int) usu_old('ciudades_id', 0); ?>
                        <?php foreach ($ciudades as $ciu) : ?>
                            <option value="<?php echo (int) $ciu['id']; ?>" <?php echo $oldCiu === (int) $ciu['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ciu['nombre'] . ' (' . $ciu['provincia_nombre'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo usu_msg($errores, 'ciudades_id'); ?>
                </div>
            </div>

            <div class="usu-form-actions" style="padding-left:0;">
                <button type="submit" class="usu-btn usu-btn--primary">Crear usuario</button>
                <a class="usu-btn usu-btn--ghost" href="index.php?accion=usuarios">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    var selRol = document.getElementById('roles_id');
    var camposAdminCand = document.getElementById('campos-admin-candidato');
    var camposEmpresa = document.getElementById('campos-empresa');
    var camposCandExtra = document.getElementById('campos-candidato-extra');
    if (!selRol) return;

    var rolesPorId = {};
    <?php foreach ($roles as $rol) : ?>
    rolesPorId[<?php echo (int) $rol['id']; ?>] = '<?php echo htmlspecialchars(strtolower($rol['nombre']), ENT_QUOTES, 'UTF-8'); ?>';
    <?php endforeach; ?>

    function actualizarCampos() {
        var rol = rolesPorId[parseInt(selRol.value, 10)] || '';
        camposAdminCand.classList.remove('visible');
        camposEmpresa.classList.remove('visible');
        camposCandExtra.classList.remove('visible');
        if (rol === 'admin' || rol === 'candidato') {
            camposAdminCand.classList.add('visible');
        }
        if (rol === 'empresa') {
            camposEmpresa.classList.add('visible');
        }
        if (rol === 'candidato') {
            camposCandExtra.classList.add('visible');
        }
    }

    selRol.addEventListener('change', actualizarCampos);
    actualizarCampos();
})();
</script>
    </main>
</div>
</div>
</body>
</html>
