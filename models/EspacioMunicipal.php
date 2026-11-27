<?php
declare(strict_types=1);

/**
 * Modelo EspacioMunicipal
 * Gestiona los recintos y sedes públicas en la tabla 'espacios_municipales'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class EspacioMunicipal
{
    /**
     * Devuelve el total de recintos municipales registrados.
     */
    public static function contarTodos(): int
    {
        return (int) obtenerConexion()
            ->query('SELECT COUNT(*) FROM espacios_municipales')
            ->fetchColumn();
    }

    /**
     * Lista todos los espacios municipales ordenados por nombre.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function todos(): array
    {
        return obtenerConexion()
            ->query('SELECT id, nombre, direccion, aforo_maximo, descripcion FROM espacios_municipales ORDER BY nombre ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un espacio municipal por su identificador único.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public static function buscarPorId(int $id): ?array
    {
        $stmt = obtenerConexion()->prepare(
            'SELECT id, nombre, direccion, aforo_maximo, descripcion 
             FROM espacios_municipales 
             WHERE id = :id 
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $espacio = $stmt->fetch(PDO::FETCH_ASSOC);

        return $espacio ?: null;
    }
}
