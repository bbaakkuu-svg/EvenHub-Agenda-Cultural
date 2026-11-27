<?php
declare(strict_types=1);

/**
 * Modelo Usuario
 * Gestiona la autenticación y permisos de usuarios en la tabla 'usuarios'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Usuario
{
    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @param string $username
     * @return array<string, mixed>|null
     */
    public static function buscarPorUsername(string $username): ?array
    {
        $stmt = obtenerConexion()->prepare(
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
        $stmt = obtenerConexion()->prepare(
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
     * Verifica credenciales contra la base de datos.
     *
     * @param string $username
     * @param string $password
     * @return array<string, mixed>|null Datos del usuario sin hash de contraseña
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
