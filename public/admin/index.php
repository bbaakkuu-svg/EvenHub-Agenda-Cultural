<?php
declare(strict_types=1);

/**
 * Controlador Principal del Panel de Administración (public/admin/index.php).
 * FLOCK — Agenda Cultural Municipal
 *
 * Aplica el patrón MVC: recupera el estado de sesión, consulta métricas
 * a través de los Modelos (Evento, Inscripcion, EspacioMunicipal) y carga la Vista.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../models/Evento.php';
require_once __DIR__ . '/../../models/Inscripcion.php';
require_once __DIR__ . '/../../models/EspacioMunicipal.php';

// Guardián de seguridad: Requiere estar autenticado; si no, redirige a ../login.php
requerirAutenticacion(null, '../login.php');

$usuario = usuarioAutenticado();
$error = $_SESSION['flash_error'] ?? null;
$aviso = $_SESSION['flash_aviso'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_aviso']);

// Consultas estadísticas para el dashboard delegando en la capa de Modelos
$metricas = [
    'eventos_activos' => 0,
    'total_reservas' => 0,
    'plazas_reservadas' => 0,
    'total_espacios' => 0
];
$proximosEventos = [];

try {
    // 1. Métrica: Eventos activos
    $metricas['eventos_activos'] = Evento::contarActivos();

    // 2. Métrica: Total de inscripciones y plazas solicitadas
    $datosInscripciones = Inscripcion::obtenerMetricas();
    $metricas['total_reservas'] = $datosInscripciones['total_reservas'];
    $metricas['plazas_reservadas'] = $datosInscripciones['plazas_reservadas'];

    // 3. Métrica: Espacios municipales
    $metricas['total_espacios'] = EspacioMunicipal::contarTodos();

    // 4. Listado de próximos 5 eventos programados
    $proximosEventos = Evento::proximos(5);

} catch (Throwable $e) {
    error_log('Error al cargar métricas del panel de admin (MVC): ' . $e->getMessage());
    $error = 'No fue posible sincronizar las métricas con la base de datos.';
}

$pageTitle = 'Panel de Gestión Cultural';
$activeNav = 'admin';
$basePath = '../';

require_once __DIR__ . '/../../views/layout/header.php';
require_once __DIR__ . '/../../views/admin/dashboard.php';
require_once __DIR__ . '/../../views/layout/footer.php';
