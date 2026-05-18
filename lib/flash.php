<?php

/**
 * Mensajes flash en sesión: viven hasta el siguiente request donde se leen.
 *
 * Convenciones de claves:
 *   - 'exito'  : string con mensaje de éxito.
 *   - 'errors' : array key=>string con errores por campo (clave especial '_general').
 *   - 'old'    : array key=>mixed con valores enviados, para repoblar formularios.
 */

require_once __DIR__ . '/auth.php';

function flash_iniciar()
{
    auth_iniciar();
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = array();
    }
}

function flash_set($tipo, $valor)
{
    flash_iniciar();
    $_SESSION['flash'][$tipo] = $valor;
}

function flash_has($tipo)
{
    flash_iniciar();
    return array_key_exists($tipo, $_SESSION['flash']);
}

/**
 * Lee un flash y lo borra (one-shot).
 */
function flash_get($tipo, $default = null)
{
    flash_iniciar();
    if (!array_key_exists($tipo, $_SESSION['flash'])) {
        return $default;
    }
    $v = $_SESSION['flash'][$tipo];
    unset($_SESSION['flash'][$tipo]);
    return $v;
}

/**
 * Devuelve un valor del último POST guardado como 'old' (sin borrar).
 * Pensado para repintar inputs después de un redirect con errores.
 */
function flash_old($key, $default = '')
{
    flash_iniciar();
    $old = isset($_SESSION['flash']['old']) && is_array($_SESSION['flash']['old'])
        ? $_SESSION['flash']['old']
        : array();
    return array_key_exists($key, $old) ? $old[$key] : $default;
}

/**
 * Devuelve el array de errores (sin borrar) o un array vacío si no hay.
 */
function flash_errors()
{
    flash_iniciar();
    if (!isset($_SESSION['flash']['errors']) || !is_array($_SESSION['flash']['errors'])) {
        return array();
    }
    return $_SESSION['flash']['errors'];
}

function flash_error($key)
{
    $errs = flash_errors();
    return isset($errs[$key]) ? $errs[$key] : null;
}

/**
 * Limpia las claves de "errors" y "old" tras renderizar la vista que las usó.
 */
function flash_consumir_form()
{
    flash_iniciar();
    unset($_SESSION['flash']['errors']);
    unset($_SESSION['flash']['old']);
}
