<?php
declare(strict_types=1);

/**
 * Módulo de Autenticación y Control de Sesiones.
 * FLOCK — Agenda Cultural Municipal
 *
 */

require_once __DIR__ . '/conexion.php';

const TIEMPO_INACTIVIDAD_SESION = 1800; 

/**
 * Inicia la sesión PHP con directivas de cookies seguras.
 * Aplica además el control de expiración por inactividad.
 */
function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            // Directivas de seguridad para la cookie de sesión (HttpOnly, SameSite, Secure)
            $esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

            session_set_cookie_params([
                'lifetime' => 0,              // Cookie de sesión
                'path'     => '/',
                'domain'   => '',
                'secure'   => $esHttps,       // Solo por HTTPS si está disponible
                'httponly' => true,           // Inaccesible desde JavaScript (mitiga robo de sesión por XSS)
                'samesite' => 'Lax'           // Protección básica contra CSRF
            ]);
            session_start();
        }
    }

    // Comprobar expiración por inactividad si el usuario está autenticado
    if (!empty($_SESSION['usuario'])) {
        $ahora = time();
        if (isset($_SESSION['ultimo_acceso']) && ($ahora - (int) $_SESSION['ultimo_acceso'] > TIEMPO_INACTIVIDAD_SESION)) {
            // Sesión caducada por inactividad
            cerrarSesion();
            iniciarSesionSegura();
            $_SESSION['flash_aviso'] = 'Su sesión ha expirado por inactividad (30 min). Por favor, inicie sesión nuevamente.';
            return;
        }
        // Actualizar marca de tiempo de última actividad
        $_SESSION['ultimo_acceso'] = $ahora;
    }
}

/**
 * Comprueba si hay un usuario autenticado.
 */
function estaAutenticado(): bool
{
    iniciarSesionSegura();
    return !empty($_SESSION['usuario']['id']);
}

/**
 * Devuelve los datos del usuario autenticado o null si es anónimo.
 *
 * @return array{id: int, username: string, nombre_completo: string, email: string, rol: string}|null
 */
function usuarioAutenticado(): ?array
{
    iniciarSesionSegura();
    return $_SESSION['usuario'] ?? null;
}

/**
 * Intenta autenticar a un usuario con sus credenciales frente a la base de datos.
 *
 * @param string $username Nombre de usuario
 * @param string $password Contraseña en texto plano
 * @return array{exito: bool, error: ?string, usuario: ?array}
 */
function intentarLogin(string $username, string $password): array
{
    iniciarSesionSegura();

    $username = trim($username);
    $password = trim($password);

    if ($username === '' || $password === '') {
        return [
            'exito'   => false,
            'error'   => 'Debe indicar el usuario y la contraseña.',
            'usuario' => null
        ];
    }

    try {
        $pdo = obtenerConexion();
        $stmt = $pdo->prepare(
            'SELECT id, username, password_hash, nombre_completo, email, rol 
             FROM usuarios 
             WHERE username = :username 
             LIMIT 1'
        );
        $stmt->execute([':username' => $username]);
        /** @var array<string, mixed>|false $usuario */
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, (string) $usuario['password_hash'])) {
            // Regenerar ID de sesión inmediatamente para mitigar Session Fixation
            if (!headers_sent()) {
                session_regenerate_id(true);
            }

            // Almacenar únicamente datos necesarios en la sesión (nunca el hash de la contraseña)
            $_SESSION['usuario'] = [
                'id'              => (int) $usuario['id'],
                'username'        => (string) $usuario['username'],
                'nombre_completo' => (string) $usuario['nombre_completo'],
                'email'           => (string) $usuario['email'],
                'rol'             => (string) $usuario['rol']
            ];
            $_SESSION['ultimo_acceso'] = time();

            return [
                'exito'   => true,
                'error'   => null,
                'usuario' => $_SESSION['usuario']
            ];
        }

        return [
            'exito'   => false,
            'error'   => 'Credenciales incorrectas. Verifique su usuario y contraseña.',
            'usuario' => null
        ];
    } catch (PDOException $e) {
        // En producción no se exponen detalles de la BD al usuario
        error_log('Error en login PDO: ' . $e->getMessage());
        return [
            'exito'   => false,
            'error'   => 'Error de comunicación con el servicio de autenticación.',
            'usuario' => null
        ];
    }
}

/**
 * Si no está autenticado, redirige al formulario de login con mensaje flash.
 *
 * @param string|null $rolRequerido Rol necesario ('admin' o 'gestor'). Si es null, cualquier rol válido accede.
 * @param string $rutaLogin Ruta relativa al controlador login.php (por defecto 'login.php' o '../login.php')
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
 * Cierra la sesión de forma limpia, vaciando $_SESSION y destruyendo la cookie.
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
