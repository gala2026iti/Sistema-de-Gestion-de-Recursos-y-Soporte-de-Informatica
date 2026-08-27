<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tickets Personales - Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../public/assets/css/global.css">
  <link rel="stylesheet" href="../../../public/assets/css/administracion-tecnico/ticketsPersonales.css">
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
                        <li><a href="../homeTecnico.php">Tickets registrados</a></li>
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

  <main class="container-fluid py-4 px-0">
    <h2 class="centro mt-2 text-primary">Tickets personales</h2>
    <span class="centro mb-4 text-muted">Estos son tus tickets y el estado en que se encuentran</span>
    
    <section class="espacio-arriba-tabla d-flex flex-row justify-content-start justify-content-md-center align-items-start gap-4 pb-3 overflow-x-auto w-100 px-3">
       
        <table class="kanban-columna text-center pb-3 w-100" id=tablaPendiente>
          <thead>
            <tr>
              <th class="bg-danger text-white py-3 fs-5 rounded-top">Pendiente</th>
            </tr>
          </thead>
          <tbody>
            </tbody>
        </table>

        <table class="kanban-columna pb-3 text-center w-100" id="tablaEnProceso">
          <thead>
            <tr>
              <th class="bg-warning text-white py-3 fs-5 rounded-top">En progreso</th>
            </tr>
          </thead>
          <tbody>
            </tbody>
        </table>

        <table class="kanban-columna pb-3 text-center w-100" id="tablaResuelto">
          <thead>
            <tr>
              <th class="bg-success text-white py-3 fs-5 rounded-top">Cerrado / Resuelto</th>
            </tr>
          </thead>
          <tbody>
            </tbody>
        </table>

    </section>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnologico de Informática</span>
  </footer>

  <script src="../../../public/assets/js/btnMenuCelular.js"></script>
  <script src="../../../public/assets/js/cerrarSesion.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/verificarSesion.js"></script>
  <script src="../../../public/assets/js/ticketsPersonales.js"></script>

</body>
 
</html>