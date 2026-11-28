<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Controlador de Pantalla de Confirmación de Inscripción (public/confirmacion.php)
 * Issue #11 — feat(backend): attendee registration and seat reservation system
 *
 * Recibe ?loc=FLK-YYYY-XXXXXX y muestra el resumen de la reserva confirmada.
 */

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Inscripcion.php';

// ── Validar parámetro 'loc' ───────────────────────────────────────────────────
$localizador = strtoupper(trim(filter_input(INPUT_GET, 'loc', FILTER_DEFAULT) ?? ''));

if (!preg_match('/^FLK-\d{4}-[A-F0-9]{6}$/', $localizador)) {
    mostrar404('El localizador de inscripción no es válido o no existe.');
}

// ── Buscar inscripción en la BD ───────────────────────────────────────────────
try {
    $inscripcion = Inscripcion::obtenerPorLocalizador($localizador);
} catch (Throwable $ex) {
    error_log('[FLOCK Confirmacion] ' . $ex->getMessage());
    mostrar404('Error al consultar la inscripción. Inténtalo de nuevo más tarde.');
}

if (!$inscripcion) {
    mostrar404('No se encontró ninguna inscripción con ese localizador.');
}

// ── Preparar vista ────────────────────────────────────────────────────────────
$pageTitle = 'Inscripción confirmada — ' . ($inscripcion['evento_titulo'] ?? 'Evento');
$activeNav = 'eventos';

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/inscripcion/confirmacion.php';
require_once __DIR__ . '/../views/layout/footer.php';
