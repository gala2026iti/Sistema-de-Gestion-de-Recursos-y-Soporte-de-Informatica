<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial General</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../../public/assets/css/historial.css">
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
                              <li><a href="../../../public/paginaWeb/cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
                </ul>
            </section>
            
            <ul class="nav-menu">
                <li class="desplegable">
                    <a href="#">Gestión de tickets 🡻 </a>
                    <ul class="desplegable-menu">
                        <li><a href="../homeAdmin.php">Tickets registrados</a></li>
                        <li><a href="ticketsPersonales.php">Tickets asignados</a></li>
                    </ul>
                </li>
                <li class="desplegable">
                    <a href="#">Gestion de prestamos 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="tablaPrestamos.php">Tabla de prestamos</a></li>
                        <li><a href="inventarioEquipos.php">Inventario de equipos</a></li>
                    </ul>
                </li>
                <li><a href="gestionSolicitudes.php">Gestion de solicitudes</a></li>
            </ul>
        </section>
    </nav>

    <main class="container-fluid px-4 py-4">
        <section class="mb-4 border-bottom pb-2 d-flex align-items-center justify-content-between w-100">
            <div>
                <h2 class="text-start mb-1 text-primary" id="tituloHistorial">Historial</h2>
                <span class="text-muted fs-6" id="descripcionHistorial">Registros cronológicos del sistema</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="filtroFecha" class="text-muted small fw-bold">Filtrar:</label>
                <input type="date" id="filtroFecha" class="form-control form-control-sm">
            </div>
        </section>

        <section id="contenedorHistorial"></section>

        <section class="mt-4">
            <a href="#" id="btnVolverHistorial" class="btn-asignar">Volver</a>
        </section>
    </main>

    <footer>
        <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
    </footer>

    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../../public/assets/js/historialGeneral.js"></script>
</body>

</html>