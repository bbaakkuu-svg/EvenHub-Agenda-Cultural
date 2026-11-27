<?php
declare(strict_types=1);

/**
 * Controlador Principal del Panel de Administración (public/admin/index.php)
 * Panel protegido para el personal municipal con métricas y próximos eventos.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../models/Evento.php';
require_once __DIR__ . '/../../models/Inscripcion.php';
require_once __DIR__ . '/../../models/EspacioMunicipal.php';

requerirAutenticacion(null, '../login.php');

$usuario = usuarioAutenticado();
$error   = $_SESSION['flash_error'] ?? null;
$aviso   = $_SESSION['flash_aviso'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_aviso']);

$metricas = [
    'eventos_activos'   => 0,
    'total_reservas'    => 0,
    'plazas_reservadas' => 0,
    'total_espacios'    => 0
];
$proximosEventos = [];

try {
    $metricas['eventos_activos'] = Evento::contarActivos();

    $datosInscripciones = Inscripcion::obtenerMetricas();
    $metricas['total_reservas']    = $datosInscripciones['total_reservas'];
    $metricas['plazas_reservadas'] = $datosInscripciones['plazas_reservadas'];

    $metricas['total_espacios'] = EspacioMunicipal::contarTodos();
    $proximosEventos = Evento::proximos(5);
} catch (Throwable $e) {
    error_log('Error en dashboard admin: ' . $e->getMessage());
    $error = 'No fue posible sincronizar las métricas con la base de datos.';
}

$pageTitle = 'Panel de Gestión Cultural';
$activeNav = 'admin';
$basePath  = '../';

require_once __DIR__ . '/../../views/layout/header.php';
require_once __DIR__ . '/../../views/admin/dashboard.php';
require_once __DIR__ . '/../../views/layout/footer.php';
