<?php
/**
 * Vista provisional: alta de solicitud (búsqueda + detalle_busquedas + habilidades_por_busqueda).
 * Estilos scopeados con prefijo .as- para no colisionar con el layout.
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
?>
<style>
    .as-card{
        --as-brand-700: #1f3c88;
        --as-brand-600: #2a4aa3;
        --as-ink-900: #0f172a;
        --as-ink-700: #334155;
        --as-muted-600: #64748b;
        --as-border-200: #e5e7eb;
        --as-radius-12: 12px;
        --as-radius-10: 10px;

        max-width: 1020px;
        margin: 8px auto 24px;
        background: #ffffff;
        padding: 26px;
        border-radius: var(--as-radius-12);
        box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
        border: 1px solid rgba(229, 231, 235, 0.9);
        color: var(--as-ink-900);
        font-family: 'Segoe UI', Tahoma, sans-serif;
        box-sizing: border-box;
    }

    .as-card *,
    .as-card *::before,
    .as-card *::after{ box-sizing: border-box; }

    .as-card .as-header{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        border-bottom: 1px solid rgba(229, 231, 235, 0.9);
        padding-bottom: 14px;
    }

    .as-card .as-title{
        margin: 0 0 6px;
        color: var(--as-brand-700);
        letter-spacing: -0.2px;
        font-size: 22px;
    }

    .as-card .as-subtitle{
        margin: 0;
        color: var(--as-muted-600);
        font-size: 14px;
    }

    .as-card form{ margin-top: 18px; }

    .as-card .as-form-group{ margin-bottom: 16px; }

    .as-card label{
        display: block;
        margin-bottom: 7px;
        font-weight: 700;
        color: var(--as-ink-700);
        font-size: 13px;
    }

    .as-card input,
    .as-card select,
    .as-card textarea{
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(203, 213, 225, 0.9);
        border-radius: var(--as-radius-10);
        font-size: 14px;
        transition: border-color .15s ease, box-shadow .15s ease;
        background: #fff;
        color: var(--as-ink-900);
        font-family: inherit;
    }

    .as-card select{
        appearance: none;
        background-image:
            linear-gradient(45deg, transparent 50%, rgba(100, 116, 139, .9) 50%),
            linear-gradient(135deg, rgba(100, 116, 139, .9) 50%, transparent 50%);
        background-position:
            calc(100% - 18px) calc(1em + 2px),
            calc(100% - 13px) calc(1em + 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
        padding-right: 38px;
    }

    .as-card textarea{
        resize: vertical;
        min-height: 86px;
    }

    .as-card input::placeholder,
    .as-card textarea::placeholder{
        color: rgba(100, 116, 139, .85);
    }

    .as-card input:focus,
    .as-card select:focus,
    .as-card textarea:focus{
        border-color: rgba(31, 60, 136, 0.65);
        outline: none;
        box-shadow: 0 0 0 4px rgba(31, 60, 136, 0.12);
    }

    .as-card .as-row{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .as-card .as-row-3{
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
    }

    .as-card .as-btn-submit{
        width: 100%;
        background: linear-gradient(135deg, var(--as-brand-700), var(--as-brand-600));
        color: #fff;
        border: none;
        padding: 13px 18px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 800;
        letter-spacing: .2px;
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        box-shadow: 0 14px 32px rgba(31, 60, 136, 0.22);
        margin-top: 6px;
    }

    .as-card .as-btn-submit:hover{
        transform: translateY(-1px);
        filter: brightness(0.98);
        box-shadow: 0 18px 42px rgba(31, 60, 136, 0.28);
    }

    .as-card .as-btn-submit:active{ transform: translateY(0); }

    .as-card .as-tech-input{
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 10px;
        align-items: center;
    }

    .as-card .as-btn-icon,
    .as-card .as-btn-secondary{
        border: 1px solid rgba(203, 213, 225, 0.95);
        background: rgba(255, 255, 255, 0.95);
        color: var(--as-ink-700);
        padding: 11px 12px;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
        white-space: nowrap;
        font-family: inherit;
    }

    .as-card .as-btn-secondary{
        font-size: 12px;
        letter-spacing: .2px;
        padding: 11px 14px;
    }

    .as-card .as-btn-icon{
        width: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        line-height: 1;
    }

    .as-card .as-btn-icon:hover,
    .as-card .as-btn-secondary:hover{
        border-color: rgba(31, 60, 136, 0.25);
        box-shadow: 0 10px 26px rgba(2, 6, 23, 0.10);
        transform: translateY(-1px);
    }

    .as-card .as-tech-tags{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .as-card .as-tech-tag{
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 999px;
        border: 1px solid rgba(229, 231, 235, 0.95);
        background: rgba(31, 60, 136, 0.06);
        color: var(--as-ink-900);
        font-weight: 800;
        font-size: 12px;
    }

    .as-card .as-tech-tag__text{
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .as-card .as-tech-tag__actions{
        display: inline-flex;
        gap: 6px;
        opacity: 0;
        pointer-events: none;
        transition: opacity .12s ease;
    }

    .as-card .as-tech-tag:hover .as-tech-tag__actions{
        opacity: 1;
        pointer-events: auto;
    }

    .as-card .as-tech-tag__btn{
        border: 1px solid rgba(203, 213, 225, 0.95);
        background: rgba(255, 255, 255, 0.95);
        color: var(--as-ink-700);
        padding: 6px 9px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 11px;
        cursor: pointer;
        font-family: inherit;
    }

    .as-card .as-tech-tag__btn--danger{
        border-color: rgba(239, 68, 68, 0.30);
        color: #b91c1c;
        background: rgba(239, 68, 68, 0.08);
    }

    .as-card .as-alert{
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 10px 12px;
        border-radius: var(--as-radius-10);
        font-size: 13px;
        margin-bottom: 16px;
    }

    .as-card .as-error{
        display: block;
        color: #b91c1c;
        font-size: 12px;
        margin-top: 6px;
    }

    .as-card .as-input-error{
        border-color: rgba(239, 68, 68, 0.6) !important;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.10);
    }

    @media (max-width: 768px){
        .as-card{ margin: 8px 0 24px; padding: 18px; }
        .as-card .as-row,
        .as-card .as-row-3{ grid-template-columns: 1fr; }
        .as-card .as-tech-input{ grid-template-columns: 1fr auto; }
        .as-card .as-btn-secondary{ display: none; }
    }
</style>

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
<?php flash_consumir_form(); ?>
