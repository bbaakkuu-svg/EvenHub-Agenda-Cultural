<?php
declare(strict_types=1);

/**
 * Controlador de Cierre de Sesión (public/logout.php)
 * Destruye la sesión activa y redirige al formulario de acceso.
 */

require_once __DIR__ . '/../includes/auth.php';

iniciarSesionSegura();
cerrarSesion();

iniciarSesionSegura();
$_SESSION['flash_aviso'] = 'Ha cerrado su sesión de forma segura.';

header('Location: login.php');
exit;
