<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Controlador de Inscripción de Asistentes y Reserva de Plazas (public/inscribirse.php)
 * Issue #11
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';

// 1. Si llega un localizador por GET, mostrar la pantalla de confirmación de reserva
$localizadorGet = trim((string) filter_input(INPUT_GET, 'localizador', FILTER_UNSAFE_RAW));
if ($localizadorGet !== '') {
    try {
        $inscripcion = Inscripcion::obtenerPorLocalizador($localizadorGet);
    } catch (Throwable $ex) {
        error_log('[FLOCK Confirmación Inscripción] ' . $ex->getMessage());
        $inscripcion = null;
    }

    if (!$inscripcion) {
        mostrar404('No se ha encontrado ninguna reserva con el código de referencia indicado.');
        exit;
    }

    $pageTitle = 'Reserva Confirmada — ' . $inscripcion['localizador'];
    $activeNav = 'eventos';

    require_once __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/inscripciones/confirmacion.php';
    require_once __DIR__ . '/../views/layout/footer.php';
    exit;
}

// 2. Obtener y validar el identificador del evento
$rawEventoId = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? filter_input(INPUT_POST, 'evento_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : filter_input(INPUT_GET, 'evento_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($rawEventoId === false || $rawEventoId === null) {
    mostrar404('Identificador de evento no válido para realizar la inscripción.');
    exit;
}

$eventoId = (int) $rawEventoId;

// 3. Cargar datos del evento y verificar que esté activo
try {
    $evento = Evento::obtenerPorId($eventoId);
} catch (Throwable $ex) {
    error_log('[FLOCK Inscripción Evento] ' . $ex->getMessage());
    $evento = null;
}

if (!$evento || $evento['estado'] !== 'activo') {
    mostrar404('El evento solicitado no existe o no admite inscripciones.');
    exit;
}

$aforoTotal        = (int) $evento['aforo_total'];
$plazasReservadas  = (int) $evento['plazas_ocupadas'];
$plazasDisponibles = max(0, $aforoTotal - $plazasReservadas);
$estaAgotado       = ($plazasDisponibles === 0);

// Token CSRF para proteger el formulario de inscripción
if (empty($_SESSION['csrf_inscripcion'])) {
    $_SESSION['csrf_inscripcion'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_inscripcion'];

$errores = [];
$formData = [
    'nombre'        => '',
    'email'         => '',
    'telefono'      => '',
    'plazas'        => 1,
    'observaciones' => ''
];

// 4. Procesar envío POST del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenRecibido = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals($csrfToken, $tokenRecibido)) {
        $errores[] = 'La sesión del formulario ha caducado. Por favor, inténtalo de nuevo.';
    }

    $formData['nombre']        = trim((string) ($_POST['nombre'] ?? ''));
    $formData['email']         = trim((string) ($_POST['email'] ?? ''));
    $formData['telefono']      = trim((string) ($_POST['telefono'] ?? ''));
    $formData['plazas']        = (int) ($_POST['plazas'] ?? 1);
    $formData['observaciones'] = trim((string) ($_POST['observaciones'] ?? ''));

    if ($estaAgotado) {
        $errores[] = 'El evento tiene el aforo completo y no admite nuevas inscripciones.';
    }

    if (mb_strlen($formData['nombre']) < 2 || mb_strlen($formData['nombre']) > 100) {
        $errores[] = 'Introduce un nombre completo válido (entre 2 y 100 caracteres).';
    }

    if ($formData['email'] === '' || mb_strlen($formData['email']) > 120 || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Introduce una dirección de correo electrónico válida.';
    }

    if (!preg_match('/^[0-9+\s()-]{6,20}$/', $formData['telefono'])) {
        $errores[] = 'Introduce un teléfono de contacto válido (entre 6 y 20 caracteres numéricos).';
    }

    if ($formData['plazas'] < 1 || $formData['plazas'] > 5) {
        $errores[] = 'Puedes reservar entre 1 y 5 plazas por inscripción.';
    } elseif ($formData['plazas'] > $plazasDisponibles) {
        $errores[] = "Solo quedan {$plazasDisponibles} plazas disponibles para este evento.";
    }

    if (mb_strlen($formData['observaciones']) > 500) {
        $errores[] = 'Las observaciones no pueden superar los 500 caracteres.';
    }

    if (empty($errores)) {
        try {
            $localizador = Inscripcion::reservarConValidacion(
                $eventoId,
                $formData['nombre'],
                $formData['email'],
                $formData['telefono'],
                $formData['plazas'],
                $formData['observaciones']
            );

            // Regenerar token CSRF tras inserción exitosa y aplicar patrón PRG
            $_SESSION['csrf_inscripcion'] = bin2hex(random_bytes(32));
            header('Location: inscribirse.php?localizador=' . urlencode($localizador));
            exit;
        } catch (DomainException $de) {
            $errores[] = $de->getMessage();
            // Refrescar aforo tras rechazo de negocio
            $eventoActualizado = Evento::obtenerPorId($eventoId);
            if ($eventoActualizado) {
                $evento = $eventoActualizado;
                $aforoTotal        = (int) $evento['aforo_total'];
                $plazasReservadas  = (int) $evento['plazas_ocupadas'];
                $plazasDisponibles = max(0, $aforoTotal - $plazasReservadas);
                $estaAgotado       = ($plazasDisponibles === 0);
            }
        } catch (Throwable $ex) {
            error_log('[FLOCK Registro Inscripción] ' . $ex->getMessage());
            $errores[] = 'No se pudo completar la inscripción por un error interno. Inténtalo de nuevo más tarde.';
        }
    }
}

$pageTitle = 'Inscribirse — ' . $evento['titulo'];
$activeNav = 'eventos';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/inscripciones/formulario.php';
require_once __DIR__ . '/../views/layout/footer.php';
