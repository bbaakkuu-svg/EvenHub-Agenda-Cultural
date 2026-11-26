<?php
declare(strict_types=1);

/**
 * Controlador del catálogo de eventos (public/eventos.php).
 *
 * Recibe ?categoria=(ID|slug) y ?q=texto por GET, aplica el ciclo
 * Recuperar -> Validar -> Sanitizar -> Procesar -> Feedback,
 * consulta la BD con sentencias preparadas y muestra la vista lista.php.
 */

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/helpers.php';

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
    $q              = $_GET['q'] ?? '';

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

/**
 * Eventos activos ordenados cronológicamente, con filtros opcionales.
 *
 * @param PDO             $pdo       Conexión activa.
 * @param int|string|null $categoria ID o slug de la categoría o null para todas.
 * @param string          $q         Palabra clave (vacía = sin filtro).
 * @return array<int, array<string, mixed>> Filas de eventos.
 */
function consultarEventos(PDO $pdo, int|string|null $categoria, string $q): array
{
    $sql = 'SELECT e.id, e.titulo, e.descripcion, e.imagen_url, e.imagen_url AS imagen,
                   e.fecha, e.hora, e.precio, e.aforo_total, e.aforo_total AS aforo,
                   e.estado,
                   em.nombre AS lugar, em.direccion,
                   c.id AS categoria_id, c.nombre AS categoria, c.slug AS categoria_slug, c.color_badge,
                   (e.aforo_total - COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0)) AS plazas_disponibles
            FROM eventos e
            INNER JOIN categorias c ON c.id = e.categoria_id
            INNER JOIN espacios_municipales em ON em.id = e.espacio_id
            LEFT JOIN inscripciones i ON i.evento_id = e.id
            WHERE e.estado = "activo"';

    $params = [];

    if ($categoria !== null) {
        if (is_int($categoria)) {
            $sql .= ' AND e.categoria_id = :categoria';
            $params[':categoria'] = $categoria;
        } else {
            $sql .= ' AND c.slug = :categoria_slug';
            $params[':categoria_slug'] = $categoria;
        }
    }

    if ($q !== '') {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $sql .= ' AND (e.titulo LIKE :q1 OR e.descripcion LIKE :q2 OR em.nombre LIKE :q3 OR c.nombre LIKE :q4)';
        $params[':q1'] = $like;
        $params[':q2'] = $like;
        $params[':q3'] = $like;
        $params[':q4'] = $like;
    }

    $sql .= ' GROUP BY e.id, c.id, em.id ORDER BY e.fecha ASC, e.hora ASC, e.id ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Categorías para referencias y desplegable del filtro.
 *
 * @param PDO $pdo Conexión activa.
 * @return array<int, array<string, mixed>> Filas (id, nombre, slug, color_badge).
 */
function consultarCategorias(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT id, nombre, slug, color_badge FROM categorias ORDER BY nombre ASC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ---- Flujo principal ----
$filtros         = leerFiltros();
$categoriaSel    = $filtros['categoria'];
$q               = $filtros['q'];
$avisos          = $filtros['avisos'];
$hayFiltros      = ($categoriaSel !== null || $q !== '');
$eventos         = [];
$categorias      = [];
$categoriaActiva = null;
$error           = null;

// 4. PROCESAR
try {
    $pdo        = obtenerConexion();
    $categorias = consultarCategorias($pdo);
    $eventos    = consultarEventos($pdo, $categoriaSel, $q);

    // Identificar si hay una categoría activa para el feedback visual
    if ($categoriaSel !== null) {
        foreach ($categorias as $cat) {
            if ((is_int($categoriaSel) && (int) $cat['id'] === $categoriaSel) ||
                (is_string($categoriaSel) && $cat['slug'] === $categoriaSel)) {
                $categoriaActiva = $cat;
                break;
            }
        }
    }
} catch (Throwable $ex) {
    error_log('[EventHub] ' . $ex->getMessage());
    $error = 'No hemos podido cargar los eventos en este momento. Inténtalo de nuevo más tarde.';
}

// 5. FEEDBACK
$pageTitle = 'Agenda cultural';
$activeNav = 'eventos';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/eventos/lista.php';
require_once __DIR__ . '/../views/layout/footer.php';
