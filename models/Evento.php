<?php
declare(strict_types=1);

/**
 * Modelo Evento (models/Evento.php)
 * FLOCK — Agenda Cultural Municipal
 *
 * Encapsula la persistencia y operaciones sobre la tabla 'eventos'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Evento
{
    /**
     * Cuenta el número total de eventos activos en la agenda municipal.
     */
    public static function contarActivos(): int
    {
        $pdo = obtenerConexion();
        return (int) $pdo->query("SELECT COUNT(*) FROM eventos WHERE estado = 'activo'")->fetchColumn();
    }

    /**
     * Obtiene los próximos N eventos programados con datos relacionados de categoría y espacio.
     *
     * @param int $limite
     * @return array<int, array<string, mixed>>
     */
    public static function proximos(int $limite = 5): array
    {
        $pdo = obtenerConexion();
        $sql = "SELECT e.id, e.titulo, e.fecha, e.hora, e.aforo_total, e.precio, e.estado,
                       c.nombre AS categoria_nombre, c.color_badge,
                       esp.nombre AS espacio_nombre,
                       COALESCE(SUM(i.plazas_reservadas), 0) AS plazas_ocupadas
                FROM eventos e
                JOIN categorias c ON e.categoria_id = c.id
                JOIN espacios_municipales esp ON e.espacio_id = esp.id
                LEFT JOIN inscripciones i ON e.id = i.evento_id AND i.estado = 'confirmada'
                GROUP BY e.id
                ORDER BY e.fecha ASC, e.hora ASC
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los eventos activos aplicando filtros opcionales de categoría y búsqueda textual.
     * Incluye cálculo dinámico de plazas ocupadas y plazas disponibles.
     *
     * @param int|string|null $categoria ID numérico o slug textual
     * @param string $q Término de búsqueda en título, descripción o recinto
     * @return array<int, array<string, mixed>>
     */
    public static function obtenerConFiltros(int|string|null $categoria = null, string $q = ''): array
    {
        $pdo = obtenerConexion();

        $sql = 'SELECT 
                    e.id, 
                    e.titulo, 
                    e.descripcion, 
                    e.fecha, 
                    e.hora, 
                    e.precio, 
                    e.aforo_total, 
                    e.aforo_total AS aforo,
                    e.imagen_url, 
                    e.imagen_url AS imagen, 
                    e.estado,
                    c.id AS categoria_id, 
                    c.nombre AS categoria_nombre, 
                    c.nombre AS categoria, 
                    c.slug AS categoria_slug, 
                    c.color_badge,
                    esp.id AS espacio_id, 
                    esp.nombre AS espacio_nombre, 
                    esp.nombre AS lugar, 
                    esp.direccion,
                    COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0) AS plazas_ocupadas,
                    (e.aforo_total - COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0)) AS plazas_disponibles
                FROM eventos e
                INNER JOIN categorias c ON c.id = e.categoria_id
                INNER JOIN espacios_municipales esp ON esp.id = e.espacio_id
                LEFT JOIN inscripciones i ON i.evento_id = e.id
                WHERE e.estado = "activo"';

        $params = [];

        if ($categoria !== null) {
            if (is_int($categoria)) {
                $sql .= ' AND e.categoria_id = :categoria';
                $params[':categoria'] = $categoria;
            } else {
                $sql .= ' AND c.slug = :categoria_slug';
                $params[':categoria_slug'] = $categoria;
            }
        }

        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $sql .= ' AND (e.titulo LIKE :q1 OR e.descripcion LIKE :q2 OR esp.nombre LIKE :q3 OR c.nombre LIKE :q4)';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
            $params[':q4'] = $like;
        }

        $sql .= ' GROUP BY e.id, c.id, esp.id ORDER BY e.fecha ASC, e.hora ASC, e.id ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la ficha completa de un evento por su ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public static function obtenerPorId(int $id): ?array
    {
        $pdo = obtenerConexion();
        $sql = 'SELECT 
                    e.id, 
                    e.titulo, 
                    e.descripcion, 
                    e.fecha, 
                    e.hora, 
                    e.precio, 
                    e.aforo_total, 
                    e.aforo_total AS aforo,
                    e.imagen_url, 
                    e.imagen_url AS imagen, 
                    e.estado,
                    c.id AS categoria_id, 
                    c.nombre AS categoria_nombre, 
                    c.nombre AS categoria, 
                    c.slug AS categoria_slug, 
                    c.color_badge,
                    esp.id AS espacio_id, 
                    esp.nombre AS espacio_nombre, 
                    esp.nombre AS lugar, 
                    esp.direccion,
                    COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0) AS plazas_ocupadas,
                    (e.aforo_total - COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0)) AS plazas_disponibles
                FROM eventos e
                INNER JOIN categorias c ON c.id = e.categoria_id
                INNER JOIN espacios_municipales esp ON esp.id = e.espacio_id
                LEFT JOIN inscripciones i ON i.evento_id = e.id
                WHERE e.id = :id
                GROUP BY e.id, c.id, esp.id
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $evento ?: null;
    }
}
