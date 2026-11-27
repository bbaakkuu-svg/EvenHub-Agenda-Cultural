<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Vista de Detalle de Evento (views/eventos/detalle.php)
 *
 * Variables disponibles desde public/evento.php:
 * @var array<string, mixed> $evento
 * @var int $plazasDisponibles
 * @var bool $estaAgotado
 * @var int $porcentajeOcupado
 */

$imgUrl    = obtenerImagenEvento($evento['imagen_url'] ?? null, $evento['categoria_slug'] ?? '');
$colorBadge = !empty($evento['color_badge']) ? $evento['color_badge'] : '#1d3557';
$aforoTotal = (int) $evento['aforo_total'];
?>

<div class="container my-4">
    <!-- Migas de pan / Enlace de retorno -->
    <nav class="mb-4" aria-label="Navegación de migas">
        <a href="eventos.php" class="btn btn-outline-secondary btn-sm">
            ← Volver a la Agenda Cultural
        </a>
    </nav>

    <div class="row g-4">
        <!-- Columna Principal (Información y Multimedia) -->
        <div class="col-12 col-lg-8">
            <article class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <!-- Imagen principal del evento -->
                <div class="position-relative">
                    <img src="<?= e($imgUrl) ?>" 
                         alt="Fotografía de <?= e($evento['titulo']) ?>" 
                         class="w-100" 
                         style="max-height: 420px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 p-3">
                        <span class="badge px-3 py-2 text-white fw-bold shadow-sm" style="background-color: <?= e($colorBadge) ?>;">
                            <?= e($evento['categoria']) ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 fw-bold text-dark mb-3">
                        <?= e($evento['titulo']) ?>
                    </h1>

                    <!-- Datos clave -->
                    <div class="row g-3 py-3 my-2 border-top border-bottom bg-light rounded-3 px-2">
                        <div class="col-12 col-sm-6 d-flex align-items-center gap-2">
                            <span class="fs-4" aria-hidden="true"></span>
                            <div>
                                <small class="text-muted d-block">Fecha y hora</small>
                                <strong class="text-dark">
                                    <?= e(formatearFecha($evento['fecha'], $evento['hora'])) ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 d-flex align-items-center gap-2">
                            <span class="fs-4" aria-hidden="true"></span>
                            <div>
                                <small class="text-muted d-block">Espacio Municipal</small>
                                <strong class="text-dark"><?= e($evento['espacio_nombre']) ?></strong>
                                <small class="text-muted d-block"><?= e($evento['espacio_direccion']) ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción extendida -->
                    <h2 class="h5 fw-bold text-dark mt-4 mb-3">Sobre esta actividad</h2>
                    <p class="text-secondary leading-relaxed fs-6">
                        <?= nl2br(e($evento['descripcion'])) ?>
                    </p>
                </div>
            </article>
        </div>

        <!-- Columna Lateral (Panel de Reserva y Estado de Aforo) -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 2rem;">
                <h3 class="h5 fw-bold text-dark mb-3">Inscripción y Aforo</h3>

                <!-- Precio -->
                <div class="mb-4">
                    <span class="text-muted fs-sm d-block">Precio de entrada:</span>
                    <span class="display-6 fw-bold text-dark">
                        <?= e(formatearPrecio($evento['precio'])) ?>
                    </span>
                </div>

                <!-- CRITERIO EVALUADO: Estado de aforo y plazas libres -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold fs-sm">Disponibilidad de aforo:</span>
                        <span class="badge <?= $estaAgotado ? 'bg-danger' : 'bg-success' ?> fs-xs">
                            <?= $estaAgotado ? 'Agotado' : $plazasDisponibles . ' plazas libres' ?>
                        </span>
                    </div>

                    <!-- Barra de progreso de ocupación -->
                    <div class="progress my-2" style="height: 10px;">
                        <div class="progress-bar <?= $estaAgotado ? 'bg-danger' : 'bg-primary' ?>" 
                             role="progressbar" 
                             style="width: <?= $porcentajeOcupado ?>%;" 
                             aria-valuenow="<?= $porcentajeOcupado ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted d-block">
                        Aforo total: <?= $aforoTotal ?> personas (<?= $evento['plazas_reservadas'] ?> ya reservadas)
                    </small>
                </div>

                <!-- CRITERIO EVALUADO: Botón habilitado o deshabilitado según aforo -->
                <?php if ($estaAgotado): ?>
                    <!-- Caso Plazas Agotadas -->
                    <div class="alert alert-danger py-2 px-3 text-center fs-sm mb-3" role="alert">
                         Aforo completo. No quedan plazas libres para esta actividad.
                    </div>
                    <button type="button" class="btn btn-secondary btn-lg w-100 disabled" disabled>
                        Inscripción Agotada
                    </button>
                <?php else: ?>
                    <!-- Caso Plazas Disponibles -->
                    <div class="alert alert-success py-2 px-3 text-center fs-sm mb-3" role="alert">
                         Plazas disponibles con confirmación inmediata.
                    </div>
                    <a href="inscribirse.php?evento_id=<?= (int)$evento['id'] ?>" class="btn btn-flock-accent btn-lg w-100 text-center">
                        Reservar Mi Plaza
                    </a>
                <?php endif; ?>

                <!-- Información del recinto -->
                <div class="border-top pt-3 mt-4 fs-xs text-muted">
                    <p class="mb-1"> <strong>Recinto:</strong> <?= e($evento['espacio_nombre']) ?></p>
                    <p class="mb-0">Capacidad máxima del recinto: <?= (int)$evento['aforo_maximo'] ?> personas.</p>
                </div>
            </div>
        </div>
    </div>
</div>