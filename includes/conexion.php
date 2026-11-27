<?php
declare(strict_types=1);

/**
 * Conexión Singleton a la base de datos MySQL mediante PDO.
 * Proporciona acceso centralizado con excepciones activas, emulación desactivada y UTF-8.
 */

require_once __DIR__ . '/../config/config.php';

function obtenerConexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host    = defined('DB_HOST') ? DB_HOST : 'localhost';
        $port    = defined('DB_PORT') ? DB_PORT : '3306';
        $dbname  = defined('DB_NAME') ? DB_NAME : 'flock_db';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $user    = defined('DB_USER') ? DB_USER : 'root';
        $pass    = defined('DB_PASS') ? DB_PASS : '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $opciones);
        } catch (PDOException $e) {
            error_log('Error de conexión PDO: ' . $e->getMessage());
            throw new PDOException('No fue posible conectar con la base de datos municipal.');
        }
    }

    return $pdo;
}
