<?php
declare(strict_types=1);

/**
 * Controlador de Cierre de Sesión (public/logout.php).
 * FLOCK — Agenda Cultural Municipal
 */

require_once __DIR__ . '/../includes/auth.php';

iniciarSesionSegura();
cerrarSesion();

// Iniciar sesión limpia para almacenar el mensaje flash de despedida
iniciarSesionSegura();
$_SESSION['flash_aviso'] = 'Ha cerrado su sesión de forma segura.';

header('Location: login.php');
exit;
