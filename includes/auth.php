<?php
declare(strict_types=1);

/**
 * Módulo de Autenticación y Control de Sesiones.
 * Gestiona sesiones seguras (HttpOnly, SameSite, regeneración de ID), inactividad y RBAC.
 */

require_once __DIR__ . '/../models/Usuario.php';

const TIEMPO_INACTIVIDAD_SESION = 1800; // 30 minutos

/**
 * Inicia la sesión PHP con directivas de cookies seguras y verifica inactividad.
 */
function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        $esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $esHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    if (!empty($_SESSION['usuario'])) {
        $ahora = time();
        if (isset($_SESSION['ultimo_acceso']) && ($ahora - (int) $_SESSION['ultimo_acceso'] > TIEMPO_INACTIVIDAD_SESION)) {
            cerrarSesion();
            iniciarSesionSegura();
            $_SESSION['flash_aviso'] = 'Su sesión ha expirado por inactividad (30 min). Por favor, inicie sesión nuevamente.';
            return;
        }
        $_SESSION['ultimo_acceso'] = $ahora;
    }
}

/**
 * Comprueba si hay un usuario autenticado en la sesión activa.
 */
function estaAutenticado(): bool
{
    iniciarSesionSegura();
    return !empty($_SESSION['usuario']['id']);
}

/**
 * Devuelve los datos del usuario en sesión o null si es anónimo.
 *
 * @return array{id: int, username: string, nombre_completo: string, email: string, rol: string}|null
 */
function usuarioAutenticado(): ?array
{
    iniciarSesionSegura();
    return $_SESSION['usuario'] ?? null;
}

/**
 * Valida credenciales contra el modelo Usuario y almacena la sesión.
 *
 * @param string $username
 * @param string $password
 * @return array{exito: bool, error: ?string, usuario: ?array}
 */
function intentarLogin(string $username, string $password): array
{
    iniciarSesionSegura();

    $username = trim($username);
    $password = trim($password);

    if ($username === '' || $password === '') {
        return ['exito' => false, 'error' => 'Debe indicar el usuario y la contraseña.', 'usuario' => null];
    }

    try {
        $usuario = Usuario::buscarPorUsername($username);

        if ($usuario && password_verify($password, (string) $usuario['password_hash'])) {
            if (!headers_sent()) {
                session_regenerate_id(true);
            }

            $_SESSION['usuario'] = [
                'id'              => (int) $usuario['id'],
                'username'        => (string) $usuario['username'],
                'nombre_completo' => (string) $usuario['nombre_completo'],
                'email'           => (string) $usuario['email'],
                'rol'             => (string) $usuario['rol']
            ];
            $_SESSION['ultimo_acceso'] = time();

            return ['exito' => true, 'error' => null, 'usuario' => $_SESSION['usuario']];
        }

        return ['exito' => false, 'error' => 'Credenciales incorrectas. Verifique su usuario y contraseña.', 'usuario' => null];
    } catch (Throwable $e) {
        error_log('Error en autenticación: ' . $e->getMessage());
        return ['exito' => false, 'error' => 'Error de comunicación con el servicio de autenticación.', 'usuario' => null];
    }
}

/**
 * Guardián de control de acceso. Redirige a login si no hay sesión activa.
 *
 * @param string|null $rolRequerido 'admin' o 'gestor' (null = cualquier rol autenticado)
 * @param string $rutaLogin Ruta relativa al controlador de login
 */
function requerirAutenticacion(?string $rolRequerido = null, string $rutaLogin = 'login.php'): void
{
    iniciarSesionSegura();

    if (!estaAutenticado()) {
        $_SESSION['flash_error'] = 'Debe iniciar sesión para acceder al panel de administración.';
        header('Location: ' . $rutaLogin);
        exit;
    }

    if ($rolRequerido !== null) {
        $usuario = usuarioAutenticado();
        if ($usuario === null || $usuario['rol'] !== $rolRequerido) {
            $_SESSION['flash_error'] = 'Acceso denegado: se requieren permisos de ' . ucfirst($rolRequerido) . '.';
            $rutaAdmin = str_ends_with($rutaLogin, '../login.php') ? 'index.php' : 'admin/index.php';
            header('Location: ' . $rutaAdmin);
            exit;
        }
    }
}

/**
 * Destruye la sesión activa y elimina su cookie asociada.
 */
function cerrarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }

    $_SESSION = [];

    if (!headers_sent() && ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}
