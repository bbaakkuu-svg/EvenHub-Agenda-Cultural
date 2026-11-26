<?php
declare(strict_types=1);

/**
 * Conexión a la base de datos MySQL mediante PDO (Singleton).
 * FLOCK — Agenda Cultural Municipal
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Devuelve la instancia única de conexión PDO.
 *
 * @throws PDOException Si falla la conexión.
 * @return PDO
 */
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

        $pdo = new PDO($dsn, $user, $pass, $opciones);
    }

    return $pdo;
}
