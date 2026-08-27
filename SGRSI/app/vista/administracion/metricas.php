<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metricas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../../public/assets/css/direccion/metricas.css">
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="img-logo" src="../../../public/assets/img/logo_iti.png" alt="Logo">
    </header>

    <nav class="navbarSGRSI">
      <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
            <ul class="nav-menu">
              <li><a href="../../public/paginaWeb/cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
            </ul>
            </section>
            <ul class="nav-menu">
                <li class="desplegable"><a href="../homeAdmin.php">Dashboard</a></li>
                <li class="desplegable"><a href="estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="reportes.php">Reportes y estadisticas</a></li>
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="gestionUsuarios.php">Gestion de usuarios</a></li>
                        <li><a href="gestionInventarioTecnologico.php">Gestion de inventario de equipos</a></li>
                    </ul>
                </li>
            </ul>
           
        </section>
    </nav>

    <main class="container-fluid py-4 px-3">
        <section class="row g-4 mb-4">
            <article class="col-12 col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted small fw-bold">Total Equipos</span>
                    <h3 class="display-6 fw-bold text-primary m-0" id="cantTotalEquipos">0</h3>
                </div>
            </article>
            <article class="col-12 col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted small fw-bold">Equipos con Fallas Activas</span>
                    <h3 class="display-6 fw-bold text-warning m-0" id="cantEquiposFallados">0</h3>
                </div>
            </article>
            <article class="col-12 col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted small fw-bold">Último Incidente</span>
                    <h3 class="fs-5 fw-bold text-secondary m-0" id="fechaUltimoIncidente">N/A</h3>
                </div>
            </article>
        </section>

        <section class="row g-4 mb-4">
            <article class="col-12 col-md-8">
                <div class="card shadow-sm border-0 bg-white">
                    <header class="card-header fw-semibold bg-white border-0 pt-3">
                        Ranking de Equipos con Más Fallas
                    </header>
                    <div class="card-body table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Equipo / PC</th>
                                    <th scope="col">Salón / Sector</th>
                                    <th scope="col">Fallas Registradas</th>
                                </tr>
                            </thead>
                            <tbody id="tablaFallas"></tbody>
                        </table>
                    </div>
                </div>
            </article>

            <article class="col-12 col-md-4 d-flex flex-column gap-4">
                <div class="card p-3 shadow-sm border-0 bg-white flex-fill">
                    <span class="text-muted small fw-bold">Equipo Más Problemático</span>
                    <h4 class="fw-bold text-dark mt-2" id="equipoMasFallado">Ninguno</h4>
                    <span class="text-muted small">Cantidad: <strong id="cantEquipoMasFallado">0</strong></span>
                </div>
                <div class="card p-3 shadow-sm border-0 bg-white flex-fill">
                    <span class="text-muted small fw-bold">Incidencias Graves / Críticas</span>
                    <h3 class="text-danger fw-bold display-6 mt-2" id="cantIncidencias">0</h3>
                </div>
            </article>
        </section>

        <section class="row g-4">
            <article class="col-12">
                <div class="card shadow-sm border-0 bg-white">
                    <header class="card-header fw-semibold bg-primary text-white py-3 rounded-top">
                        Usuarios registrados en el Sistema
                    </header>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4 py-3">Nombre Completo / Usuario</th>
                                    <th scope="col" class="py-3">Correo Electrónico</th>
                                    <th scope="col" class="py-3">Rol Asignado</th>
                                    <th scope="col" class="pe-4 py-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaUsuarios">
                                </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </section>
    </main>

    <footer>
        <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
    </footer>

    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../../public/assets/js/metricasDirector.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>