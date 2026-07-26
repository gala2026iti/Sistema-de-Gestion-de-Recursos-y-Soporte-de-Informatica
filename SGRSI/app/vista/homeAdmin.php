<!DOCTYPE html>
<html lang="es">
  
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tickets registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/administracion-tecnico/ticketsRegistrados.css">
</head>

<body data-rol-permitido="administrador tecnico">
   <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="assets/img/logo_iti.png" alt="Logo">    </header>

   <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
                <ul class="nav-opciones-sistema">
                <li><a href="cerrarSesion.php">Cerrar Sesion</a></li>
                </ul>
            </section>
            
            <ul class="nav-menu">
                <li class="desplegable">
                    <a href="#">Gestión de tickets 🡻 </a>
                    <ul class="desplegable-menu">
                        <li><a href="../paginaWeb/administracion-tecnico/ticketsPersonales.php">Tickets asignados</a></li>
                    </ul>
                </li>
                <li class="desplegable">
                    <a href="#">Gestion de prestamos 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="../paginaWeb/administracion-tecnico/tablaPrestamos.php">Tabla de prestamos</a></li>
                        <li><a href="../paginaWeb/administracion-tecnico/inventarioEquipos.php">Inventario de equipos</a></li>
                    </ul>
                </li>
                <li><a href="../paginaWeb/administracion-tecnico/gestionSolicitudes.php">Gestion de solicitudes</a></li>
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

  <main class="container-fluid px-0 py-3">
    <a href="../paginaWeb/administracion-tecnico/historialGeneral.php?tipo=tickets" class="btn btn-primary ms-3">Historial de tickets</a>
    <h2 class="centro mt-3 text-primary">Tickets notificados</h2>
    <span class="centro mb-4">A continuación se muestran los tickets</span>

    <section class="filtros">
      <select id="filtroFecha">
        <option value="recientes" selected>Más recientes</option>
        <option value="antiguos">Más antiguos</option>
      </select>

      <select id="filtroGravedad">
        <option value="">Filtrar gravedad</option>
        <option value="ligera">Ligera</option>
        <option value="media">Media</option>
        <option value="grave">Grave</option>
      </select>

      <select id="filtroClasificacion">
        <option value="">Filtrar por clasificación</option>
        <option value="hardware">Hardware</option>
        <option value="software">Software</option>
        <option value="red">Red</option>
      </select>

      <select id="filtroEstado">
        <option value="">Filtrar por estado</option>
        <option value="pendiente">Pendiente</option>
        <option value="en proceso">En proceso</option>
        <option value="resuelto">Resuelto</option>
      </select>
    </section>

    <section class="table-responsive w-100 m-0 pb-5">
      <table id="tablaEquipos">
        <thead>
          <tr>
            <th>Asunto</th>
            <th>Tipo</th>
            <th>Gravedad</th>
            <th>Estado</th>
            <th>Información de creacion</th>
            <th>Asignación de ticket</th>
          </tr>
        </thead>
        <tbody>
          </tbody>
      </table>
    </section>
  </main>

  <footer>
    <span>Copyright 2026 - SGRSI | Instituto tecnologico de Informática</span>
  </footer>

  <script src="assets/js/btnMenuCelular.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/ticketsRegistrados.js"></script>
</body>

</html>