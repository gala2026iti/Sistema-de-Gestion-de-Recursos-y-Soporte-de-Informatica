<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Ticket (Dirección) - SGRSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/css/global.css">
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="../../../public/assets/img/logo_iti.png" alt="Logo">
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
                <li class="desplegable"><a href="administracion/estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="administracion/reportes.php">Reportes y estadisticas</a></li>
                <li><a href="administracion/metricas.php">Metricas del sistema</a></li>
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">⚙ Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="administracion/gestionUsuarios.php">Gestion de usuarios</a></li>
                        <li><a href="administracion/gestionInventarioTecnologico.php">Gestion de inventario de equipos</a></li>
                    </ul>
                </li>
            </ul>
        </section>
    </nav>

    <main class="container py-4">
        <section class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="text-start mb-2 text-primary" id="tituloTicket">Cargando Asunto...</h2>
                <div class="d-flex flex-wrap align-items-center gap-2 text-muted small" id="contenedorAsunto">
                </div>
            </div>
            <a href="d-reportes.php" class="btn btn-secondary fw-bold px-4">Volver a Reportes</a>
        </section>

        <section class="bg-light p-4 rounded-3 border mb-5">
            <h3 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Información Técnica del Dispositivo</h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="d-block small fw-bold text-secondary">Docente Emisor</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoDocente">-</p>
                </div>
                <div class="col-md-6">
                    <span class="d-block small fw-bold text-secondary">Fecha de Apertura</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoFecha">-</p>
                </div>
                <div class="col-md-4">
                    <span class="d-block small fw-bold text-secondary">Ubicación Física</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoSalon">-</p>
                </div>
                <div class="col-md-4">
                    <span class="d-block small fw-bold text-secondary">Código del Equipo (ID PC)</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoPC">-</p>
                </div>
                <div class="col-md-4">
                    <span class="d-block small fw-bold text-secondary">Gravedad / Criticidad</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoGravedad">-</p>
                </div>
                <div class="col-12">
                    <span class="d-block small fw-bold text-secondary">Categoría del Incidente</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoCategoria">-</p>
                </div>
                <div class="col-12">
                    <span class="d-block small fw-bold text-secondary">Descripción Original del Problema</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoDescripcion">-</p>
                </div>
                <div class="col-12">
                    <span class="d-block small fw-bold text-secondary">Operadores/Técnicos Asignados</span>
                    <p class="fs-6 p-2 bg-white rounded border" id="infoEncargados">-</p>
                </div>
                <div class="col-12 d-none" id="bloqueJustificacion">
                    <span class="d-block small fw-bold text-success">Resolución Técnica e Informe de Cierre</span>
                    <p class="fs-6 p-2 bg-white rounded border border-success text-dark" id="infoJustificacion">-</p>
                </div>
            </div>
        </section>

        <section class="border-top pt-4">
            <h3 class="text-primary mb-3 h4">Historial de comentarios e hitos</h3>
            <div id="contenedorComentarios"></div>
        </section>
    </main>

    <footer>
        <span>Copyright 2026 - SGRSI | Instituto tecnológico de Informática</span>
    </footer>

    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../../public/assets/js/detalleTicketDirector.js" defer></script>
</body>

</html>