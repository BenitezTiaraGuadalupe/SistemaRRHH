<?php
/**
 * @var string $titulo
 * @var array  $solicitudes
 */
require_once dirname(__DIR__, 2) . '/lib/flash.php';
require_once dirname(__DIR__, 2) . '/lib/paths.php';

$pageTitle = 'Solicitudes — TalentLink';
$activeMenu = 'solicitudes';
$mensajeExito = flash_get('exito');

include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/side_bar.php';
include __DIR__ . '/../partials/navbar.php';
?>
<h1><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if ($mensajeExito !== null) : ?>
    <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:10px 12px;border-radius:10px;font-size:13px;margin-bottom:14px;">
        <?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<p>Listado de solicitudes. Los datos vendrán de la base de datos más adelante.</p>

<?php if (empty($solicitudes)) : ?>
    <p>No hay solicitudes cargadas por ahora.</p>
<?php else : ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($solicitud['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['estado'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['fecha'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include __DIR__ . '/../partials/app_close.php'; ?>
