<?php
/**
 * Vista provisional: alta de solicitud (búsqueda + detalle_busquedas + habilidades_por_busqueda).
 * Estilos en solicitudes/solicitudes_styles.css (prefijo .as-).
 *
 * @var string $titulo
 * @var array  $empresas
 * @var array  $estadosBusqueda
 * @var array  $modalidades
 * @var array  $paises
 * @var array  $provincias
 * @var array  $ciudades
 * @var array  $habilidades
 */
require_once dirname(__DIR__, 2) . '/lib/flash.php';

$asErrores = flash_errors();
$asErrorGeneral = isset($asErrores['_general']) ? $asErrores['_general'] : null;

$asOldHabIds = flash_old('habilidades_ids', array());
if (!is_array($asOldHabIds)) {
    $asOldHabIds = array();
}
$asOldHabNuevas = flash_old('habilidades_nuevas', array());
if (!is_array($asOldHabNuevas)) {
    $asOldHabNuevas = array();
}

$asHasOldGeo = (flash_old('paises_id', '') !== '' || flash_old('provincias_id', '') !== '' || flash_old('ciudades_id', '') !== '');
$asOldPais = (int) flash_old('paises_id', 0);
$asOldProv = (int) flash_old('provincias_id', 0);
$asOldCiu = (int) flash_old('ciudades_id', 0);

$asHabilidadesPorId = array();
foreach ($habilidades as $h) {
    $asHabilidadesPorId[(int) $h['id']] = $h['nombre'];
}

$asTechInicial = array();
foreach ($asOldHabIds as $hid) {
    $hid = (int) $hid;
    if (isset($asHabilidadesPorId[$hid])) {
        $asTechInicial[] = array('id' => $hid, 'nombre' => $asHabilidadesPorId[$hid]);
    }
}
foreach ($asOldHabNuevas as $nombre) {
    $nombre = trim((string) $nombre);
    if ($nombre === '') {
        continue;
    }
    $asTechInicial[] = array('id' => null, 'nombre' => $nombre);
}

function as_clase_error(array $errores, $key)
{
    return isset($errores[$key]) ? ' as-input-error' : '';
}

function as_msg_error(array $errores, $key)
{
    if (!isset($errores[$key])) {
        return '';
    }
    return '<span class="as-error">' . htmlspecialchars($errores[$key], ENT_QUOTES, 'UTF-8') . '</span>';
}

require_once dirname(__DIR__, 2) . '/lib/paths.php';

$pageTitle = 'Nueva solicitud — TalentLink';
$activeMenu = 'solicitudes';
$moduleStylesheets = array(app_module_styles('solicitudes'));

