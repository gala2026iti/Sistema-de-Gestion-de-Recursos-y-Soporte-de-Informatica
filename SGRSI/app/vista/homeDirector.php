<!-- <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dirección - SGRSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="../assets/img/logo_iti.png" alt="Logo">
    </header>

    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
            <ul class="nav-opciones-sistema">
                    <li><a href="../../public/paginaWeb/cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
            </ul>
            </section>
            <ul class="nav-menu">
                <li class="desplegable"><a href="direccion/d-estadoEquipos.html">Estado de equipos</a></li>
                <li class="desplegable"><a href="direccion/d-reportes.html">Reportes y estadisticas</a></li>
                <li><a href="direccion/d-metricas.html">Metricas del sistema</a></li>
            </ul>
        </section>
    </nav>

    <main class="container py-4">
        <section class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Tickets abiertos</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantTicketsAbiertos">0</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Tickets cerrados</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantTicketsCerrados">0</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Equipos activos</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantEquiposActivos">0</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Equipos inactivos</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantEquiposInactivos">0</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Solicitudes pendientes</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantSolicitudesPendientes">0</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm text-center p-3 border-0 h-100">
                    <h3 class="h5 text-secondary fw-semibold">Préstamos activos</h3>
                    <p class="display-4 fw-bold text-primary my-2" id="cantPrestamosActivos">0</p>
                </div>
            </div>
        </section>

        <hr class="my-5">

        <section class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card p-4 shadow-sm border-0">
                    <h4 class="h5 fw-bold text-secondary mb-3 text-center">Distribución de Tickets por Sector</h4>
                    <div class="ratio ratio-16x9">
                        <canvas id="graficaTicketsSector"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <span>Copyright 2026 - SGRSI | Instituto Tecnológico de Informática</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/btnMenuCelular.js"></script>
    <script src="../assets/js/verificarSesion.js"></script>
    <script src="../assets/js/cerrarSesion.js"></script>
    <script src="../assets/js/dashboardDirector.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>