<h1><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h1>
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
