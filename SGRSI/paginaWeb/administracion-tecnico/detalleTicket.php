<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Ticket - SGRSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/global.css">
</head>

<body data-rol-permitido="administrador tecnico">
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="../../public/assets/img/logo_iti.png" alt="Logo">
    </header>

    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
                <ul class="nav-opciones-sistema">
                    <li><a href="../../public/cerrarSesion.php">Cerrar Sesion</a></li>
                </ul>
            </section>

            <ul class="nav-menu">
                <li class="desplegable">
                    <a href="#">Gestión de tickets 🡻 </a>
                    <ul class="desplegable-menu">
                        <li><a href="../../public/homeAdmin.php">Tickets registrados</a></li>
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
<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador'): ?>
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">⚙ Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="gestionUsuarios.html">Gestion de usuarios</a></li>
                        <li><a href="gestionInventarioTecnologico.html">Gestion de inventario de equipos</a></li>
                    </ul>
                </li>
<?php endif; ?>
            </ul>
        </section>
    </nav>

    <main class="container-fluid px-4 py-4">
        <section class="border-bottom pb-3 mb-4">
            <div class="d-flex align-items-center justify-content-between w-100">
                <h2 class="text-start mb-0 text-primary" id="tituloTicket">
                    Nombre no disponible... <span class="fw-bold" id="txt-id-ticket">#--</span>
                </h2>
                <a id="btnVolver" class="btn-asignar">Volver</a>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                <span class="badge bg-secondary px-2 py-1 fs-6" id="pendiente">Pendiente</span>
                <span class="badge bg-secondary px-2 py-1 fs-6" id="proceso">En proceso</span>
                <span class="badge bg-secondary px-2 py-1 fs-6" id="resuelto">Resuelto</span>
            </div>
        </section>

        <form id="formDetalleTicket" class="form bg-light p-4 rounded-3 border mb-5">
            <h3 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Control y Citación del Ticket</h3>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="usuarioAsignado" class="form-label small fw-bold text-secondary mb-0">Asignación de
                    Encargados</label>
                <button type="button" id="btnAutoasignar" class="btn btn-success py-1 px-2 fs-7">Asignarse a mí</button>
            </div>
            <input type="text" id="usuarioAsignado" name="usuarioAsignado" class="form-control bg-white text-dark mb-3"
                readonly>

            <label for="selectorEstado" class="form-label fw-bold text-secondary">Estado del flujo operativo</label>
            <select id="selectorEstado" name="selectorEstado" class="form-select mb-3">
                <option value="pendiente">🔴 Pendiente</option>
                <option value="en proceso">🟡 En proceso</option>
            </select>

            <label for="selectorGravedad" class="form-label fw-bold text-secondary">Gravedad de la incidencia</label>
            <select id="selectorGravedad" name="selectorGravedad" class="form-select mb-3">
                <option value="ligera">🟢 Ligera</option>
                <option value="media">🟡 Media</option>
                <option value="grave">🔴 Grave</option>
            </select>

            <label for="ubicacionSalon" class="form-label small fw-bold text-secondary">Ubicación</label>
            <input type="text" id="ubicacionSalon" name="ubicacionSalon" class="form-control bg-white mb-3" readonly>

            <label for="entradaPC" class="form-label small fw-bold text-secondary">ID PC</label>
            <input type="text" id="entradaPC" name="entradaPC" class="form-control bg-white mb-3" readonly>

            <label for="entradaCategoria" class="form-label small fw-bold text-secondary">Categoría del Problema</label>
            <input type="text" id="entradaCategoria" name="entradaCategoria" class="form-control bg-white mb-3"
                readonly>

            <label for="contenido" class="form-label small fw-bold text-secondary">Descripción original del
                problema</label>
            <textarea name="contenido" id="contenido" class="form-control bg-white mb-3" readonly></textarea>

            <div id="espacioJustificacion" class="mb-4 d-none">
                <label for="justificacion" class="form-label small fw-bold text-success">Resolución Técnica Técnico
                    (Justificación)</label>
                <textarea id="justificacion" class="form-control bg-white text-dark border-success" readonly></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <button type="button" id="btnFinalizarTicket" class="btn btn-success fw-bold"
                    style="display: none;">Finalizar ticket</button>
                <button type="submit" class="btn btn-primary">Guardar cambios de estado</button>
            </div>
        </form>

        <section class="border-top pt-4">
            <h2 class="text-primary mb-3">Comentarios registrados</h2>

            <div id="contenedorComentarios"></div>

            <article class="card mb-4 shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="text-warning h6 fw-bold text-center m-0 py-1">Escribir comentario</h3>
                </div>
                <div class="card-body p-2">
                    <textarea id="nuevoComentario" class="form-control border-0"
                        placeholder="Escribe un comentario o actualización de la PC..." rows="4"></textarea>
                    <div class="d-flex justify-content-end p-2 border-top mt-2">
                        <button type="button" id="btnGuardarComentario" class="btn btn-primary">Comentar</button>
                    </div>
                </div>
            </article>
        </section>
    </main>

    <footer>
        <span>Copyright 2026 - SGRSI | Instituto tecnológico de Informática</span>
    </footer>

    <script src="../../public/assets/js/btnMenuCelular.js"></script>
        <script src="../../public/assets/js/gestionTickets.js"></script>
    
</body>

</html>