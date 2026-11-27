<?php
declare(strict_types=1);

/**
 * Modelo Categoria
 * Gestiona las temáticas y disciplinas artísticas de la tabla 'categorias'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Categoria
{
    /**
     * Lista todas las categorías y contabiliza sus eventos activos programados.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listarConTotalEventos(): array
    {
        $sql = 'SELECT 
                    c.id, c.nombre, c.slug, c.color_badge, c.descripcion,
                    COUNT(CASE WHEN e.estado = "activo" THEN e.id ELSE NULL END) AS total_eventos
                FROM categorias c
                LEFT JOIN eventos e ON e.categoria_id = c.id
                GROUP BY c.id, c.nombre, c.slug, c.color_badge, c.descripcion
                ORDER BY c.nombre ASC';

        return obtenerConexion()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista simple de todas las categorías para selectores y filtros.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function todas(): array
    {
        return obtenerConexion()
            ->query('SELECT id, nombre, slug, color_badge, descripcion FROM categorias ORDER BY nombre ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una categoría por su ID numérico o por su slug textual.
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
