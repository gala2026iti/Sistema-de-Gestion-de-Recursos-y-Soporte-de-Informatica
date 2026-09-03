<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitudes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/global.css">
  <link rel="stylesheet" href="../../assets/css/index.css">
</head>

<body data-rol-permitido="docente">
  <header class="d-flex justify-content-center align-items-center py-4">
    <img class="img-logo" src="../../assets/img/logo_iti.png" alt="Logo">
  </header>
  <nav class="navbarSGRSI">
    <section class="nav-container">
      <section class="nav-primera-fila">
        <button class="btn-menu" id="btnMenu">☰</button>
        <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
<!-- TOFIX: No funciona la opcion para cerrar sesión-->
      <ul class="nav-menu">
            <li class="desplegable">
              <a href="Prueba"></a>
              <ul class="desplegable-menu">
                        <li><a href="#">Cambiar a Docente</a></li>
                        <li><a href="#">Cambiar a Tecnico</a></li>
                        <li><a href="#">Cambiar a Administrador</a></li>
                        <li><a href="../../../public/paginaWeb/cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
                    </ul>
            </li>
      </ul>
      </section>
      <ul class="nav-menu">
        <li><a href="../homeDocente.php" id="btnIncidencias">Registro de incidencias</a></li>
      </ul>
    </section>
  </nav>

  <main class="d-flex flex-column align-items-center justify-content-center py-5 px-3">
    <section class="text-center mb-4">
      <h2 class="fw-bold texto-azul mb-3">Solicitud de servicio</h2>
      <p class="text-muted mb-1 fs-6">Aquí podrás hacer solicitudes en el caso de precisar ayuda con la planificación o preparación de clases.</p>
      <p class="text-danger fw-semibold small">Le pedimos por favor realizar la solicitud con un tiempo de anticipación adecuado.</p>
    </section>

    <section class="Tarjeta-login card p-4 shadow border-0 w-100">
      <form action="" method="post" id="formSolicitud">
        <fieldset class="border-0 p-0 m-0">

          <div class="mb-3 text-start">
            <label for="asunto" class="form-label fw-semibold texto-azul-dark">Asunto:</label>
            <input type="text" id="asunto" class="form-control form-control-lg" placeholder="Ej: Instalación de NetBeans" required>
          </div>

          <div class="mb-3 text-start">
            <label for="descripcion" class="form-label fw-semibold texto-azul-dark">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Ej: Solicito que las PC del Laboratorio 1 tengan instalado NetBeans" rows="4" required></textarea>
          </div>

          <div class="mb-4 text-start">
            <label for="fecha" class="form-label fw-semibold texto-azul-dark">Fecha y Hora solicitada:</label>
            <input type="datetime-local" name="fecha" id="fecha" class="form-control form-control-lg" required>
          </div>

        </fieldset>

        <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-dark fs-5 shadow-sm">Enviar</button>
      </form>
    </section>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnológico de Informática</span>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/js/btnMenuCelular.js"></script>
  <script src="../../assets/js/cerrarSesion.js"></script>
  <script src="../../assets/js/verificarSesion.js"></script>
  <script src="../../assets/js/ingresoSolicitudes.js"></script>
</body>

</html>