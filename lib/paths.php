<?php

/**
 * Ruta web base del proyecto (ej. "" o "/SistemaRRHH"), sin barra final.
 */
function app_web_base()
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $docRoot = str_replace('\\', '/', rtrim((string) realpath($_SERVER['DOCUMENT_ROOT']), '/'));
    $projectRoot = str_replace('\\', '/', rtrim((string) realpath(dirname(__DIR__)), '/'));

    if ($docRoot !== '' && $projectRoot !== '' && strpos($projectRoot, $docRoot) === 0) {
        $base = substr($projectRoot, strlen($docRoot));
    } else {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $subs = array('/candidatos');
        foreach ($subs as $sub) {
            if (strlen($base) >= strlen($sub) && substr($base, -strlen($sub)) === $sub) {
                $base = dirname($base);
                break;
            }
        }
    }

    if ($base === '/' || $base === '.') {
        $base = '';
    }

    return $base;
}

/**
 * URL absoluta desde la raíz web del proyecto.
 */
function app_url($path)
{
    return app_web_base() . '/' . ltrim(str_replace('\\', '/', $path), '/');
}

/**
 * Ruta al CSS de un módulo (ej. views/auth/auth_styles.css).
 */
function app_module_styles($modulo)
{
    return app_url('views/' . $modulo . '/' . $modulo . '_styles.css');
}
