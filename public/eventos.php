<?php
declare(strict_types=1);

/**
 * Controlador del catálogo de eventos (public/eventos.php).
 * FLOCK — Agenda Cultural Municipal
 *
 * Recibe ?categoria=(ID|slug) y ?q=texto por GET, aplica el ciclo
 * Recuperar -> Validar -> Sanitizar -> Procesar (Modelo) -> Feedback (Vista).
 */

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Categoria.php';

// Constante global: longitud máxima de la búsqueda.
const LONGITUD_MAX_BUSQUEDA = 100;

/**
 * Recupera, valida y sanea los parámetros GET.
 * Admite filtro de categoría numérico (ID) o slug textual.
 *
 * @return array{categoria: int|string|null, q: string, avisos: string[]}
 */
function leerFiltros(): array
{
    $avisos = [];

    // 1. RECUPERAR
    $categoriaBruta = $_GET['categoria'] ?? '';
    $q = $_GET['q'] ?? '';

    // 2. VALIDAR categoría: entero positivo o slug alfanumérico
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

    // 2. VALIDAR búsqueda: debe ser texto (descarta ?q[]=x)
    if (!is_string($q)) {
        $avisos[] = 'La búsqueda no es válida y se ha ignorado.';
        $q = '';
    }

    // 3. SANEAR: sin caracteres de control, sin espacios sobrantes, longitud acotada.
    $q = preg_replace('/[\x00-\x1F\x7F]/u', '', $q) ?? '';
    $q = mb_substr(trim($q), 0, LONGITUD_MAX_BUSQUEDA);

    return ['categoria' => $categoria, 'q' => $q, 'avisos' => $avisos];
}

// ---- Flujo principal ----
$filtros = leerFiltros();
$categoriaSel = $filtros['categoria'];
$q = $filtros['q'];
$avisos = $filtros['avisos'];
$hayFiltros = ($categoriaSel !== null || $q !== '');
$eventos = [];
$categorias = [];
$categoriaActiva = null;
$error = null;

// 4. PROCESAR: Delegación en la capa Modelo (MVC)
try {
    $categorias = Categoria::todas();
    $eventos = Evento::obtenerConFiltros($categoriaSel, $q);

    // Identificar si hay una categoría activa para el feedback visual
    if ($categoriaSel !== null) {
        $categoriaActiva = Categoria::buscarPorIdOSlug($categoriaSel);
    }
} catch (Throwable $ex) {
    error_log('[FLOCK-Eventos] ' . $ex->getMessage());
    $error = 'No hemos podido cargar los eventos en este momento. Inténtalo de nuevo más tarde.';
}

// 5. FEEDBACK: Carga de la capa Vista
$pageTitle = 'Agenda cultural';
$activeNav = 'eventos';
$basePath = '';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/eventos/lista.php';
require_once __DIR__ . '/../views/layout/footer.php';
