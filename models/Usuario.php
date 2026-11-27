<?php
declare(strict_types=1);

/**
 * Modelo Usuario (models/Usuario.php)
 * FLOCK — Agenda Cultural Municipal
 *
 * Encapsula la persistencia y operaciones sobre la tabla 'usuarios'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Usuario
{
    /**
     * Busca un usuario por su nombre de usuario único.
     *
     * @param string $username
     * @return array<string, mixed>|null
     */
    public static function buscarPorUsername(string $username): ?array
    {
        $pdo = obtenerConexion();
        $stmt = $pdo->prepare(
            'SELECT id, username, password_hash, nombre_completo, email, rol, created_at
             FROM usuarios
             WHERE username = :username
             LIMIT 1'
        );
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Busca un usuario por su ID primario.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public static function buscarPorId(int $id): ?array
    {
        $pdo = obtenerConexion();
        $stmt = $pdo->prepare(
            'SELECT id, username, nombre_completo, email, rol, created_at
             FROM usuarios
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Verifica las credenciales de un usuario contra la base de datos.
     *
     * @param string $username
     * @param string $password
     * @return array<string, mixed>|null Devuelve el usuario (sin hash) o null si falla
     */
    public static function autenticar(string $username, string $password): ?array
    {
        $usuario = self::buscarPorUsername($username);
        if ($usuario && password_verify($password, (string) $usuario['password_hash'])) {
            unset($usuario['password_hash']);
            return $usuario;
        }

        return null;
    }
}
