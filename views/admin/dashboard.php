<?php
declare(strict_types=1);

/**
 * Vista del Dashboard Administrativo (views/admin/dashboard.php).
 * FLOCK — Agenda Cultural Municipal
 *
 * Variables disponibles:
 * @var array{id: int, username: string, nombre_completo: string, email: string, rol: string} $usuario
 * @var array{eventos_activos: int, total_reservas: int, plazas_reservadas: int, total_espacios: int} $metricas
 * @var array<int, array<string, mixed>> $proximosEventos
 * @var string|null $error
 * @var string|null $aviso
 */
?>

<div class="container my-4">
    <!-- Encabezado Institucional del Panel -->
    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-4 border-bottom gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fs-4" aria-hidden="true">🏛️</span>
                <h1 class="h3 fw-bold mb-0 text-flock-primary">Panel de Gestión Cultural</h1>
                <span class="badge bg-secondary-subtle text-secondary border fs-xs text-uppercase">
                    <?= e($usuario['rol'] ?? 'gestor') ?>
                </span>
            </div>
            <p class="text-muted mb-0 fs-sm">
                Sesión iniciada como <strong><?= e($usuario['nombre_completo'] ?? $usuario['username']) ?></strong> (<?= e($usuario['email'] ?? '') ?>)
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="../eventos.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <span>🌐</span> Ver Catálogo Público
            </a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                <span>🚪</span> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Mensajes de Feedback Flash -->
    <?php if (!empty($aviso)): ?>
        <div class="alert alert-info alert-dismissible fade show fs-sm d-flex align-items-center gap-2" role="alert">
            <span aria-hidden="true">ℹ️</span>
            <div><?= e($aviso) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show fs-sm d-flex align-items-center gap-2" role="alert">
            <span aria-hidden="true">⚠️</span>
            <div><?= e($error) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de Métricas Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 p-3 bg-white border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fs-xs text-uppercase fw-semibold d-block">Eventos Activos</span>
                        <span class="h2 fw-bold mb-0 text-dark"><?= (int) $metricas['eventos_activos'] ?></span>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle fs-4" aria-hidden="true">
                        🎭
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 p-3 bg-white border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fs-xs text-uppercase fw-semibold d-block">Reservas Confirmadas</span>
                        <span class="h2 fw-bold mb-0 text-dark"><?= (int) $metricas['total_reservas'] ?></span>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-circle fs-4" aria-hidden="true">
                        🎟️
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 p-3 bg-white border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fs-xs text-uppercase fw-semibold d-block">Plazas Ocupadas</span>
                        <span class="h2 fw-bold mb-0 text-dark"><?= (int) $metricas['plazas_reservadas'] ?></span>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-circle fs-4" aria-hidden="true">
                        👥
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 p-3 bg-white border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fs-xs text-uppercase fw-semibold d-block">Espacios Culturales</span>
                        <span class="h2 fw-bold mb-0 text-dark"><?= (int) $metricas['total_espacios'] ?></span>
                    </div>
                    <div class="p-3 bg-warning-subtle text-warning rounded-circle fs-4" aria-hidden="true">
                        📍
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Acciones y Navegación Administrativa -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="h5 fw-bold mb-0 text-dark">Gestión de la Programación Cultural</h2>
            <div class="d-flex gap-2">
                <a href="../eventos.php" class="btn btn-outline-primary btn-sm">
                    🔍 Explorar Todo el Catálogo
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Tabla de Próximos Eventos -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-xs text-uppercase text-muted">
                        <tr>
                            <th scope="col" class="ps-3">Evento</th>
                            <th scope="col">Disciplina</th>
                            <th scope="col">Espacio</th>
                            <th scope="col">Fecha y Hora</th>
                            <th scope="col" class="text-center">Aforo / Reservas</th>
                            <th scope="col" class="text-center">Estado</th>
                            <th scope="col" class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="fs-sm">
                        <?php if (empty($proximosEventos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No hay eventos registrados en la base de datos municipal.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($proximosEventos as $evento): ?>
                                <?php
                                    $aforoTotal = (int) $evento['aforo_total'];
                                    $ocupadas   = (int) $evento['plazas_ocupadas'];
                                    $porcentaje = ($aforoTotal > 0) ? min(100, round(($ocupadas / $aforoTotal) * 100)) : 0;
                                ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">
                                        <?= e($evento['titulo']) ?>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?= e($evento['color_badge'] ?: '#4a5568') ?>; color: #fff;">
                                            <?= e($evento['categoria_nombre']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <?= e($evento['espacio_nombre']) ?>
                                    </td>
                                    <td>
                                        <div><?= formatearFecha((string) $evento['fecha']) ?></div>
                                        <small class="text-muted"><?= substr((string) $evento['hora'], 0, 5) ?>h</small>
                                    </td>
                                    <td class="text-center" style="min-width: 140px;">
                                        <div class="d-flex justify-content-between fs-xs mb-1">
                                            <span><?= $ocupadas ?> / <?= $aforoTotal ?></span>
                                            <span class="fw-bold"><?= $porcentaje ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;" role="progressbar" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar <?= ($porcentaje >= 90) ? 'bg-danger' : (($porcentaje >= 60) ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $porcentaje ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($evento['estado'] === 'activo'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                        <?php elseif ($evento['estado'] === 'cancelado'): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">Finalizado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="../eventos.php?q=<?= urlencode((string) $evento['titulo']) ?>" class="btn btn-sm btn-outline-secondary" title="Ver en catálogo público">
                                            👁️
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
