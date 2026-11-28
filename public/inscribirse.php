<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Controlador de Inscripción de Asistentes a Eventos (public/inscribirse.php)
 * Issue #11 — feat(backend): attendee registration and seat reservation system
 *
 * Flujo:
 *   GET  ?evento_id=N  → muestra el formulario de reserva con datos del evento
 *   POST              → valida, registra la inscripción y redirige a confirmación
 */

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';

// ── Protección CSRF básica ────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 1. Obtener y validar evento_id ────────────────────────────────────────────
$eventoId = filter_input(INPUT_GET,  'evento_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
         ?? filter_input(INPUT_POST, 'evento_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$eventoId) {
    mostrar404('No se ha especificado un evento válido para la inscripción.');
}

// ── 2. Cargar datos del evento desde la BD ────────────────────────────────────
try {
    $evento = Evento::obtenerPorId((int) $eventoId);
} catch (Throwable $ex) {
    error_log('[FLOCK Inscripcion] ' . $ex->getMessage());
    mostrar404('Error al consultar el evento. Inténtalo de nuevo más tarde.');
}

if (!$evento) {
    mostrar404('El evento solicitado no existe o ha sido cancelado.');
}

// ── 3. Verificar que el evento admite inscripciones ───────────────────────────
$aforoTotal        = (int) $evento['aforo_total'];
$plazasOcupadas    = (int) ($evento['plazas_ocupadas'] ?? 0);
$plazasDisponibles = max(0, $aforoTotal - $plazasOcupadas);

if ($evento['estado'] !== 'activo') {
    mostrar404('Este evento no está disponible para inscripciones en este momento.');
}

// ── 4. Gestión del formulario POST ────────────────────────────────────────────
$error  = null;
$campos = [
    'nombre'        => '',
    'email'         => '',
    'telefono'      => '',
    'plazas'        => 1,
    'observaciones' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4a. Leer y sanear campos
    $campos['nombre']        = trim(filter_input(INPUT_POST, 'nombre',        FILTER_DEFAULT) ?? '');
    $campos['email']         = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));
    $campos['telefono']      = trim(filter_input(INPUT_POST, 'telefono',      FILTER_DEFAULT) ?? '');
    $campos['plazas']        = max(1, min(5, (int) filter_input(INPUT_POST, 'plazas', FILTER_VALIDATE_INT)));
    $campos['observaciones'] = trim(filter_input(INPUT_POST, 'observaciones', FILTER_DEFAULT) ?? '');

    // 4b. Validación en servidor
    if (mb_strlen($campos['nombre']) < 3 || mb_strlen($campos['nombre']) > 100) {
        $error = 'El nombre debe tener entre 3 y 100 caracteres.';
    } elseif (!filter_var($campos['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no tiene un formato válido.';
    } elseif (!preg_match('/^\+?[\d\s\-]{7,20}$/', $campos['telefono'])) {
        $error = 'El teléfono no tiene un formato válido (solo dígitos, espacios o guiones).';
    } elseif ($campos['plazas'] < 1 || $campos['plazas'] > 5) {
        $error = 'Solo se pueden reservar entre 1 y 5 plazas por inscripción.';
    } elseif ($campos['plazas'] > $plazasDisponibles) {
        $error = "No hay suficientes plazas disponibles. Quedan {$plazasDisponibles} plazas libres.";
    }

    // 4c. Si pasa validación → registrar en transacción
    if ($error === null) {
        try {
            $pdo = obtenerConexion();
            $pdo->beginTransaction();

            // Re-verificar aforo dentro de la transacción para evitar race condition
            $stmtAforo = $pdo->prepare(
                "SELECT COALESCE(SUM(plazas_reservadas), 0)
                 FROM inscripciones
                 WHERE evento_id = :id AND estado = 'confirmada'
                 FOR UPDATE"
            );
            $stmtAforo->execute([':id' => $eventoId]);
            $ocupadasActuales = (int) $stmtAforo->fetchColumn();
            $disponiblesActuales = max(0, $aforoTotal - $ocupadasActuales);

            if ($campos['plazas'] > $disponiblesActuales) {
                $pdo->rollBack();
                $error = "Las plazas solicitadas ya no están disponibles. Quedan {$disponiblesActuales} plazas.";
            } else {
                $localizador = Inscripcion::registrar(
                    $eventoId,
                    $campos['nombre'],
                    $campos['email'],
                    $campos['telefono'],
                    $campos['plazas'],
                    $campos['observaciones'] !== '' ? $campos['observaciones'] : null
                );
                $pdo->commit();

                // Redirigir a pantalla de confirmación
                header('Location: confirmacion.php?loc=' . urlencode($localizador));
                exit;
            }
        } catch (Throwable $ex) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('[FLOCK Inscripcion POST] ' . $ex->getMessage());
            $error = 'Se produjo un error al procesar tu inscripción. Por favor, inténtalo de nuevo.';
        }
    }
}

// ── 5. Preparar datos para la vista ──────────────────────────────────────────
$pageTitle = 'Reservar plaza — ' . ($evento['titulo'] ?? 'Evento');
$activeNav = 'eventos';
$colorBadge = !empty($evento['color_badge']) ? $evento['color_badge'] : '#1d3557';

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/inscripcion/formulario.php';
require_once __DIR__ . '/../views/layout/footer.php';
