<?php
declare(strict_types=1);

/**
 * Controlador del Catálogo de Eventos (public/eventos.php)
 * Recibe filtros GET (?categoria, ?q), consulta el Modelo Evento y renderiza la lista.
 */

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Categoria.php';

const LONGITUD_MAX_BUSQUEDA = 100;

/**
 * Recupera, valida y sanea los parámetros GET de búsqueda y categoría.
 *
 * @return array{categoria: int|string|null, q: string, avisos: string[]}
 */
function leerFiltros(): array
{
    $avisos = [];
    $categoriaBruta = $_GET['categoria'] ?? '';
    $q = $_GET['q'] ?? '';

    $categoria = null;
    if ($categoriaBruta !== '') {
        $catStr = trim((string) $categoriaBruta);
        if (ctype_digit($catStr) && (int) $catStr > 0) {
            $categoria = (int) $catStr;
        } elseif (preg_match('/^[a-z0-9-]+$/i', $catStr)) {
            $categoria = strtolower($catStr);
        } else {
            $avisos[] = 'La categoría indicada no es válida; se muestran todas.';
        }
    }

    if (!is_string($q)) {
        $avisos[] = 'El término de búsqueda no es válido y se ha ignorado.';
        $q = '';
    }

    $q = preg_replace('/[\x00-\x1F\x7F]/u', '', $q) ?? '';
    $q = mb_substr(trim($q), 0, LONGITUD_MAX_BUSQUEDA);

    return ['categoria' => $categoria, 'q' => $q, 'avisos' => $avisos];
}

$filtros         = leerFiltros();
$categoriaSel    = $filtros['categoria'];
$q               = $filtros['q'];
$avisos          = $filtros['avisos'];
$hayFiltros      = ($categoriaSel !== null || $q !== '');
$eventos         = [];
$categorias      = [];
$categoriaActiva = null;
$error           = null;

try {
    $categorias = Categoria::todas();
    $eventos    = Evento::obtenerConFiltros($categoriaSel, $q);

    if ($categoriaSel !== null) {
        $categoriaActiva = Categoria::buscarPorIdOSlug($categoriaSel);
    }
} catch (Throwable $ex) {
    error_log('[FLOCK-Eventos] ' . $ex->getMessage());
    $error = 'No hemos podido cargar los eventos en este momento. Inténtalo de nuevo más tarde.';
}

$pageTitle = 'Agenda cultural';
$activeNav = 'eventos';
$basePath  = '';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/eventos/lista.php';
require_once __DIR__ . '/../views/layout/footer.php';