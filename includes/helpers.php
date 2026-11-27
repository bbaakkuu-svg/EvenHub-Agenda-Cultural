<?php
declare(strict_types=1);

/**
 * Funciones auxiliares y de seguridad.
 * FLOCK — Agenda Cultural Municipal
 */

/**
 * Escapa cadenas para prevenir ataques XSS al imprimir en HTML.
 */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Formatea una fecha en formato MySQL (YYYY-MM-DD) al formato legible en español.
 * Opcionalmente concatena la hora (HH:MM).
 */
function formatearFecha(?string $fecha, ?string $hora = null): string
{
    if (empty($fecha)) {
        return 'Fecha por determinar';
    }

    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];

    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return (string) $fecha;
    }

    $dia = (int) date('j', $timestamp);
    $mes = (int) date('n', $timestamp);
    $ano = date('Y', $timestamp);

    $nombreMes = $meses[$mes] ?? date('F', $timestamp);
    $resultado = "{$dia} de {$nombreMes} de {$ano}";

    if (!empty($hora)) {
        $resultado .= ' — ' . substr($hora, 0, 5) . 'h';
    }

    return $resultado;
}

/**
 * Formatea el precio de un evento (0.00 => 'Gratuito', > 0 => '3,50 €').
 */
function formatearPrecio(mixed $precio): string
{
    if ($precio === null || $precio === '' || (float) $precio <= 0) {
        return 'Gratuito';
    }

    return number_format((float) $precio, 2, ',', '.') . ' €';
}

/**
 * Obtiene la ruta o URL pública de la imagen de un evento con fallback temático.
 */
function obtenerImagenEvento(?string $imagen, string $categoriaSlug = ''): string
{
    if (!empty($imagen)) {
        if (str_starts_with($imagen, 'http://') || str_starts_with($imagen, 'https://')) {
            return $imagen;
        }

        $rutaPublica = 'img/eventos/' . basename($imagen);
        if (file_exists(__DIR__ . '/../public/' . $rutaPublica)) {
            return $rutaPublica;
        }
    }

    $fallbacks = [
        'festivales'           => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=600&auto=format&fit=crop&q=80',
        'cineforum'            => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&auto=format&fit=crop&q=80',
        'musica-directo'       => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&auto=format&fit=crop&q=80',
        'arte-urbano'          => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?w=600&auto=format&fit=crop&q=80',
        'charlas-conferencias' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=600&auto=format&fit=crop&q=80',
    ];

    return $fallbacks[$categoriaSlug] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&auto=format&fit=crop&q=80';
}
/**
 * Emite cabecera HTTP 404 y muestra una pantalla amigable de no encontrado.
 */
function mostrar404(string $mensaje = 'El recurso solicitado no existe o no está disponible.'): void
{
    http_response_code(404);
    $pageTitle = 'Página no encontrada';

    require_once __DIR__ . '/../views/layout/header.php';
    ?>
    <div class="container my-5 text-center">
        <div class="display-1 text-muted mb-3 fw-bold">404</div>
        <h1 class="h3 fw-bold mb-2">Página o evento no encontrado</h1>
        <p class="text-muted mb-4"><?= e($mensaje) ?></p>
        <a href="eventos.php" class="btn btn-primary px-4">
            ← Volver a la agenda cultural
        </a>
    </div>
    <?php
    require_once __DIR__ . '/../views/layout/footer.php';
    exit;
}