<?php
/**
 * FLOCK — Agenda Cultural Municipal
 * Archivo de Configuración de Ejemplo (Plantilla para Repositorio)
 * 
 * Copia este archivo como 'config.php' y ajusta tus credenciales locales.
 * 'config.php' está excluido en .gitignore para no exponer secretos.
 */

// Configuración de Base de Datos (MySQL / MariaDB en XAMPP)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'flock_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración General de la Aplicación
define('APP_NAME', 'FLOCK — Agenda Cultural');
define('APP_CITY', 'Villaverde de la Ribera');
define('APP_URL', 'http://localhost/EvenHub-Agenda-Cultural/public');

// Modo depuración (true en desarrollo local, false en producción/entrega final)
define('APP_DEBUG', true);
