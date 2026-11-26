<?php
declare(strict_types=1);

/**
 * Controlador Principal del Panel de Administración (public/admin/index.php).
 * FLOCK — Agenda Cultural Municipal
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/conexion.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Guardián de seguridad: Requiere estar autenticado; si no, redirige a ../login.php
requerirAutenticacion(null, '../login.php');

$usuario = usuarioAutenticado();
$error = $_SESSION['flash_error'] ?? null;
$aviso = $_SESSION['flash_aviso'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_aviso']);

// Consultas estadísticas para el dashboard administrativo
$metricas = [
    'eventos_activos'   => 0,
    'total_reservas'    => 0,
    'plazas_reservadas' => 0,
    'total_espacios'    => 0
];
$proximosEventos = [];

try {
    $pdo = obtenerConexion();

    // 1. Métrica: Eventos activos
    $stmt = $pdo->query("SELECT COUNT(*) FROM eventos WHERE estado = 'activo'");
    $metricas['eventos_activos'] = (int) $stmt->fetchColumn();

    // 2. Métrica: Total de inscripciones y plazas solicitadas
    $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(plazas_reservadas), 0) FROM inscripciones WHERE estado = 'confirmada'");
    $filaInscripciones = $stmt->fetch(PDO::FETCH_NUM);
    if ($filaInscripciones) {
        $metricas['total_reservas'] = (int) $filaInscripciones[0];
        $metricas['plazas_reservadas'] = (int) $filaInscripciones[1];
    }

    // 3. Métrica: Espacios municipales
    $stmt = $pdo->query("SELECT COUNT(*) FROM espacios_municipales");
    $metricas['total_espacios'] = (int) $stmt->fetchColumn();

    // 4. Listado de próximos 5 eventos programados
    $sqlEventos = "SELECT e.id, e.titulo, e.fecha, e.hora, e.aforo_total, e.precio, e.estado,
                          c.nombre AS categoria_nombre, c.color_badge,
                          esp.nombre AS espacio_nombre,
                          COALESCE(SUM(i.plazas_reservadas), 0) AS plazas_ocupadas
                   FROM eventos e
                   JOIN categorias c ON e.categoria_id = c.id
                   JOIN espacios_municipales esp ON e.espacio_id = esp.id
                   LEFT JOIN inscripciones i ON e.id = i.evento_id AND i.estado = 'confirmada'
                   GROUP BY e.id
                   ORDER BY e.fecha ASC, e.hora ASC
                   LIMIT 5";
    $stmt = $pdo->query($sqlEventos);
    $proximosEventos = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Error al cargar métricas del panel de admin: ' . $e->getMessage());
    $error = 'No fue posible sincronizar las métricas con la base de datos.';
}

$pageTitle = 'Panel de Gestión Cultural';
$activeNav = 'admin';
$basePath = '../';

require_once __DIR__ . '/../../views/layout/header.php';
require_once __DIR__ . '/../../views/admin/dashboard.php';
require_once __DIR__ . '/../../views/layout/footer.php';
