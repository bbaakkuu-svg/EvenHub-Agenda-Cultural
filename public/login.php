<?php
declare(strict_types=1);

/**
 * Controlador de Inicio de Sesión (public/login.php)
 * Procesa el formulario de acceso para el personal municipal.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

iniciarSesionSegura();

if (estaAutenticado()) {
    header('Location: admin/index.php');
    exit;
}

$error = $_SESSION['flash_error'] ?? null;
$aviso = $_SESSION['flash_aviso'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_aviso']);

$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $resultado = intentarLogin($username, $password);

    if ($resultado['exito']) {
        header('Location: admin/index.php');
        exit;
    }

    $error = $resultado['error'];
}

$pageTitle = 'Acceso al Panel de Gestión';
$activeNav = 'login';
$basePath  = '';

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/auth/login.php';
require_once __DIR__ . '/../views/layout/footer.php';
