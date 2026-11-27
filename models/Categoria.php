<?php
declare(strict_types=1);

/**
 * Modelo Categoria (models/Categoria.php)
 * FLOCK — Agenda Cultural Municipal
 *
 * Encapsula la persistencia y consultas sobre la tabla 'categorias'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Categoria
{
    /**
     * Lista todas las categorías junto con el número total de eventos activos programados.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listarConTotalEventos(): array
    {
        $pdo = obtenerConexion();
        $sql = 'SELECT 
                    c.id, 
                    c.nombre, 
                    c.slug, 
                    c.color_badge, 
                    c.descripcion,
                    COUNT(CASE WHEN e.estado = "activo" THEN e.id ELSE NULL END) AS total_eventos
                FROM categorias c
                LEFT JOIN eventos e ON e.categoria_id = c.id
                GROUP BY c.id, c.nombre, c.slug, c.color_badge, c.descripcion
                ORDER BY c.nombre ASC';

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el listado simple de todas las categorías.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function todas(): array
    {
        $pdo = obtenerConexion();
        $stmt = $pdo->query('SELECT id, nombre, slug, color_badge, descripcion FROM categorias ORDER BY nombre ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una categoría por su ID numérico o por su slug alfanumérico.
     *
     * @param int|string $identificador
     * @return array<string, mixed>|null
     */
    public static function buscarPorIdOSlug(int|string $identificador): ?array
    {
        $pdo = obtenerConexion();
        if (is_int($identificador) || ctype_digit((string) $identificador)) {
            $stmt = $pdo->prepare('SELECT id, nombre, slug, color_badge, descripcion FROM categorias WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => (int) $identificador]);
        } else {
            $stmt = $pdo->prepare('SELECT id, nombre, slug, color_badge, descripcion FROM categorias WHERE slug = :slug LIMIT 1');
            $stmt->execute([':slug' => strtolower((string) $identificador)]);
        }

        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cat ?: null;
    }
}
