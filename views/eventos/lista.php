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
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Agenda Cultural</h1>
            <p class="text-muted mb-0">Explora la programación oficial del municipio e inscríbete para reservar plaza</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                ← Volver al Inicio
            </a>
        </div>
    </div>

    <!-- Barra de Navegación de Categorías (Category Navbar) -->
    <nav class="category-navbar navbar navbar-expand-lg bg-white shadow-sm rounded-3 p-2 mb-4 border" aria-label="Navegación por categorías culturales">
        <div class="container-fluid px-1">
            <span class="navbar-text fw-bold text-uppercase fs-xs text-muted me-3 d-none d-lg-inline">
                Disciplinas:
            </span>
            <button class="navbar-toggler w-100 mb-2 d-lg-none border-0 py-1" type="button" data-bs-toggle="collapse" data-bs-target="#categoryNavItems" aria-controls="categoryNavItems" aria-expanded="false" aria-label="Alternar categorías">
                <span class="d-flex justify-content-between align-items-center fs-sm text-dark fw-semibold">
                    <span><?= $categoriaActiva !== null ? 'Categoría: ' . e($categoriaActiva['nombre']) : 'Filtrar por Disciplina' ?></span>
                    <span class="navbar-toggler-icon"></span>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="categoryNavItems">
                <ul class="nav nav-pills flex-wrap gap-1 align-items-center w-100">
                    <li class="nav-item">
                        <a class="nav-link py-1 px-3 fs-sm rounded-pill <?= ($categoriaSel === null) ? 'active bg-flock-primary text-white fw-semibold' : 'text-dark bg-light' ?>"
                           href="eventos.php<?= ($q !== '') ? '?q=' . urlencode($q) : '' ?>">
                            Todas
                        </a>
                    </li>
                    <?php foreach ($categorias as $cat): ?>
                        <?php
                        $isCatSelected = ($categoriaSel !== null && (
                            (string) $cat['id'] === (string) $categoriaSel ||
                            (isset($cat['slug']) && $cat['slug'] === (string) $categoriaSel)
                        ));
                        $catUrl = 'eventos.php?categoria=' . urlencode((string) $cat['id']);
                        if ($q !== '') {
                            $catUrl .= '&q=' . urlencode($q);
                        }
                        $badgeBg = !empty($cat['color_badge']) ? $cat['color_badge'] : '#1d3557';
                        ?>
                        <li class="nav-item">
                            <a class="nav-link py-1 px-3 fs-sm rounded-pill <?= $isCatSelected ? 'active text-white fw-bold shadow-sm' : 'text-dark bg-light' ?>"
                               style="<?= $isCatSelected ? 'background-color: ' . e($badgeBg) . ' !important;' : '' ?>"
                               href="<?= e($catUrl) ?>">
                                <?= e($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav-item ms-lg-auto">
                        <a class="nav-link py-1 px-3 fs-sm text-primary fw-semibold rounded-pill" href="categorias.php">
                            📂 Fichas de Categorías →
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Indicador de categoría activa (si se filtra por una categoría) -->
    <?php if ($categoriaActiva !== null): ?>
        <div class="p-3 mb-4 rounded-3 bg-white border shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2"
             style="border-left: 5px solid <?= e($categoriaActiva['color_badge'] ?? '#1d3557') ?> !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-sm">Mostrando eventos de:</span>
                <span class="badge px-3 py-2 text-white fw-bold fs-sm" style="background-color: <?= e($categoriaActiva['color_badge'] ?? '#1d3557') ?>;">
                    <?= e($categoriaActiva['nombre']) ?>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="categorias.php" class="btn btn-sm btn-outline-secondary">
                    Ver ficha descriptiva
                </a>
                <a href="eventos.php<?= ($q !== '') ? '?q=' . urlencode($q) : '' ?>" class="btn btn-sm btn-outline-danger">
                    ✕ Ver todas las categorías
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

    <!-- Formulario de Búsqueda GET Tradicional por palabra clave -->
    <div class="card p-3 mb-4 shadow-sm border-0 bg-white rounded-3">
        <form method="get" action="eventos.php" class="row g-2 align-items-center">
            <?php if ($categoriaSel !== null): ?>
                <input type="hidden" name="categoria" value="<?= e((string) $categoriaSel) ?>">
            <?php endif; ?>

            <div class="col-12 col-md-9">
                <label for="q" class="visually-hidden">Buscar</label>
                <input type="search" id="q" name="q" class="form-control"
                       placeholder="Buscar por título, temática, descripción o recinto municipal..."
                       maxlength="100" value="<?= e($q) ?>">
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-flock-accent flex-fill">
                    Buscar
                </button>
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
                    ? 'Prueba con otra palabra clave o selecciona otra disciplina cultural en la barra superior.'
                    : 'Añadimos nuevas propuestas con frecuencia. Vuelve a consultar la agenda pronto.' ?>
            </p>
            <div class="d-flex justify-content-center gap-2">
                <?php if ($hayFiltros): ?>
                    <a href="eventos.php" class="btn btn-outline-primary">
                        Ver todos los eventos
                    </a>
                <?php endif; ?>
                <a href="categorias.php" class="btn btn-outline-secondary">
                    Explorar fichas de categorías
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
                <?= $hayFiltros ? 'para tu selección' : 'en la programación activa' ?>.
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
