<?php
declare(strict_types=1);

/**
 * FLOCK — Agenda Cultural Municipal
 * Vista del catálogo de eventos (views/eventos/lista.php)
 *
 * Variables recibidas desde el controlador eventos.php:
 * @var array<int, array<string, mixed>> $eventos
 * @var array<int, array<string, mixed>> $categorias
 * @var int|string|null $categoriaSel
 * @var array<string, mixed>|null $categoriaActiva
 * @var string $q
 * @var string[] $avisos
 * @var bool $hayFiltros
 * @var string|null $error
 */
?>

<div class="container my-4">
    <!-- Encabezado de sección -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Agenda Cultural</h1>
            <p class="text-muted mb-0">Explora la programación oficial del municipio e inscríbete para reservar plaza</p>
        </div>
        <div class="d-flex gap-2">
            <a href="categorias.php" class="btn btn-outline-primary btn-sm">
                📂 Ver Categorías
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                ← Inicio
            </a>
        </div>
    </div>

    <!-- Indicador de categoría activa (si se filtra por una categoría) -->
    <?php if ($categoriaActiva !== null): ?>
        <div class="p-3 mb-4 rounded-3 bg-white border shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2"
             style="border-left: 5px solid <?= e($categoriaActiva['color_badge'] ?? '#1d3557') ?> !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-sm">Filtrando por disciplina:</span>
                <span class="badge px-3 py-2 text-white fw-bold" style="background-color: <?= e($categoriaActiva['color_badge'] ?? '#1d3557') ?>;">
                    <?= e($categoriaActiva['nombre']) ?>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="categorias.php" class="btn btn-sm btn-outline-secondary">
                    Cambiar categoría
                </a>
                <a href="eventos.php" class="btn btn-sm btn-outline-danger">
                    ✕ Quitar filtro
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Avisos de validación no bloqueantes -->
    <?php if (!empty($avisos)): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
            <ul class="mb-0 ps-3">
                <?php foreach ($avisos as $aviso): ?>
                    <li><?= e($aviso) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario de Búsqueda GET Tradicional -->
    <div class="card p-3 p-md-4 mb-4 shadow-sm border-0 bg-white rounded-3">
        <form method="get" action="eventos.php" class="row g-3 align-items-end">
            <?php if ($categoriaSel !== null): ?>
                <input type="hidden" name="categoria" value="<?= e((string) $categoriaSel) ?>">
            <?php endif; ?>

            <div class="col-12 col-md-7">
                <label for="q" class="form-label fw-semibold fs-sm mb-1">Buscar por palabra clave</label>
                <input type="search" id="q" name="q" class="form-control"
                       placeholder="Buscar por título, temática, descripción o recinto..."
                       maxlength="100" value="<?= e($q) ?>">
            </div>

            <div class="col-12 col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-flock-accent flex-fill">
                    Buscar
                </button>
                <a href="categorias.php" class="btn btn-outline-primary" title="Explorar disciplinas en una página dedicada">
                    📂 Categorías
                </a>
                <?php if ($hayFiltros): ?>
                    <a href="eventos.php" class="btn btn-outline-secondary" title="Limpiar todos los filtros">
                        Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Mensaje de error general -->
    <?php if ($error !== null): ?>
        <div class="alert alert-danger shadow-sm my-4" role="alert">
            <h2 class="h5 alert-heading mb-1">Aviso del Sistema</h2>
            <p class="mb-0"><?= e($error) ?></p>
        </div>

    <?php elseif (empty($eventos)): ?>
        <!-- Estado vacío: Sin resultados -->
        <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm my-4">
            <div class="display-6 mb-3">🔍</div>
            <h2 class="h5 fw-bold mb-2">
                <?= $hayFiltros
                    ? 'No hemos encontrado eventos con esos criterios de búsqueda.'
                    : 'Ahora mismo no hay eventos programados en esta sección.' ?>
            </h2>
            <p class="text-muted mb-4">
                <?= $hayFiltros
                    ? 'Prueba con otra palabra clave o explora otra categoría cultural.'
                    : 'Añadimos nuevas propuestas con frecuencia. Vuelve a consultar la agenda pronto.' ?>
            </p>
            <div class="d-flex justify-content-center gap-2">
                <?php if ($hayFiltros): ?>
                    <a href="eventos.php" class="btn btn-outline-primary">
                        Ver todos los eventos
                    </a>
                <?php endif; ?>
                <a href="categorias.php" class="btn btn-outline-secondary">
                    Explorar categorías
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- Contador de resultados -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted small mb-0">
                Se <?= count($eventos) === 1 ? 'ha encontrado' : 'han encontrado' ?>
                <strong><?= count($eventos) ?></strong>
                <?= count($eventos) === 1 ? 'evento cultural' : 'eventos culturales' ?>
                <?= $hayFiltros ? 'para tu búsqueda' : 'en la programación activa' ?>.
            </p>
        </div>

        <!-- Rejilla Responsive de Tarjetas FLOCK (Bootstrap: col-12 col-md-6 col-lg-4) -->
        <div class="row g-4">
            <?php foreach ($eventos as $ev): ?>
                <?php
                $imgUrl       = obtenerImagenEvento($ev['imagen_url'] ?? $ev['imagen'] ?? null, $ev['categoria_slug'] ?? '');
                $colorBadge   = !empty($ev['color_badge']) ? $ev['color_badge'] : '#1d3557';
                $aforoTotal   = (int) ($ev['aforo_total'] ?? $ev['aforo'] ?? 0);
                $plazasLibres = isset($ev['plazas_disponibles']) ? (int) $ev['plazas_disponibles'] : $aforoTotal;
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="event-card h-100">
                        <div class="event-card-img-wrapper">
                            <img src="<?= e($imgUrl) ?>"
                                 alt="Imagen del evento <?= e($ev['titulo']) ?>"
                                 class="event-card-img"
                                 loading="lazy">
                            <div class="position-absolute top-0 start-0 p-3">
                                <a href="eventos.php?categoria=<?= (int) ($ev['categoria_id'] ?? 0) ?>" class="text-decoration-none">
                                    <span class="badge-category" style="background-color: <?= e($colorBadge) ?>;">
                                        <?= e($ev['categoria']) ?>
                                    </span>
                                </a>
                            </div>
                            <div class="position-absolute bottom-0 end-0 p-3">
                                <span class="badge-price"><?= e(formatearPrecio($ev['precio'])) ?></span>
                            </div>
                        </div>

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h2 class="h5 fw-bold mb-2"><?= e($ev['titulo']) ?></h2>
                            <p class="text-muted fs-sm mb-3 flex-grow-1">
                                <?= e($ev['descripcion'] ?? '') ?>
                            </p>

                            <div class="fs-xs text-secondary mb-3 d-flex flex-column gap-1">
                                <span>📅 <strong><?= e(formatearFecha($ev['fecha'], $ev['hora'] ?? null)) ?></strong></span>
                                <span>📍 <?= e($ev['lugar']) ?></span>
                                <span>👥 <strong><?= $plazasLibres ?> plazas disponibles</strong> (de <?= $aforoTotal ?>)</span>
                            </div>

                            <a href="evento.php?id=<?= (int) $ev['id'] ?>" class="btn btn-flock-accent w-100 text-center">
                                Detalles e Inscripción
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
