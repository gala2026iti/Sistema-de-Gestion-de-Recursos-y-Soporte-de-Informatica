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
                <ul class="nav-opciones-sistema">
                    <li class="desplegable desplegable-derecha" id="menuUsuario" data-rol-actual="<?= htmlspecialchars($_SESSION['rolActual'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <a href="#">
                        <?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') . " - " ?>
                        <?php switch($_SESSION['rolActual'] ?? ''): case "administrador": ?> Administrador
                        <?php break; case "tecnico": ?> Técnico
                        <?php break; case "docente": ?> Docente
                        <?php endswitch; ?> 🡻</a>
                        <ul class="desplegable-menu">

                            <?php if($_SESSION['rolActual'] !== "docente" && $_SESSION['docente']): ?>
                            <li>
                            <form class="form-estado" action="../../../app/controlador/procesarCambioRol.php" method="post">
                                <button type="submit" class="cambiar-rol">Cambiar a Docente</button>
                                <input type="hidden" name="rol" value="docente">
                                <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">
                            </form>
                            </li>
                            <?php endif; ?>

                            <?php if($_SESSION['rolActual'] !== "tecnico" && $_SESSION['tecnico']): ?>
                            <li>
                            <form class="form-estado" action="../../../app/controlador/procesarCambioRol.php" method="post">
                                <button type="submit" class="cambiar-rol">Cambiar a Tecnico</button>
                                <input type="hidden" name="rol" value="tecnico">
                                <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">
                            </form>
                            </li>
                            <?php endif; ?>

                            <?php if($_SESSION['rolActual'] !== "administrador" && $_SESSION['administrador']): ?>
                            <li>
                            <form class="form-estado" action="../../../app/controlador/procesarCambioRol.php" method="post">
                                <button type="submit" class="cambiar-rol">Cambiar a Administrador</button>
                                <input type="hidden" name="rol" value="administrador">
                                <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">
                            </form>
                            </li>
                            <?php endif; ?>

                            <li><a href="../../../public/paginaWeb/cerrarSesion.php" method="get" id="cerrarSesion">Cerrar Sesion</a></li>
                        </ul>
                    </li>
                </ul>
            </section>
            <?php if($_SESSION["rolActual"] === "administrador"): ?>
            <ul class="nav-menu">
                <li class="desplegable"><a href="../homeAdmin.php">Dashboard</a></li>
                <li class="desplegable"><a href="../administracion/estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="../administracion/reportes.php">Reportes y estadisticas</a></li>
                <li><a href="../administracion/metricas.php">Metricas del sistema</a></li>
            </ul>
            <ul class="nav-menu">
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="gestionUsuarios.php">Gestion de usuarios</a></li>
                    </ul>
                </li>
            </ul>
            <?php elseif ($_SESSION["rolActual"] === "tecnico"): ?>
                        <ul class="nav-menu">
                <li class="desplegable">
                    <a href="#">Gestión de tickets 🡻 </a>
                    <ul class="desplegable-menu">
                        <li><a href="../homeAdmin.php">Tickets registrados</a></li>
                        <li><a href="../tecnico/ticketsPersonales.php">Tickets asignados</a></li>
                    </ul>
                </li>
                <li class="desplegable">
                        <li><a href="../tecnico/tablaPrestamos.php">Tabla de prestamos</a></li>
                </li>
                <li><a href="../tecnico/gestionSolicitudes.php">Gestion de solicitudes</a></li>
            </ul>
            <?php endif; ?>
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