include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/side_bar.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="as-card">
    <div class="as-header">
        <div>
            <h2 class="as-title"><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="as-subtitle">Complete los datos para registrar una nueva búsqueda de candidatos</p>
        </div>
    </div>

    <?php if ($asErrorGeneral !== null) : ?>
        <div class="as-alert" role="alert">
            <?php echo htmlspecialchars($asErrorGeneral, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?accion=store" autocomplete="off">

        <div class="as-form-group">
            <label for="nombre_puesto">Puesto solicitado</label>
            <input id="nombre_puesto" name="nombre_puesto" type="text"
                   class="<?php echo trim('' . as_clase_error($asErrores, 'nombre_puesto')); ?>"
                   value="<?php echo htmlspecialchars(flash_old('nombre_puesto', ''), ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Ej: Desarrollador Backend" required>
            <?php echo as_msg_error($asErrores, 'nombre_puesto'); ?>
        </div>

        <div class="as-row">
            <div class="as-form-group">
                <label for="empresas_id">Empresa</label>
                <select id="empresas_id" name="empresas_id" required
                        class="<?php echo trim('' . as_clase_error($asErrores, 'empresas_id')); ?>">
                    <option value="">Seleccione una empresa</option>
                    <?php $oldEmp = (int) flash_old('empresas_id', 0); ?>
                    <?php foreach ($empresas as $empresa) : ?>
                        <option value="<?php echo (int) $empresa['id']; ?>"
                            <?php echo $oldEmp === (int) $empresa['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($empresa['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo as_msg_error($asErrores, 'empresas_id'); ?>
            </div>

            <div class="as-form-group">
                <label for="estado_busqueda_id">Estado de la búsqueda</label>
                <select id="estado_busqueda_id" name="estado_busqueda_id" required
                        class="<?php echo trim('' . as_clase_error($asErrores, 'estado_busqueda_id')); ?>">
                    <option value="">Seleccione un estado</option>
                    <?php $oldEstado = (int) flash_old('estado_busqueda_id', 0); ?>
                    <?php foreach ($estadosBusqueda as $estado) : ?>
                        <option value="<?php echo (int) $estado['id']; ?>"
                            <?php echo $oldEstado === (int) $estado['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($estado['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo as_msg_error($asErrores, 'estado_busqueda_id'); ?>
            </div>
        </div>

        <div class="as-row">
            <div class="as-form-group">
                <label for="cantidad_vacantes">Cantidad de vacantes</label>
                <input id="cantidad_vacantes" name="cantidad_vacantes" type="number" min="1" required
                       class="<?php echo trim('' . as_clase_error($asErrores, 'cantidad_vacantes')); ?>"
                       value="<?php echo htmlspecialchars((string) flash_old('cantidad_vacantes', '1'), ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo as_msg_error($asErrores, 'cantidad_vacantes'); ?>
            </div>

            <div class="as-form-group">
                <label for="anios_experiencia">Años de experiencia</label>
                <input id="anios_experiencia" name="anios_experiencia" type="number" min="0" placeholder="Ej: 3"
                       class="<?php echo trim('' . as_clase_error($asErrores, 'anios_experiencia')); ?>"
                       value="<?php echo htmlspecialchars((string) flash_old('anios_experiencia', ''), ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo as_msg_error($asErrores, 'anios_experiencia'); ?>
            </div>
        </div>

        <div class="as-form-group">
            <label for="techInput">Tecnologías requeridas</label>
            <div class="as-tech-input">
                <input id="techInput" type="text" placeholder="Ej: Laravel, MySQL, JavaScript" list="habilidadesList">
                <datalist id="habilidadesList">
                    <?php foreach ($habilidades as $hab) : ?>
                        <option
                            data-id="<?php echo (int) $hab['id']; ?>"
                            value="<?php echo htmlspecialchars($hab['nombre'], ENT_QUOTES, 'UTF-8'); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <button id="techAddBtn" class="as-btn-secondary" type="button">Agregar</button>
                <button id="techPlusBtn" class="as-btn-icon" type="button" aria-label="Agregar tecnología">+</button>
            </div>
            <div id="techTags" class="as-tech-tags" aria-label="Tecnologías agregadas"></div>
            <div id="techHidden"></div>
        </div>

        <div class="as-form-group">
            <label for="descripcion">Descripción del puesto</label>
            <textarea id="descripcion" name="descripcion" rows="4" maxlength="500"
                      placeholder="Detalle de responsabilidades y requisitos"
                      class="<?php echo trim('' . as_clase_error($asErrores, 'descripcion')); ?>"><?php
                echo htmlspecialchars((string) flash_old('descripcion', ''), ENT_QUOTES, 'UTF-8');
            ?></textarea>
            <?php echo as_msg_error($asErrores, 'descripcion'); ?>
        </div>

        <div class="as-form-group">
            <label for="modalidades_id">Modalidad</label>
            <select id="modalidades_id" name="modalidades_id" required
                    class="<?php echo trim('' . as_clase_error($asErrores, 'modalidades_id')); ?>">
                <option value="">Seleccione una modalidad</option>
                <?php $oldMod = (int) flash_old('modalidades_id', 0); ?>
                <?php foreach ($modalidades as $modalidad) : ?>
                    <option value="<?php echo (int) $modalidad['id']; ?>"
                        <?php echo $oldMod === (int) $modalidad['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($modalidad['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo as_msg_error($asErrores, 'modalidades_id'); ?>
        </div>

        <div class="as-row-3">
            <div class="as-form-group">
                <label for="paises_id">País</label>
                <select id="paises_id" name="paises_id">
                    <option value="">Seleccione un país</option>
                    <?php foreach ($paises as $pais) : ?>
                        <option value="<?php echo (int) $pais['id']; ?>"
                            <?php echo $asOldPais === (int) $pais['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pais['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="as-form-group">
                <label for="provincias_id">Provincia</label>
                <select id="provincias_id" name="provincias_id" <?php echo $asOldPais > 0 ? '' : 'disabled'; ?>>
                    <?php if ($asOldPais === 0) : ?>
                        <option value="">Seleccione un país primero</option>
                    <?php else : ?>
                        <option value="">Seleccione una provincia</option>
                        <?php foreach ($provincias as $prov) : ?>
                            <?php if ((int) $prov['paises_id'] !== $asOldPais) {
                                continue;
                            } ?>
                            <option value="<?php echo (int) $prov['id']; ?>"
                                <?php echo $asOldProv === (int) $prov['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prov['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="as-form-group">
                <label for="ciudades_id">Ciudad</label>
                <select id="ciudades_id" name="ciudades_id" <?php echo $asOldProv > 0 ? '' : 'disabled'; ?>>
                    <?php if ($asOldProv === 0) : ?>
                        <option value="">Seleccione una provincia primero</option>
                    <?php else : ?>
                        <option value="">Seleccione una ciudad</option>
                        <?php foreach ($ciudades as $ciu) : ?>
                            <?php if ((int) $ciu['provincias_id'] !== $asOldProv) {
                                continue;
                            } ?>
                            <option value="<?php echo (int) $ciu['id']; ?>"
                                <?php echo $asOldCiu === (int) $ciu['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ciu['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="as-btn-submit">Enviar</button>

    </form>
</div>

<script>
(function () {
    var provinciasData = <?php echo json_encode(array_map(function ($p) {
        return array(
            'id' => (int) $p['id'],
            'nombre' => $p['nombre'],
            'paises_id' => (int) $p['paises_id'],
        );
    }, $provincias), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    var ciudadesData = <?php echo json_encode(array_map(function ($c) {
        return array(
            'id' => (int) $c['id'],
            'nombre' => $c['nombre'],
            'provincias_id' => (int) $c['provincias_id'],
        );
    }, $ciudades), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    var paisSel = document.getElementById('paises_id');
    var provSel = document.getElementById('provincias_id');
    var ciuSel = document.getElementById('ciudades_id');

    function fillSelect(select, items, placeholder) {
        select.innerHTML = '';
        var ph = document.createElement('option');
        ph.value = '';
        ph.textContent = placeholder;
        select.appendChild(ph);
        items.forEach(function (it) {
            var opt = document.createElement('option');
            opt.value = String(it.id);
            opt.textContent = it.nombre;
            select.appendChild(opt);
        });
    }

    paisSel.addEventListener('change', function () {
        var paisId = parseInt(paisSel.value, 10);
        if (!paisId) {
            fillSelect(provSel, [], 'Seleccione un país primero');
            provSel.disabled = true;
            fillSelect(ciuSel, [], 'Seleccione una provincia primero');
            ciuSel.disabled = true;
            return;
        }
        var provs = provinciasData.filter(function (p) { return p.paises_id === paisId; });
        fillSelect(provSel, provs, 'Seleccione una provincia');
        provSel.disabled = provs.length === 0;
        fillSelect(ciuSel, [], 'Seleccione una provincia primero');
        ciuSel.disabled = true;
    });

    provSel.addEventListener('change', function () {
        var provId = parseInt(provSel.value, 10);
        if (!provId) {
            fillSelect(ciuSel, [], 'Seleccione una provincia primero');
            ciuSel.disabled = true;
            return;
        }
        var ciuds = ciudadesData.filter(function (c) { return c.provincias_id === provId; });
        fillSelect(ciuSel, ciuds, 'Seleccione una ciudad');
        ciuSel.disabled = ciuds.length === 0;
    });
})();

(function () {
    var input = document.getElementById('techInput');
    var addBtn = document.getElementById('techAddBtn');
    var plusBtn = document.getElementById('techPlusBtn');
    var tagsWrap = document.getElementById('techTags');
    var hiddenWrap = document.getElementById('techHidden');
    var dataList = document.getElementById('habilidadesList');

    if (!input || !addBtn || !plusBtn || !tagsWrap || !hiddenWrap || !dataList) return;

    var habilidades = <?php echo json_encode($asTechInicial, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    function findHabilidadId(nombre) {
        var lower = nombre.toLowerCase();
        var opts = dataList.options || dataList.getElementsByTagName('option');
        for (var i = 0; i < opts.length; i++) {
            if ((opts[i].value || '').toLowerCase() === lower) {
                var id = opts[i].getAttribute('data-id');
                return id ? parseInt(id, 10) : null;
            }
        }
        return null;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function render() {
        tagsWrap.innerHTML = habilidades.map(function (h, idx) {
            return '<span class="as-tech-tag" data-idx="' + idx + '">'
                + '<span class="as-tech-tag__text">' + escapeHtml(h.nombre) + '</span>'
                + '<span class="as-tech-tag__actions">'
                + '<button class="as-tech-tag__btn" type="button" data-action="edit">Editar</button>'
                + '<button class="as-tech-tag__btn as-tech-tag__btn--danger" type="button" data-action="delete">Eliminar</button>'
                + '</span>'
                + '</span>';
        }).join('');

        hiddenWrap.innerHTML = habilidades.map(function (h) {
            if (h.id) {
                return '<input type="hidden" name="habilidades_ids[]" value="' + h.id + '">';
            }
            return '<input type="hidden" name="habilidades_nuevas[]" value="' + escapeHtml(h.nombre) + '">';
        }).join('');
    }

    function addFromInput() {
        var value = (input.value || '').trim().replace(/\s+/g, ' ');
        if (!value) return;
        var exists = habilidades.some(function (h) {
            return h.nombre.toLowerCase() === value.toLowerCase();
        });
        if (exists) {
            input.value = '';
            input.focus();
            return;
        }
        habilidades.push({ id: findHabilidadId(value), nombre: value });
        input.value = '';
        input.focus();
        render();
    }

    function removeAt(idx) {
        habilidades = habilidades.filter(function (_, i) { return i !== idx; });
        render();
    }

    function editAt(idx) {
        var current = habilidades[idx];
        if (!current) return;
        removeAt(idx);
        input.value = current.nombre;
        input.focus();
    }

    addBtn.addEventListener('click', addFromInput);
    plusBtn.addEventListener('click', addFromInput);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addFromInput();
        }
    });

    tagsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-action]');
        if (!btn) return;
        var tag = btn.closest('.as-tech-tag');
        if (!tag) return;
        var idx = Number(tag.getAttribute('data-idx'));
        var action = btn.getAttribute('data-action');
        if (Number.isNaN(idx)) return;
        if (action === 'delete') removeAt(idx);
        if (action === 'edit') editAt(idx);
    });

    render();
})();
</script>
<?php
flash_consumir_form();
include __DIR__ . '/../partials/app_close.php';
?>
