<?php
/**
 * FLOCK — Agenda Cultural Municipal
 * Página de Inicio y Catálogo de Eventos (Punto de entrada web)
 */

require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Agenda Cultural y Calendario de Eventos';
$activeNav = 'home';


require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container">
    <!-- Hero Banner Institucional -->
    <section class="hero-banner p-4 p-md-5 mb-4 rounded-3 text-white bg-flock-primary border">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-danger text-uppercase px-3 py-2 mb-2">Programación Oficial 2026</span>
                <h1 class="display-5 fw-bold mb-3">Cultura en Villaverde de la Ribera</h1>
                <p class="lead mb-4 text-white-50">Explora la agenda cultural de nuestro municipio: festivales, cinefórum, música en directo, arte urbano y encuentros vecinales en nuestros espacios públicos.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#catalogo" class="btn btn-flock-accent btn-lg px-4">Ver Próximos Eventos</a>
                    <a href="#calendario" class="btn btn-outline-light btn-lg px-4">Calendario del Mes</a>
                </div>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-light border-opacity-25">
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-building me-1" aria-hidden="true"></i> Espacios Abiertos</h2>
                    <p class="fs-sm text-white-50 mb-0">5 recintos municipales con aforos controlados y reservas gratuitas o a precios populares.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Componente de Calendario Híbrido -->
    <section id="calendario" class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h3 fw-bold mb-0">Calendario Cultural</h2>
                <p class="text-muted mb-0">Selecciona un día destacado para ver las actividades programadas</p>
            </div>
        </div>

        <div class="calendar-container">
            <div class="calendar-header">
                <h3 class="h5 fw-bold mb-0">Noviembre 2026</h3>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" disabled>&lt; Anterior</button>
                    <button class="btn btn-outline-secondary" disabled>Siguiente &gt;</button>
                </div>
            </div>
            
            <div class="calendar-grid">
                <div class="calendar-day-header">Lun</div>
                <div class="calendar-day-header">Mar</div>
                <div class="calendar-day-header">Mié</div>
                <div class="calendar-day-header">Jue</div>
                <div class="calendar-day-header">Vie</div>
                <div class="calendar-day-header">Sáb</div>
                <div class="calendar-day-header">Dom</div>

                <!-- Días de prueba de Noviembre 2026 -->
                <div class="calendar-day-cell text-muted">27</div>
                <div class="calendar-day-cell text-muted">28</div>
                <div class="calendar-day-cell text-muted">29</div>
                <div class="calendar-day-cell text-muted">30</div>
                <div class="calendar-day-cell">1</div>
                <div class="calendar-day-cell">2</div>
                <div class="calendar-day-cell">3</div>

                <div class="calendar-day-cell">4</div>
                <div class="calendar-day-cell">5</div>
                <div class="calendar-day-cell">6</div>
                <div class="calendar-day-cell">7</div>
                <div class="calendar-day-cell">8</div>
                <div class="calendar-day-cell">9</div>
                <div class="calendar-day-cell">10</div>

                <div class="calendar-day-cell">11</div>
                <div class="calendar-day-cell">12</div>
                <div class="calendar-day-cell">13</div>
                <div class="calendar-day-cell">14</div>
                <div class="calendar-day-cell">15</div>
                <div class="calendar-day-cell">16</div>
                <div class="calendar-day-cell">17</div>

                <div class="calendar-day-cell">18</div>
                <div class="calendar-day-cell">19</div>
                <div class="calendar-day-cell has-events" title="Festival Urbano de Otoño">
                    <span>20</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell has-events" title="Ciclo Cine Social">
                    <span>21</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell has-events" title="Noche Acústica">
                    <span>22</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell">23</div>
                <div class="calendar-day-cell has-events" title="Exposición Arte Urbano">
                    <span>24</span>
                    <span class="event-dot"></span>
                </div>

                <div class="calendar-day-cell has-events" title="Conferencia Sostenibilidad">
                    <span>25</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell">26</div>
                <div class="calendar-day-cell has-events" title="Gala Coral">
                    <span>27</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell has-events" title="Feria Autoedición">
                    <span>28</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell has-events" title="Cinefórum Juvenil">
                    <span>29</span>
                    <span class="event-dot"></span>
                </div>
                <div class="calendar-day-cell">30</div>
                <div class="calendar-day-cell text-muted">1</div>
            </div>
        </div>
    </section>

    <!-- Catálogo de Eventos Destacados -->
    <section id="catalogo" class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="h3 fw-bold mb-0">Próximas Actividades</h2>
                <p class="text-muted mb-0">Inscripción directa con reserva de plazas y aforos en tiempo real</p>
            </div>
            
            <!-- Barra de Filtros Tradicionales (GET) -->
            <form action="index.php" method="GET" class="d-flex flex-wrap gap-2">
                <select name="categoria" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Todas las Categorías</option>
                    <option value="festivales">Festivales y Encuentros</option>
                    <option value="cineforum">Cinefórum y Audiovisual</option>
                    <option value="musica-directo">Música en Directo</option>
                    <option value="arte-urbano">Arte Urbano y Exposiciones</option>
                    <option value="charlas-conferencias">Charlas y Conferencias</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
            </form>
        </div>

        <!-- Rejilla Responsive de Tarjetas (Bootstrap: col-12 col-md-6 col-lg-4) -->
        <div class="row g-4">
            <!-- Tarjeta 1: Festival -->
            <div class="col-12 col-md-6 col-lg-4">
                <article class="event-card">
                    <div class="event-card-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=600&auto=format&fit=crop&q=80" alt="Festival Urbano de Otoño" class="event-card-img">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge-category" style="background-color: #e63946;">Festivales</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-3">
                            <span class="badge-price">Gratuito</span>
                        </div>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="h5 fw-bold mb-2">Festival Urbano de Otoño</h3>
                        <p class="text-muted fs-sm mb-3 flex-grow-1">Música en directo, creadores emergentes locales y actividades comunitarias al aire libre.</p>
                        <div class="fs-xs text-secondary mb-3 d-flex flex-column gap-1">
                            <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i> <strong>20 de Noviembre 2026</strong> — 18:00h</span>
                            <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Plaza de las Artes</span>
                            <span><i class="bi bi-people me-1" aria-hidden="true"></i> <strong>320 plazas disponibles</strong> (de 400)</span>
                        </div>
                        <a href="evento.php?id=1" class="btn btn-flock-accent w-100 text-center">Detalles e Inscripción</a>
                    </div>
                </article>
            </div>

            <!-- Tarjeta 2: Cinefórum -->
            <div class="col-12 col-md-6 col-lg-4">
                <article class="event-card">
                    <div class="event-card-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&auto=format&fit=crop&q=80" alt="Ciclo Cine Social" class="event-card-img">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge-category" style="background-color: #457b9d;">Cinefórum</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-3">
                            <span class="badge-price">Gratuito</span>
                        </div>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="h5 fw-bold mb-2">Ciclo Cine Social: "Voces Vecinales"</h3>
                        <p class="text-muted fs-sm mb-3 flex-grow-1">Proyección del documental y mesa redonda posterior con asociaciones locales.</p>
                        <div class="fs-xs text-secondary mb-3 d-flex flex-column gap-1">
                            <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i> <strong>21 de Noviembre 2026</strong> — 19:30h</span>
                            <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Cinefórum Central</span>
                            <span><i class="bi bi-people me-1" aria-hidden="true"></i> <strong>65 plazas disponibles</strong> (de 80)</span>
                        </div>
                        <a href="evento.php?id=2" class="btn btn-flock-accent w-100 text-center">Detalles e Inscripción</a>
                    </div>
                </article>
            </div>

            <!-- Tarjeta 3: Música -->
            <div class="col-12 col-md-6 col-lg-4">
                <article class="event-card">
                    <div class="event-card-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&auto=format&fit=crop&q=80" alt="Noche de Música Acústica" class="event-card-img">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge-category" style="background-color: #2a9d8f;">Música</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-3">
                            <span class="badge-price">3.50 €</span>
                        </div>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="h5 fw-bold mb-2">Noche de Música Acústica Emergente</h3>
                        <p class="text-muted fs-sm mb-3 flex-grow-1">Concierto íntimo con tres cantautores y bandas locales en formato desenchufado.</p>
                        <div class="fs-xs text-secondary mb-3 d-flex flex-column gap-1">
                            <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i> <strong>22 de Noviembre 2026</strong> — 20:00h</span>
                            <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Espacio Joven "La Nave"</span>
                            <span><i class="bi bi-people me-1" aria-hidden="true"></i> <strong>85 plazas disponibles</strong> (de 100)</span>
                        </div>
                        <a href="evento.php?id=3" class="btn btn-flock-accent w-100 text-center">Detalles e Inscripción</a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
