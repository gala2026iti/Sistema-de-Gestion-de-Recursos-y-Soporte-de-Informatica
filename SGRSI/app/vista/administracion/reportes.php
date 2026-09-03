<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes y estadisticas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../public/assets/css/global.css">
  <link rel="stylesheet" href="../../../public/assets/css/direccion/reportes.css">
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
        <li class="desplegable"><a href="../homeAdmin.php">Dashboard</a></li>
        <li class="desplegable"><a href="estadoEquipos.php">Estado de equipos</a></li>
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
      </ul>
    </section>
  </nav>

  <main class="container-fluid py-4 px-0">
    <h2 class="centro mt-2 text-primary">Lista de tickets</h2>
    <span class="centro mb-3 text-muted">Estos son todos los tickets y el estado en que se encuentran</span>

    <section
      class="espacio-arriba-tabla d-flex flex-row justify-content-start justify-content-md-center align-items-start gap-4 pb-3 overflow-x-auto w-100 px-3">

      <table class="kanban-columna text-center pb-3 w-100" id="tablaPendiente">
        <thead>
          <tr>
            <th class="bg-danger text-white py-3 fs-5 rounded-top">Pendiente</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <table class="kanban-columna pb-3 text-center w-100" id="tablaEnProceso">
        <thead>
          <tr>
            <th class="bg-warning text-white py-3 fs-5 rounded-top">En progreso</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <table class="kanban-columna pb-3 text-center w-100" id="tablaResuelto">
        <thead>
          <tr>
            <th class="bg-success text-white py-3 fs-5 rounded-top">Cerrado</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

    </section>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/btnMenuCelular.js"></script>
  <script src="../../../public/assets/js/verificarSesion.js"></script>
  <script src="../../../public/assets/js/cerrarSesion.js"></script>
  <script src="../../../public/assets/js/reportesDirector.js" defer></script>
</body>

</html>