<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Controlador de Detalle de Evento (public/evento.php)
 */

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/helpers.php';

// 1. Validar que 'id' exista por GET y sea entero positivo
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if ($id === false || $id === null) {
    mostrar404('Identificador de evento no válido o no especificado.');
    exit;
}

// 2. Conectar a la base de datos y consultar el evento por su ID
try {
    $pdo = obtenerConexion();

    $sql = 'SELECT 
                e.id, e.titulo, e.descripcion, e.fecha, e.hora, e.precio, 
                e.aforo_total, e.imagen_url, e.estado,
                c.nombre AS categoria, c.slug AS categoria_slug, c.color_badge,
                em.nombre AS espacio_nombre, em.direccion AS espacio_direccion, em.aforo_maximo,
                COALESCE(SUM(CASE WHEN i.estado = "confirmada" THEN i.plazas_reservadas ELSE 0 END), 0) AS plazas_reservadas
            FROM eventos e
            INNER JOIN categorias c ON c.id = e.categoria_id
            INNER JOIN espacios_municipales em ON em.id = e.espacio_id
            LEFT JOIN inscripciones i ON i.evento_id = e.id
            WHERE e.id = :id AND e.estado != "cancelado"
            GROUP BY e.id, c.id, em.id
            LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $evento = $stmt->fetch();

} catch (Throwable $ex) {
    error_log('[FLOCK Detalle Evento] ' . $ex->getMessage());
    mostrar404('Error al consultar el evento. Inténtalo de nuevo más tarde.');
    exit;
}

// 3. Si no existe ningún evento con ese ID en la base de datos: error 404
if (!$evento) {
    mostrar404('El evento solicitado no existe o ha sido cancelado.');
    exit;
}

// 4. Calcular en servidor las plazas libres restantes y el estado de aforo
$aforoTotal        = (int) $evento['aforo_total'];
$plazasReservadas  = (int) $evento['plazas_reservadas'];
$plazasDisponibles = max(0, $aforoTotal - $plazasReservadas);
$estaAgotado       = ($plazasDisponibles === 0);

$porcentajeOcupado = ($aforoTotal > 0) 
    ? min(100, (int) round(($plazasReservadas / $aforoTotal) * 100)) 
    : 100;

// 5. Cargar la plantilla visual
$pageTitle = $evento['titulo'];
$activeNav = 'eventos';

require_once __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/eventos/detalle.php';
require_once __DIR__ . '/../views/layout/footer.php';