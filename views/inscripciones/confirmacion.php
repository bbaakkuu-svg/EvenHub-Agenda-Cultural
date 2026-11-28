<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Vista de Confirmación de Reserva (views/inscripciones/confirmacion.php)
 *
 * Variables disponibles desde public/inscribirse.php:
 * @var array<string, mixed> $inscripcion
 */

$colorBadge = !empty($inscripcion['color_badge']) ? $inscripcion['color_badge'] : '#1d3557';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border rounded-3 overflow-hidden bg-white">
                <div class="bg-success text-white p-4 text-center">
                    <div class="display-6 mb-2" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></div>
                    <h1 class="h3 fw-bold mb-1">¡Inscripción Confirmada!</h1>
                    <p class="mb-0 opacity-75 fs-sm">
                        Tu reserva de plazas se ha registrado correctamente en la Agenda Cultural.
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Código de Referencia / Localizador -->
                    <div class="text-center p-3 mb-4 bg-light rounded-3 border">
                        <span class="text-muted fs-xs text-uppercase fw-semibold d-block mb-1">
                            Código de referencia (Localizador)
                        </span>
                        <span class="h2 fw-bold text-flock-primary font-monospace mb-0">
                            <?= e($inscripcion['localizador']) ?>
                        </span>
                    </div>

                    <!-- Datos del Evento -->
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3">Detalles del evento</h2>
                    <div class="p-3 rounded-3 border mb-4">
                        <?php if (!empty($inscripcion['categoria_nombre'])): ?>
                            <span class="badge text-white mb-2" style="background-color: <?= e($colorBadge) ?>;">
                                <?= e($inscripcion['categoria_nombre']) ?>
                            </span>
                        <?php endif; ?>
                        <h3 class="h5 fw-bold text-dark mb-2"><?= e($inscripcion['evento_titulo']) ?></h3>
                        <p class="mb-1 fs-sm text-secondary">
                            <strong>Fecha y hora:</strong>
                            <?= e(formatearFecha((string) $inscripcion['evento_fecha'], (string) $inscripcion['evento_hora'])) ?>
                        </p>
                        <p class="mb-1 fs-sm text-secondary">
                            <strong>Espacio:</strong>
                            <?= e($inscripcion['espacio_nombre']) ?> (<?= e($inscripcion['espacio_direccion']) ?>)
                        </p>
                        <?php if (isset($inscripcion['evento_precio'])): ?>
                            <p class="mb-0 fs-sm text-secondary">
                                <strong>Precio por plaza:</strong>
                                <?= e(formatearPrecio($inscripcion['evento_precio'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Datos del Asistente -->
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3">Datos de la reserva</h2>
                    <dl class="row fs-sm mb-4">
                        <dt class="col-sm-4 text-muted">Asistente</dt>
                        <dd class="col-sm-8 fw-semibold"><?= e($inscripcion['nombre_asistente']) ?></dd>

                        <dt class="col-sm-4 text-muted">Correo electrónico</dt>
                        <dd class="col-sm-8"><?= e($inscripcion['email_asistente']) ?></dd>

                        <dt class="col-sm-4 text-muted">Teléfono</dt>
                        <dd class="col-sm-8"><?= e($inscripcion['telefono_asistente']) ?></dd>

                        <dt class="col-sm-4 text-muted">Plazas reservadas</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-primary-subtle text-primary border">
                                <?= (int) $inscripcion['plazas_reservadas'] ?> <?= ((int) $inscripcion['plazas_reservadas'] === 1) ? 'plaza' : 'plazas' ?>
                            </span>
                        </dd>

                        <?php if (!empty($inscripcion['observaciones'])): ?>
                            <dt class="col-sm-4 text-muted">Observaciones</dt>
                            <dd class="col-sm-8"><?= e($inscripcion['observaciones']) ?></dd>
                        <?php endif; ?>
                    </dl>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="evento.php?id=<?= (int) $inscripcion['evento_id'] ?>" class="btn btn-outline-secondary btn-sm">
                                ← Volver al evento
                            </a>
                            <a href="eventos.php" class="btn btn-primary btn-sm">
                                Ver más actividades
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1" onclick="window.print()">
                            <i class="bi bi-printer" aria-hidden="true"></i> Imprimir comprobante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
