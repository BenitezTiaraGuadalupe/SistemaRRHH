<?php

/**
 * Listado de candidatos (entrada directa desde el menú lateral).
 */
require_once dirname(__DIR__) . '/controllers/candidatosController.php';

(new CandidatosController())->index();
