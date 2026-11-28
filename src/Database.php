<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Módulo centralizado de conexión a base de datos (src/Database.php)
 * 
 * Requisitos DWS RA6 (a, b) y Seguridad:
 * 1. Conexión centralizada mediante PDO (Patrón Singleton).
 * 2. Manejo de errores por excepción: PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION.
 * 3. Modo de fetch asociativo por defecto: PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC.
 * 4. Forzado de codificación UTF-8: utf8mb4 (DSN + INIT_COMMAND).
 * 5. Captura de PDOException: registro técnico en error_log y pantalla amigable al usuario (sin fuga de credenciales).
 */

require_once __DIR__ . '/../config/config.php';

class Database
{
    private static ?PDO $instance = null;

    /**
     * Retorna la instancia única de conexión PDO.
     *
     * @return PDO Conexión PDO activa.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host    = defined('DB_HOST') ? DB_HOST : 'localhost';
            $port    = defined('DB_PORT') ? (string) DB_PORT : '3306';
            $dbname  = defined('DB_NAME') ? DB_NAME : 'flock_db';
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $user    = defined('DB_USER') ? DB_USER : 'root';
            $pass    = defined('DB_PASS') ? DB_PASS : '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Registro técnico exclusivo en el log del servidor (sin exponer datos al cliente)
                error_log('[FLOCK Database Connection Error] ' . $e->getMessage());

                // Muestra la pantalla de error amigable sin exponer contraseñas ni rutas internas
                self::renderFriendlyError();
                exit;
            }
        }

        return self::$instance;
    }

    /**
     * Renderiza una pantalla de error amigable con código HTTP 500 para el usuario.
     */
    private static function renderFriendlyError(): void
    {
        if (!headers_sent()) {
            http_response_code(500);
        }
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Servicio no disponible | FLOCK — Agenda Cultural</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f9fa;
                    font-family: system-ui, -apple-system, sans-serif;
                }
                .error-card {
                    max-width: 540px;
                    border-radius: 16px;
                }
            </style>
        </head>
        <body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
            <div class="card border p-4 p-md-5 text-center error-card bg-white">
                <h1 class="h4 fw-bold text-dark mb-2">Servicio temporalmente no disponible</h1>
                <p class="text-muted mb-4">
                    Estamos realizando labores de mantenimiento o experimentando una incidencia técnica momentánea con el servidor municipal. Por favor, inténtalo de nuevo en unos minutos.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="index.php" class="btn btn-primary px-4">
                        Reintentar
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

/**
 * Función global para compatibilidad con todo el proyecto.
 */
function obtenerConexion(): PDO
{
    return Database::getConnection();
}
