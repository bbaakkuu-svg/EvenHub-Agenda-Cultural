</main>

<footer class="site-footer bg-flock-primary text-white py-4 mt-auto">
    <div class="container">
        <div class="row gy-3 align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start">
                <p class="mb-1 fw-bold">Ayuntamiento de Villaverde de la Ribera</p>
                <p class="mb-0 text-white-50 fs-sm">Concejalía de Cultura y Participación Ciudadana — Agenda Oficial FLOCK</p>
            </div>
            <div class="col-12 col-md-6 text-center text-md-end">
                <p class="mb-1 text-white-50 fs-sm">Plataforma desarrollada para DWC, DWS y DIW</p>
                <p class="mb-0 fs-xs text-white-50">© <?= date('Y') ?> Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS (Necesario exclusivamente para el botón colapsable en móviles) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<!-- Scripts Propios de Interactividad (DWC) -->
<script src="<?= $basePath ?? '' ?>js/cookies.js"></script>
<script src="<?= $basePath ?? '' ?>js/validator.js"></script>
</body>
</html>
