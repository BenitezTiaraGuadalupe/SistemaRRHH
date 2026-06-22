<?php
/**
 * Subnavegación del módulo de administración.
 * @var string $usuTab  'usuarios' | 'roles'
 */
$usuTab = isset($usuTab) ? $usuTab : 'usuarios';
?>
<nav class="usu-subnav" aria-label="Administración">
    <a class="<?php echo $usuTab === 'usuarios' ? 'active' : ''; ?>" href="index.php?accion=usuarios">Usuarios</a>
    <a class="<?php echo $usuTab === 'roles' ? 'active' : ''; ?>" href="index.php?accion=roles">Roles y permisos</a>
</nav>
