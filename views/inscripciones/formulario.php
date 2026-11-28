<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Vista del Formulario de Inscripción (views/inscripciones/formulario.php)
 *
 * Variables disponibles desde public/inscribirse.php:
 * @var array<string, mixed> $evento
 * @var int $plazasDisponibles
 * @var bool $estaAgotado
 * @var array<int, string> $errores
 * @var array{nombre: string, email: string, telefono: string, plazas: int, observaciones: string} $formData
 * @var string $csrfToken
 */

$maxPlazasSeleccionables = min(5, max(1, $plazasDisponibles));
$colorBadge = !empty($evento['color_badge']) ? $evento['color_badge'] : '#1d3557';
?>

<div class="container my-4">
    <nav class="mb-4" aria-label="Navegación de retorno">
        <a href="evento.php?id=<?= (int) $evento['id'] ?>" class="btn btn-outline-secondary btn-sm">
            ← Volver al detalle del evento
        </a>
    </nav>

    <div class="row g-4">
        <!-- Formulario de Inscripción -->
        <div class="col-12 col-lg-8">
            <div class="card border rounded-3 bg-white">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <span class="badge text-white fw-semibold mb-2" style="background-color: <?= e($colorBadge) ?>;">
                            <?= e($evento['categoria']) ?>
                        </span>
                        <h1 class="h3 fw-bold text-flock-primary mb-1">Reserva de Plazas</h1>
                        <p class="text-muted mb-0">
                            Actividad: <strong><?= e($evento['titulo']) ?></strong>
                        </p>
                    </div>

                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger fs-sm" role="alert">
                            <div class="fw-bold mb-1">No se pudo procesar la inscripción:</div>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errores as $err): ?>
                                    <li><?= e($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($estaAgotado): ?>
                        <div class="alert alert-warning py-3" role="alert">
                            <h2 class="h6 fw-bold mb-1">Aforo completo</h2>
                            <p class="mb-0 fs-sm">
                                Lo sentimos, todas las plazas para este evento ya han sido reservadas.
                            </p>
                        </div>
                        <a href="eventos.php" class="btn btn-primary mt-2">Explorar otros eventos</a>
                    <?php else: ?>
                        <form method="POST" action="inscribirse.php?evento_id=<?= (int) $evento['id'] ?>" class="needs-validation" data-validate="inscripcion" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="nombre" class="form-label fw-semibold fs-sm">Nombre y apellidos <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nombre"
                                        name="nombre"
                                        value="<?= e($formData['nombre']) ?>"
                                        placeholder="Ej. María López García"
                                        minlength="2"
                                        maxlength="100"
                                        required>
                                </div>

                                <div class="col-12 col-md-7">
                                    <label for="email" class="form-label fw-semibold fs-sm">Correo electrónico <span class="text-danger">*</span></label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?= e($formData['email']) ?>"
                                        placeholder="nombre@correo.com"
                                        maxlength="120"
                                        required>
                                    <div class="form-text fs-xs">Solo se permite una reserva activa por correo y evento.</div>
                                </div>

                                <div class="col-12 col-md-5">
                                    <label for="telefono" class="form-label fw-semibold fs-sm">Teléfono de contacto <span class="text-danger">*</span></label>
                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="telefono"
                                        name="telefono"
                                        value="<?= e($formData['telefono']) ?>"
                                        placeholder="600 123 456"
                                        maxlength="20"
                                        required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="plazas" class="form-label fw-semibold fs-sm">Número de plazas (1 a <?= $maxPlazasSeleccionables ?>) <span class="text-danger">*</span></label>
                                    <select class="form-select" id="plazas" name="plazas" data-max-plazas="<?= $maxPlazasSeleccionables ?>" required>
                                        <?php for ($n = 1; $n <= $maxPlazasSeleccionables; $n++): ?>
                                            <option value="<?= $n ?>" <?= ((int) $formData['plazas'] === $n) ? 'selected' : '' ?>>
                                                <?= $n ?> <?= ($n === 1) ? 'plaza' : 'plazas' ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="observaciones" class="form-label fw-semibold fs-sm">Observaciones o necesidades de accesibilidad</label>
                                    <textarea
                                        class="form-control"
                                        id="observaciones"
                                        name="observaciones"
                                        rows="3"
                                        maxlength="500"
                                        placeholder="Indica si necesitas plaza PMR, acceso con carrito infantil u otra información relevante (opcional)."><?= e($formData['observaciones']) ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                                <a href="evento.php?id=<?= (int) $evento['id'] ?>" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-flock-accent btn-lg px-4">
                                    Confirmar Inscripción
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resumen Lateral del Evento -->
        <div class="col-12 col-lg-4">
            <aside class="card border rounded-3 p-4 bg-white">
                <h2 class="h5 fw-bold text-dark mb-3">Resumen de la actividad</h2>

                <dl class="mb-0 fs-sm">
                    <dt class="text-muted fw-normal">Fecha y hora</dt>
                    <dd class="fw-semibold text-dark mb-3">
                        <?= e(formatearFecha((string) $evento['fecha'], (string) $evento['hora'])) ?>
                    </dd>

                    <dt class="text-muted fw-normal">Espacio municipal</dt>
                    <dd class="fw-semibold text-dark mb-1"><?= e($evento['espacio_nombre']) ?></dd>
                    <dd class="text-muted fs-xs mb-3"><?= e($evento['direccion'] ?? '') ?></dd>

                    <dt class="text-muted fw-normal">Precio por plaza</dt>
                    <dd class="fw-bold text-dark fs-5 mb-3"><?= e(formatearPrecio($evento['precio'])) ?></dd>

                    <dt class="text-muted fw-normal">Disponibilidad actual</dt>
                    <dd class="mb-0">
                        <span class="badge <?= $estaAgotado ? 'bg-danger' : 'bg-success' ?>">
                            <?= $estaAgotado ? 'Agotado' : $plazasDisponibles . ' plazas disponibles' ?>
                        </span>
                    </dd>
                </dl>
            </aside>
        </div>
    </div>
</div>
