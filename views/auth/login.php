<?php
declare(strict_types=1);

/**
 * Vista del Formulario de Inicio de Sesión (views/auth/login.php).
 * FLOCK — Agenda Cultural Municipal
 *
 * Variables disponibles:
 * @var string|null $error Mensaje de error
 * @var string|null $aviso Mensaje informativo / flash
 * @var string $username Nombre de usuario introducido previamente
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <!-- Tarjeta de Acceso -->
            <div class="card border rounded-3">
                <div class="card-body p-4 p-md-5">
                    <!-- Cabecera Institucional -->
                    <div class="text-center mb-4">
                        <div class="mb-2 text-flock-primary fs-1" aria-hidden="true"><i class="bi bi-building"></i></div>
                        <h1 class="h4 fw-bold text-flock-primary mb-1">Acceso a Gestión Cultural</h1>
                        <p class="text-muted fs-sm mb-0">Ayuntamiento de Villaverde de la Ribera</p>
                    </div>

                    <!-- Mensajes de Alerta -->
                    <?php if (!empty($aviso)): ?>
                        <div class="alert alert-info alert-dismissible fade show fs-sm d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <div><?= e($aviso) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show fs-sm d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                            <div><?= e($error) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de Autenticación -->
                    <form method="POST" action="login.php" class="needs-validation" novalidate autocomplete="on">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold fs-sm">Usuario municipal</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted" id="user-icon"><i class="bi bi-person" aria-hidden="true"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    value="<?= e($username) ?>" 
                                    placeholder="admin o gestor_cultura"
                                    required 
                                    autofocus
                                    aria-describedby="user-icon"
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold fs-sm">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted" id="pass-icon"><i class="bi bi-lock" aria-hidden="true"></i></span>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="••••••••" 
                                    required
                                    aria-describedby="pass-icon"
                                >
                            </div>
                        </div>

                        <button type="submit" class="btn btn-flock-primary w-100 py-2 fw-semibold">
                            Iniciar Sesión
                        </button>
                    </form>

                    <!-- Aviso de credenciales de prueba para evaluadores -->
                    <div class="mt-4 p-3 bg-light rounded-3 border fs-xs text-muted">
                        <strong class="text-dark d-block mb-1"><i class="bi bi-key me-1" aria-hidden="true"></i> Credenciales de demostración:</strong>
                        <ul class="list-unstyled mb-0 ps-1">
                            <li>• <strong>admin</strong> / <code>admin123</code> (Rol Administrador General)</li>
                            <li>• <strong>gestor_cultura</strong> / <code>cultura2026</code> (Rol Técnica de Cultura)</li>
                        </ul>
                    </div>

                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none fs-xs text-muted">
                            ← Volver a la portada de la Agenda Cultural
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
