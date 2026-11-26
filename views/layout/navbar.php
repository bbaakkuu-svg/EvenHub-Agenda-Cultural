<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-flock-primary shadow-sm" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
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
                        <a class="nav-link <?= (!isset($activeNav) || $activeNav === 'home') ? 'active' : '' ?>" href="index.php">Agenda y Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'eventos') ? 'active' : '' ?>" href="eventos.php">Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'categorias') ? 'active' : '' ?>" href="categorias.php">Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($activeNav) && $activeNav === 'espacios') ? 'active' : '' ?>" href="index.php#espacios">Espacios</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm px-3" href="login.php">
                            Acceso Gestor
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
