<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Controlador de Categorías Culturales (public/categorias.php)
 *
 * Muestra el catálogo de temáticas y disciplinas culturales del municipio,
 * con el número de actividades programadas en cada una.
 */

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/helpers.php';

$error = null;
$categorias = [];

try {
    $pdo = obtenerConexion();

    // Consulta las categorías junto con el número de eventos activos programados
    $sql = 'SELECT 
                c.id, 
                c.nombre, 
                c.slug, 
                c.color_badge, 
                c.descripcion,
                COUNT(CASE WHEN e.estado = "activo" THEN e.id ELSE NULL END) AS total_eventos
            FROM categorias c
            LEFT JOIN eventos e ON e.categoria_id = c.id
            GROUP BY c.id, c.nombre, c.slug, c.color_badge, c.descripcion
            ORDER BY c.nombre ASC';

    $stmt = $pdo->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $ex) {
    error_log('[EventHub-Categorias] ' . $ex->getMessage());
    $error = 'No hemos podido cargar las categorías en este momento. Inténtalo de nuevo más tarde.';
}

$pageTitle = 'Categorías Culturales';
$activeNav = 'categorias';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/categorias/lista.php';
require_once __DIR__ . '/../views/layout/footer.php';
