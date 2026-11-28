<?php
declare(strict_types=1);

/**
 * Vista de Confirmación de Inscripción (views/inscripcion/confirmacion.php)
 * FLOCK — Agenda Cultural Municipal | Issue #11
 *
 * Variables disponibles desde public/confirmacion.php:
 * @var array<string, mixed> $inscripcion
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center">

                <!-- Icono de éxito -->
                <div class="mb-3">
                    <span class="display-3" aria-hidden="true">✅</span>
                </div>

                <h1 class="h3 fw-bold text-success mb-1">¡Inscripción confirmada!</h1>
                <p class="text-muted mb-4">
                    Tu plaza en el evento ha sido reservada correctamente.
                </p>

                <!-- Tarjeta de localizador -->
                <div class="p-4 bg-light rounded-4 border mb-4 text-start">
                    <p class="text-muted fs-xs text-uppercase fw-semibold mb-1">Tu localizador de inscripción</p>
                    <p class="display-6 fw-bold text-flock-primary mb-0 font-monospace">
                        <?= e($inscripcion['localizador']) ?>
                    </p>
                    <p class="fs-xs text-muted mt-1 mb-0">
                        Guarda este código — te lo pedirán en la puerta del evento.
                    </p>
                </div>

                <!-- Detalles de la inscripción -->
                <div class="card border-0 bg-white rounded-3 shadow-sm mb-4 text-start">
                    <div class="card-header bg-transparent py-3">
                        <h2 class="h6 fw-bold text-dark mb-0">Detalles de tu reserva</h2>
                    </div>
                    <div class="card-body p-0">
                        <dl class="row g-0 fs-sm mb-0">
                            <dt class="col-5 p-3 text-muted fw-normal border-bottom bg-light">Evento</dt>
                            <dd class="col-7 p-3 fw-semibold border-bottom mb-0">
                                <?= e($inscripcion['evento_titulo']) ?>
                            </dd>

                            <dt class="col-5 p-3 text-muted fw-normal border-bottom bg-light">Fecha</dt>
                            <dd class="col-7 p-3 fw-semibold border-bottom mb-0">
                                <?= e(formatearFecha(
                                    (string) $inscripcion['evento_fecha'],
                                    (string) $inscripcion['evento_hora']
                                )) ?>
                            </dd>

                            <dt class="col-5 p-3 text-muted fw-normal border-bottom bg-light">Espacio</dt>
                            <dd class="col-7 p-3 fw-semibold border-bottom mb-0">
                                <?= e($inscripcion['espacio_nombre']) ?><br>
                                <small class="text-muted fw-normal"><?= e($inscripcion['espacio_direccion']) ?></small>
                            </dd>

                            <dt class="col-5 p-3 text-muted fw-normal border-bottom bg-light">Asistente</dt>
                            <dd class="col-7 p-3 fw-semibold border-bottom mb-0">
                                <?= e($inscripcion['nombre_asistente']) ?>
                            </dd>

                            <dt class="col-5 p-3 text-muted fw-normal border-bottom bg-light">Contacto</dt>
                            <dd class="col-7 p-3 fw-semibold border-bottom mb-0">
                                <?= e($inscripcion['email_asistente']) ?><br>
                                <small class="text-muted"><?= e($inscripcion['telefono_asistente']) ?></small>
                            </dd>

                            <dt class="col-5 p-3 text-muted fw-normal bg-light">Plazas</dt>
                            <dd class="col-7 p-3 fw-semibold mb-0">
                                <?= (int) $inscripcion['plazas_reservadas'] ?>
                                <?= (int) $inscripcion['plazas_reservadas'] === 1 ? 'plaza' : 'plazas' ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Observaciones (si las hay) -->
                <?php if (!empty($inscripcion['observaciones'])): ?>
                    <div class="p-3 bg-light rounded-3 border text-start fs-sm mb-4">
                        <strong class="d-block mb-1 text-dark">Observaciones registradas:</strong>
                        <p class="text-muted mb-0"><?= e($inscripcion['observaciones']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Aviso informativo -->
                <div class="alert alert-info d-flex align-items-start gap-2 text-start fs-sm mb-4" role="alert">
                    <span aria-hidden="true" class="mt-1">ℹ️</span>
                    <div>
                        Recuerda presentar tu <strong>localizador <?= e($inscripcion['localizador']) ?></strong>
                        en la entrada del evento. Puedes guardar esta página o anotar el código.
                    </div>
                </div>

                <!-- Acciones -->
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <a href="eventos.php" class="btn btn-flock-primary px-4">
                        Ver más eventos de la agenda
                    </a>
                    <a href="evento.php?id=<?= (int) $inscripcion['evento_id'] ?>" class="btn btn-outline-secondary px-4">
                        Volver al evento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
