<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Vista de Categorías Culturales (views/categorias/lista.php)
 *
 * Variables recibidas desde el controlador public/categorias.php:
 * @var array<int, array<string, mixed>> $categorias
 * @var string|null $error
 */
?>

<div class="container my-4">
    <!-- Encabezado de la página -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Categorías Culturales</h1>
            <p class="text-muted mb-0">Explora las disciplinas artísticas y ejes temáticos de la programación municipal</p>
        </div>
        <div class="d-flex gap-2">
            <a href="eventos.php" class="btn btn-outline-primary btn-sm">
                Ver Todos los Eventos
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                ← Inicio
            </a>
        </div>
    </div>

    <!-- Mensaje de error general -->
    <?php if ($error !== null): ?>
        <div class="alert alert-danger shadow-sm my-4" role="alert">
            <h2 class="h5 alert-heading mb-1">Aviso del Sistema</h2>
            <p class="mb-0"><?= e($error) ?></p>
        </div>

    <?php elseif (empty($categorias)): ?>
        <div class="text-center py-5 bg-white rounded-3 border p-4 my-4">
            <div class="fs-1 text-secondary mb-3" aria-hidden="true"><i class="bi bi-folder2-open"></i></div>
            <h2 class="h5 fw-bold mb-2">No hay categorías disponibles</h2>
            <p class="text-muted mb-0">En este momento no se han configurado categorías culturales en el sistema.</p>
        </div>

    <?php else: ?>
        <!-- Rejilla Responsive de Tarjetas de Categoría -->
        <div class="row g-4">
            <?php foreach ($categorias as $cat): ?>
                <?php
                $colorBadge   = !empty($cat['color_badge']) ? (string) $cat['color_badge'] : '#1d3557';
                $totalEventos = (int) ($cat['total_eventos'] ?? 0);
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 overflow-hidden bg-white d-flex flex-column"
                        style="border-top: 4px solid <?= e($colorBadge) ?> !important;">
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-2" aria-hidden="true"></span>
                                <span class="badge rounded-pill px-3 py-2 text-white" style="background-color: <?= e($colorBadge) ?>;">
                                    <?= $totalEventos ?> <?= $totalEventos === 1 ? 'evento' : 'eventos' ?>
                                </span>
                            </div>

                            <h2 class="h5 fw-bold mb-2">
                                <?= e($cat['nombre']) ?>
                            </h2>

                            <p class="text-muted fs-sm mb-4 flex-grow-1">
                                <?= e($cat['descripcion'] ?? 'Actividades culturales y participativas en Villaverde de la Ribera.') ?>
                            </p>

                            <div class="mt-auto">
                                <a href="eventos.php?categoria=<?= (int) $cat['id'] ?>"
                                   class="btn btn-outline-dark w-100 fw-semibold d-flex justify-content-between align-items-center">
                                    <span>Ver actividades</span>
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Banner informativo inferior -->
        <div class="mt-5 p-4 rounded-3 bg-flock-primary text-white border d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="h5 fw-bold mb-1">¿Buscas algo en específico?</h3>
                <p class="mb-0 text-white-50 fs-sm">Consulta el listado completo de actividades o utiliza nuestro buscador por palabra clave.</p>
            </div>
            <a href="eventos.php" class="btn btn-flock-accent px-4 py-2">
                Ir a la Agenda Completa
            </a>
        </div>
    <?php endif; ?>
</div>
