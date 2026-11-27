<?php
require_once __DIR__ . '/../../includes/auth.php';
$basePath = $basePath ?? '';
$usuarioActual = estaAutenticado() ? usuarioAutenticado() : null;
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-flock-primary shadow-sm" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $basePath ?>index.php">
                <span class="brand-shield" aria-hidden="true">🏛️</span>
                <span class="brand-text">
                    <span class="fw-bold tracking-tight">FLOCK</span>
                    <small class="d-block text-white-50 fs-xs">Ayuntamiento de Villaverde</small>
                </span>
            </a>

            <!-- Botón hamburguesa accesible para dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#flockNavbar" aria-controls="flockNavbar" aria-expanded="false" aria-label="Alternar menú de navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="flockNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($activeNav) || $activeNav === 'home') ? 'active' : '' ?>" href="<?= $basePath ?>index.php">Agenda y Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'eventos') ? 'active' : '' ?>" href="<?= $basePath ?>eventos.php">Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'categorias') ? 'active' : '' ?>" href="<?= $basePath ?>categorias.php">Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'espacios') ? 'active' : '' ?>" href="<?= $basePath ?>index.php#espacios">Espacios</a>
                    </li>

                    <?php if ($usuarioActual !== null): ?>
                        <li class="nav-item ms-lg-2 dropdown">
                            <a class="btn btn-warning btn-sm text-dark fw-semibold dropdown-toggle d-flex align-items-center gap-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span aria-hidden="true">👤</span>
                                <span><?= htmlspecialchars($usuarioActual['username']) ?></span>
                                <span class="badge bg-dark text-white fs-xs text-uppercase ms-1"><?= htmlspecialchars($usuarioActual['rol']) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <h6 class="dropdown-header text-truncate" style="max-width: 220px;">
                                        <?= htmlspecialchars($usuarioActual['nombre_completo']) ?>
                                    </h6>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 <?= (isset($activeNav) && $activeNav === 'admin') ? 'active' : '' ?>" href="<?= $basePath ?>admin/index.php">
                                        <span>📊</span> Panel de Gestión
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="<?= $basePath ?>logout.php">
                                        <span>🚪</span> Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-light btn-sm px-3" href="<?= $basePath ?>login.php">
                                Acceso Gestor
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
