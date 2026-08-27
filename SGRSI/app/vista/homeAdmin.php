<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dirección - SGRSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../public/assets/css/global.css">
        <link rel="stylesheet" href="../../public/assets/css/direccion/panelConsultas.css">
            <link rel="stylesheet" href="../../public/assets/css/direccion/metricas.css">
        
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="img-logo" src="../../public/assets/img/logo_iti.png" alt="Logo">
    </header>

    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
<ul class="nav-menu">
<li><a href="../../public/paginaWeb/cerrarSesion.php"  id="cerrarSesion">Cerrar Sesion</a></li>
</ul>
            </section>
            <ul class="nav-menu">
                <li class="desplegable"><a href="administracion/estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="administracion/reportes.php">Reportes y estadisticas</a></li>
                <li><a href="administracion/metricas.php">Metricas del sistema</a></li>
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="administracion/gestionUsuarios.php">Gestion de usuarios</a></li>
                        <li><a href="administracion/gestionInventarioTecnologico.php">Gestion de inventario de equipos</a></li>
                    </ul>
                </li>
            </ul>
           
        </section>
    </nav>

    <main class="container py-4">
        <section class="row g-4 mb-5">
          <h2 id="titulo-metricas" class="text-primary">Métricas del Dashboard</h2>
            
            <ul class="row g-4 mb-5 list-unstyled">
                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Tickets abiertos</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantTicketsAbiertos">0</p>
                    </article>
                </li>

                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Tickets cerrados</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantTicketsCerrados">0</p>
                    </article>
                </li>

                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Equipos activos</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantEquiposActivos">0</p>
                    </article>
                </li>

                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Equipos inactivos</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantEquiposInactivos">0</p>
                    </article>
                </li>

                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Solicitudes pendientes</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantSolicitudesPendientes">0</p>
                    </article>
                </li>

                <li class="col-12 col-md-6 col-lg-4">
                    <article class="card shadow-sm text-center p-3 border-0 h-100">
                        <h3 class="h5 text-secondary fw-semibold">Préstamos activos</h3>
                        <p class="display-4 fw-bold text-primary my-2" id="cantPrestamosActivos">0</p>
                    </article>
                </li>
            </ul>
        </section>

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
        <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../public/assets/js/verificarSesion.js"></script>
    <script src="../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../public/assets/js/dashboardDirector.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>