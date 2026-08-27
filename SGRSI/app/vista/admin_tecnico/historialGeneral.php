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
                <ul class="desplegable-menu">
                        <li><a href="#">Cambiar a Docente</a></li>
                        <li><a href="#">Cambiar a Tecnico</a></li>
                        <li><a href="#">Cambiar a Administrador</a></li>
                        <li><a href="cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
                    </ul>
            </section>
            
            <ul class="nav-menu">

                <!-- NAVBAR ASISTENTE -->
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'asistente'): ?>
                <li class="desplegable">
                    <a href="#">Gestión de tickets 🡻 </a>
                    <ul class="desplegable-menu">
                        <li><a href="../administracion/homeAdmin.php">Tickets registrados</a></li>
                        <li><a href="../administracion/ticketsPersonales.php">Tickets asignados</a></li>
                    </ul>
                </li>
                <li class="desplegable">
                    <a href="#">Gestion de prestamos 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="../administracion/tablaPrestamos.php">Tabla de prestamos</a></li>
                        <li><a href="../administracion/inventarioEquipos.php">Inventario de equipos</a></li>
                    </ul>
                </li>
                <li><a href="../administracion/gestionSolicitudes.php">Gestion de solicitudes</a></li>
                
                <!-- FIN NAVBAR ASISTENTE -->
                
                <?php elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador'): ?>
            
                <!-- NAVBAR ADMINISTRADOR -->

                <li class="desplegable"><a href="../homeAdmin.php">Dashboard</a></li>
                <li class="desplegable"><a href="estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="reportes.php">Reportes y estadisticas</a></li>
                <li><a href="metricas.php">Metricas del sistema</a></li>
            </ul>
            <ul class="nav-menu">
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="gestionUsuarios.php">Gestion de usuarios</a></li>
                        <li><a href="gestionInventarioTecnologico.php">Gestion de inventario de equipos</a></li>

                    </ul>
                </li>
                <?php endif; ?>
                <!-- FIN NAVBAR ADMINISTRADOR -->

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
        <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnologico de Informática</span>
    </footer>

    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../../public/assets/js/historialGeneral.js"></script>
</body>

</html>