<?php
declare(strict_types=1);

/**
 * Controlador de Categorías Culturales (public/categorias.php)
 * Muestra el catálogo de disciplinas artísticas de la agenda municipal.
 */

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Categoria.php';

$error = null;
$categorias = [];

try {
    $categorias = Categoria::listarConTotalEventos();
} catch (Throwable $ex) {
    error_log('[FLOCK-Categorias] ' . $ex->getMessage());
    $error = 'No fue posible cargar las categorías en este momento.';
}

$pageTitle = 'Categorías Culturales';
$activeNav = 'categorias';
$basePath  = '';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/categorias/lista.php';
require_once __DIR__ . '/../views/layout/footer.php';
