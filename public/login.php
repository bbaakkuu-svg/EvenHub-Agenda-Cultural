<?php
declare(strict_types=1);

/**
 * Controlador de Inicio de Sesión (public/login.php).
 * FLOCK — Agenda Cultural Municipal
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

iniciarSesionSegura();

// Si ya está autenticado, redirigir directamente al panel de administración
if (estaAutenticado()) {
    header('Location: admin/index.php');
    exit;
}

$error = null;
$aviso = $_SESSION['flash_aviso'] ?? null;
unset($_SESSION['flash_aviso']);

if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$username = '';

// Procesamiento de formulario por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $resultado = intentarLogin($username, $password);

    if ($resultado['exito']) {
        // Redirección tras login exitoso (Patrón PRG)
        header('Location: admin/index.php');
        exit;
    }

    $error = $resultado['error'];
}

$pageTitle = 'Acceso al Panel de Gestión';
$activeNav = 'login';
$basePath = '';

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/auth/login.php';
require_once __DIR__ . '/../views/layout/footer.php';
