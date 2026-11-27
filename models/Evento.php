<?php
declare(strict_types=1);

/**
 * Modelo Evento
 * Gestiona el ciclo de vida de los eventos culturales en la tabla 'eventos'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Evento
{
    /**
     * Cuenta los eventos actualmente en estado 'activo'.
     */
    public static function contarActivos(): int
    {
        return (int) obtenerConexion()
            ->query("SELECT COUNT(*) FROM eventos WHERE estado = 'activo'")
            ->fetchColumn();
    }

    /**
     * Obtiene los próximos eventos ordenados cronológicamente con su categoría y recinto.
     *
     * @param int $limite Número máximo de resultados
     * @return array<int, array<string, mixed>>
     */
    public static function proximos(int $limite = 5): array
    {
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

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta el catálogo de eventos activos aplicando filtros opcionales de categoría y búsqueda.
     *
     * @param int|string|null $categoria ID numérico o slug de la categoría
     * @param string $q Término de búsqueda en título, descripción o recinto
     * @return array<int, array<string, mixed>>
     */
    public static function obtenerConFiltros(int|string|null $categoria = null, string $q = ''): array
    {
        $sql = 'SELECT 
                    e.id, e.titulo, e.descripcion, e.fecha, e.hora, e.precio, 
                    e.aforo_total, e.aforo_total AS aforo,
                    e.imagen_url, e.imagen_url AS imagen, e.estado,
                    c.id AS categoria_id, c.nombre AS categoria_nombre, c.nombre AS categoria, 
                    c.slug AS categoria_slug, c.color_badge,
                    esp.id AS espacio_id, esp.nombre AS espacio_nombre, esp.nombre AS lugar, esp.direccion,
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

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle completo de un evento por su ID.
     * Consumido principalmente por la Issue #8 (Vista de Detalle y Aforo).
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public static function obtenerPorId(int $id): ?array
    {
        $sql = 'SELECT 
                    e.id, e.titulo, e.descripcion, e.fecha, e.hora, e.precio, 
                    e.aforo_total, e.aforo_total AS aforo,
                    e.imagen_url, e.imagen_url AS imagen, e.estado,
                    c.id AS categoria_id, c.nombre AS categoria_nombre, c.nombre AS categoria, 
                    c.slug AS categoria_slug, c.color_badge,
                    esp.id AS espacio_id, esp.nombre AS espacio_nombre, esp.nombre AS lugar, 
                    esp.direccion, esp.aforo_maximo AS espacio_aforo_maximo,
                    COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0) AS plazas_ocupadas,
                    (e.aforo_total - COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0)) AS plazas_disponibles
                FROM eventos e
                INNER JOIN categorias c ON c.id = e.categoria_id
                INNER JOIN espacios_municipales esp ON esp.id = e.espacio_id
                LEFT JOIN inscripciones i ON i.evento_id = e.id
                WHERE e.id = :id
                GROUP BY e.id, c.id, esp.id
                LIMIT 1';

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $evento ?: null;
    }

    /**
     * Inserta un nuevo evento en el catálogo.
     * Punto de extensión para la Issue #9 (Formulario de Creación CRUD).
     *
     * @param array<string, mixed> $datos Campos requeridos: categoria_id, espacio_id, titulo, descripcion, fecha, hora, aforo_total, precio, imagen_url
     * @return int ID del evento insertado
     */
    public static function crear(array $datos): int
    {
        $sql = 'INSERT INTO eventos (categoria_id, espacio_id, titulo, descripcion, fecha, hora, aforo_total, precio, imagen_url, estado)
                VALUES (:categoria_id, :espacio_id, :titulo, :descripcion, :fecha, :hora, :aforo_total, :precio, :imagen_url, :estado)';

        $pdo = obtenerConexion();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':categoria_id' => (int) $datos['categoria_id'],
            ':espacio_id'   => (int) $datos['espacio_id'],
            ':titulo'       => trim((string) $datos['titulo']),
            ':descripcion'  => trim((string) $datos['descripcion']),
            ':fecha'        => (string) $datos['fecha'],
            ':hora'         => (string) $datos['hora'],
            ':aforo_total'  => (int) $datos['aforo_total'],
            ':precio'       => (float) ($datos['precio'] ?? 0.0),
            ':imagen_url'   => !empty($datos['imagen_url']) ? trim((string) $datos['imagen_url']) : null,
            ':estado'       => $datos['estado'] ?? 'activo'
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Actualiza los datos de un evento existente.
     * Punto de extensión para la Issue #9 (Formulario de Edición CRUD).
     *
     * @param int $id
     * @param array<string, mixed> $datos
     * @return bool
     */
    public static function actualizar(int $id, array $datos): bool
    {
        $sql = 'UPDATE eventos 
                SET categoria_id = :categoria_id, espacio_id = :espacio_id, titulo = :titulo,
                    descripcion = :descripcion, fecha = :fecha, hora = :hora, aforo_total = :aforo_total,
                    precio = :precio, imagen_url = :imagen_url, estado = :estado
                WHERE id = :id';

        $stmt = obtenerConexion()->prepare($sql);
        return $stmt->execute([
            ':id'           => $id,
            ':categoria_id' => (int) $datos['categoria_id'],
            ':espacio_id'   => (int) $datos['espacio_id'],
            ':titulo'       => trim((string) $datos['titulo']),
            ':descripcion'  => trim((string) $datos['descripcion']),
            ':fecha'        => (string) $datos['fecha'],
            ':hora'         => (string) $datos['hora'],
            ':aforo_total'  => (int) $datos['aforo_total'],
            ':precio'       => (float) ($datos['precio'] ?? 0.0),
            ':imagen_url'   => !empty($datos['imagen_url']) ? trim((string) $datos['imagen_url']) : null,
            ':estado'       => $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Da de baja un evento o lo cancela según integridad referencial.
     * Punto de extensión para la Issue #10 (Baja de Eventos).
     *
     * @param int $id
     * @return bool
     */
    public static function eliminar(int $id): bool
    {
        $stmt = obtenerConexion()->prepare('DELETE FROM eventos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
