<?php
declare(strict_types=1);

/**
 * Vista del Formulario de Inscripción (views/inscripcion/formulario.php)
 * FLOCK — Agenda Cultural Municipal | Issue #11
 *
 * Variables disponibles desde public/inscribirse.php:
 * @var array<string, mixed> $evento
 * @var int $plazasDisponibles
 * @var string|null $error
 * @var array{nombre: string, email: string, telefono: string, plazas: int, observaciones: string} $campos
 * @var string $colorBadge
 */

$aforoTotal     = (int) $evento['aforo_total'];
$plazasOcupadas = (int) ($evento['plazas_ocupadas'] ?? 0);
$porcentaje     = $aforoTotal > 0 ? min(100, (int) round(($plazasOcupadas / $aforoTotal) * 100)) : 100;
?>

<div class="container my-4">
    <!-- Miga de pan / retorno -->
    <nav class="mb-4" aria-label="Navegación de migas">
        <a href="evento.php?id=<?= (int) $evento['id'] ?>" class="btn btn-outline-secondary btn-sm">
            ← Volver al detalle del evento
        </a>
    </nav>

    <div class="row g-4">
        <!-- Columna principal: Formulario -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <!-- Cabecera -->
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="p-3 rounded-3 text-white fs-4" style="background-color: <?= e($colorBadge) ?>;">
                        🎟️
                    </div>
                    <div>
                        <h1 class="h4 fw-bold mb-0 text-dark">Reservar plaza</h1>
                        <p class="text-muted fs-sm mb-0"><?= e($evento['titulo']) ?></p>
                    </div>
                </div>

                <!-- Error de validación -->
                <?php if ($error !== null): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 fs-sm mb-4" role="alert">
                        <span aria-hidden="true">⚠️</span>
                        <div><?= e($error) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Formulario de inscripción -->
                <form method="POST"
                      action="inscribirse.php?evento_id=<?= (int) $evento['id'] ?>"
                      class="needs-validation"
                      novalidate
                      id="form-inscripcion">

                    <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold fs-sm">
                            Nombre completo <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nombre"
                               name="nombre"
                               value="<?= e($campos['nombre']) ?>"
                               placeholder="Ej. María García López"
                               minlength="3"
                               maxlength="100"
                               required
                               autocomplete="name">
                        <div class="invalid-feedback">Introduce tu nombre completo (mín. 3 caracteres).</div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold fs-sm">
                            Correo electrónico <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?= e($campos['email']) ?>"
                               placeholder="ejemplo@correo.es"
                               required
                               autocomplete="email">
                        <div class="invalid-feedback">Introduce un correo electrónico válido.</div>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-semibold fs-sm">
                            Teléfono de contacto <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <input type="tel"
                               class="form-control"
                               id="telefono"
                               name="telefono"
                               value="<?= e($campos['telefono']) ?>"
                               placeholder="Ej. 611 223 344"
                               pattern="^\+?[\d\s\-]{7,20}$"
                               required
                               autocomplete="tel">
                        <div class="invalid-feedback">Introduce un teléfono válido (7-20 dígitos).</div>
                    </div>

                    <!-- Número de plazas -->
                    <div class="mb-3">
                        <label for="plazas" class="form-label fw-semibold fs-sm">
                            Número de plazas <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <select class="form-select" id="plazas" name="plazas" required>
                            <?php
                            $maxSel = min(5, $plazasDisponibles);
                            for ($i = 1; $i <= $maxSel; $i++):
                            ?>
                                <option value="<?= $i ?>" <?= $campos['plazas'] === $i ? 'selected' : '' ?>>
                                    <?= $i ?> <?= $i === 1 ? 'plaza' : 'plazas' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="form-text text-muted">
                            Máximo 5 plazas por inscripción.
                            <strong><?= $plazasDisponibles ?></strong> disponibles en este momento.
                        </div>
                        <div class="invalid-feedback">Selecciona el número de plazas.</div>
                    </div>

                    <!-- Observaciones (opcional) -->
                    <div class="mb-4">
                        <label for="observaciones" class="form-label fw-semibold fs-sm">
                            Observaciones <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <textarea class="form-control"
                                  id="observaciones"
                                  name="observaciones"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Necesidades de accesibilidad, preguntas u observaciones…"><?= e($campos['observaciones']) ?></textarea>
                        <div class="form-text text-muted">Máx. 500 caracteres.</div>
                    </div>

                    <!-- Aviso de privacidad -->
                    <div class="p-3 bg-light rounded-3 border fs-xs text-muted mb-4">
                        🔒 Tus datos se utilizarán exclusivamente para gestionar tu inscripción al evento cultural,
                        en cumplimiento del RGPD y la normativa municipal de protección de datos.
                    </div>

                    <!-- Botón de envío -->
                    <?php if ($plazasDisponibles > 0): ?>
                        <button type="submit" class="btn btn-flock-primary btn-lg w-100 fw-semibold shadow-sm">
                            Confirmar mi reserva
                        </button>
                    <?php else: ?>
                        <div class="alert alert-danger text-center fw-semibold mb-0" role="alert">
                            ⚠️ Aforo completo — no quedan plazas disponibles para este evento.
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Columna lateral: Resumen del evento -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 2rem;">
                <h2 class="h5 fw-bold text-dark mb-3">Resumen del evento</h2>

                <div class="mb-3">
                    <span class="badge px-3 py-2 text-white mb-2" style="background-color: <?= e($colorBadge) ?>;">
                        <?= e($evento['categoria'] ?? $evento['categoria_nombre'] ?? '') ?>
                    </span>
                    <h3 class="h6 fw-bold text-dark mb-1"><?= e($evento['titulo']) ?></h3>
                    <p class="text-muted fs-sm mb-0"><?= e($evento['espacio_nombre'] ?? '') ?></p>
                    <p class="text-muted fs-xs mb-0"><?= e($evento['espacio_direccion'] ?? '') ?></p>
                </div>

                <dl class="row g-2 fs-sm mb-3">
                    <dt class="col-5 text-muted fw-normal">Fecha:</dt>
                    <dd class="col-7 fw-semibold mb-0"><?= e(formatearFecha((string) $evento['fecha'], (string) $evento['hora'])) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Precio:</dt>
                    <dd class="col-7 fw-semibold mb-0"><?= e(formatearPrecio($evento['precio'])) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Aforo:</dt>
                    <dd class="col-7 fw-semibold mb-0"><?= $aforoTotal ?> personas</dd>
                </dl>

                <!-- Barra de aforo -->
                <div class="mb-1 d-flex justify-content-between fs-xs text-muted">
                    <span>Ocupación actual</span>
                    <span><?= $porcentaje ?>%</span>
                </div>
                <div class="progress mb-2" style="height: 8px;" role="progressbar"
                     aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar <?= $porcentaje >= 90 ? 'bg-danger' : ($porcentaje >= 60 ? 'bg-warning' : 'bg-success') ?>"
                         style="width: <?= $porcentaje ?>%">
                    </div>
                </div>
                <p class="fs-xs text-muted">
                    <?= $plazasDisponibles ?> plazas libres de <?= $aforoTotal ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Validación HTML5 nativa con Bootstrap (DWC: validaciones en cliente) -->
<script>
(function () {
    'use strict';
    var form = document.getElementById('form-inscripcion');
    if (!form) return;
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();
</script>
