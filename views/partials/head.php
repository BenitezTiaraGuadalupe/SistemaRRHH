<?php
/**
 * Apertura del documento HTML y hojas de estilo.
 * Espera: $pageTitle, $moduleStylesheets (opcional).
 */
require_once dirname(__DIR__, 2) . '/lib/view_helpers.php';

$pageTitle = isset($pageTitle) ? $pageTitle : 'TalentLink';
$moduleStylesheets = isset($moduleStylesheets) && is_array($moduleStylesheets) ? $moduleStylesheets : array();
$stylesheets = view_stylesheets($moduleStylesheets);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php foreach ($stylesheets as $cssHref) : ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="app">
