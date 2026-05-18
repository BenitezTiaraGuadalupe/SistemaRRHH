<?php
/**
 * @var string $titulo
 * @var array  $candidatos
 */
$totalCandidatos = count($candidatos);
?>
<div class="tl-page">
    <div class="tl-card">
        <div class="tl-card-header">
            <div>
                <h2 class="tl-card-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="tl-card-subtitle">Personas registradas como candidatos en la plataforma</p>
            </div>
            <?php if ($totalCandidatos > 0) : ?>
                <span class="tl-badge"><?php echo (int) $totalCandidatos; ?> <?php echo $totalCandidatos === 1 ? 'candidato' : 'candidatos'; ?></span>
            <?php endif; ?>
        </div>

        <?php if (empty($candidatos)) : ?>
            <div class="tl-empty">
                <strong>No hay candidatos cargados</strong>
                Cuando se registren candidatos, aparecerán en este listado.
            </div>
        <?php else : ?>
            <div class="tl-table-wrap">
                <table class="tl-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Provincia</th>
                            <th>Fecha de nac.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatos as $candidato) : ?>
                            <?php
                            $fechaNac = '';
                            if (!empty($candidato['fecha_nac'])) {
                                $ts = strtotime($candidato['fecha_nac']);
                                $fechaNac = $ts !== false ? date('d/m/Y', $ts) : '';
                            }
                            $nombreCompleto = trim($candidato['nombre'] . ' ' . $candidato['apellido']);
                            $ciudad = isset($candidato['ciudad_nombre']) ? $candidato['ciudad_nombre'] : '';
                            $provincia = isset($candidato['provincia_nombre']) ? $candidato['provincia_nombre'] : '';
                            ?>
                            <tr>
                                <td class="tl-cell-name"><?php echo htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo htmlspecialchars($candidato['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($ciudad !== '' ? $ciudad : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($provincia !== '' ? $provincia : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="tl-cell-muted"><?php echo htmlspecialchars($fechaNac !== '' ? $fechaNac : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
