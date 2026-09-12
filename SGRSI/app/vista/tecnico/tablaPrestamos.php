<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Préstamos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../public/assets/css/global.css">
  <link rel="stylesheet" href="../../../public/assets/css/administracion-tecnico/gestionEquipos.css">
  <link rel="stylesheet" href="../../../public/assets/css/formulariospopup.css">
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
            <li><a href="ticketsPersonales.php">Tickets asignados</a></li>
          </ul>
        </li>
        <li class="desplegable">
          <a href="#">Gestion de prestamos 🡻</a>
          <ul class="desplegable-menu">
            <li><a href="inventarioEquipos.php">Inventario de equipos</a></li>
          </ul>
        </li>
        <li><a href="gestionSolicitudes.php">Gestion de solicitudes</a></li>
      </ul>
    </section>
  </nav>

  <main class="container-fluid px-0 py-3">
    <a href="../admin_tecnico/historialGeneral.php?tipo=prestamos" class="btn btn-primary mx-3">Historial de prestamos</a>
    <h2 class="centro mt-3 text-primary">Tabla de prestamos</h2>
    <span class="centro mb-4">A continuacion se muestran los prestamos activos de equipos</span>

<?php if (isset($_GET["resultado"])): ?>
    <span class="alert alert-success d-table text-center mx-auto my-2">
        <?= htmlspecialchars($_GET["resultado"]) ?>
    </span>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
    <span class="alert alert-danger d-table text-center mx-auto my-2">
        <?= htmlspecialchars($_GET["error"]) ?>
    </span>
<?php endif; ?>

    <section class="table-responsive w-100 m-0">
      <table class="tabla-contenedor m-0" id="tablaPrestamos">
        <thead>
          <tr>
            <th>Id</th>
            <th>Prestador</th>
            <th>Prestado</th>
            <th>Equipo Prestado</th>
            <th>Fecha fin</th>
            <th>Devolución</th>
            <th>Finalizar el prestasmo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($prestamos)): ?>
            <tr>
              <td colspan="7" class="text-center py-4 text-muted text-bold"> No se encontraron préstamos. </td>
            </tr>
          <?php else: ?>

            <?php foreach ($prestamos as $prestamo): ?>
              <tr>
                <td><?= htmlspecialchars($prestamo["id"] ?? "N/A") ?></td>
                <td><?= htmlspecialchars($prestamo["nombreTecnico"] . " - (" . $prestamo["ciTecnico"] . ")") ?></td>
                <td><?= htmlspecialchars($prestamo["nombrePrestado"] . " - (" . $prestamo["ciPrestado"] . ")") ?></td>
                <td><?= htmlspecialchars($prestamo["idEquipo"]) ?></td>
                <td><?= htmlspecialchars($prestamo["fechaFin"] . " - " . $prestamo["horaFin"]) ?? "N/A" ?></td>
                <td><?= htmlspecialchars($prestamo["devuelto"]) ? "Devuelto" : "Pendiente" ?></td>
                <td>
                  <?php if (htmlspecialchars(!$prestamo["devuelto"])): ?>
                    <form method="POST" action="../../../app/controlador/prestamos/procesarEstadoPrestamo.php" class="d-inline form-estado">
                      <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($prestamo["id"]) ?>">
                      <button type="submit" class="btn btn-primary ms-1 text-bold">
                        Finalizar
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>

        </tbody>
      </table>
    </section>

    <section class="contenedor-registrar d-flex justify-content-end px-3 mt-4">
      <button id="btnRegistrarPrestamo" class="btn btn-success mb-4" type="button">Registrar préstamo</button>
    </section>

    <div id="modalPrestamo"
      class="modal-incidencia d-none fixed-top w-100 h-100 justify-content-center align-items-center"
      style="z-index: 1050;">
      <div class="modal-contenido bg-white p-4 rounded shadow w-100">

        <form id="formPrestamo" method="POST" class="form" action=../../../app/controlador/prestamos/procesarAltaPrestamo.php>
          <h2 class="text-primary border-bottom pb- 3">Registro de prestamo</h2>

          <fieldset>
            <label for="ciPrestado" class="form-label fw-semibold mt-2">CI del solicitante:</label>
            <input type="text" id="ciPrestado" class="form-control" name="ciPrestado" placeholder="ej: 1234578"
              required maxlength="8" minlength="8" inputmode="numeric">

            <label for="nombrePrestado" class="form-label fw-semibold mt-2">Nombre del solicitante:</label>
            <input type="text" id="nombrePrestado" class="form-control" name="nombrePrestado"
              placeholder="ej: Juan Pérez" required>

            <input type="hidden" name="csrfToken"
              value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">

            <label for="idEquipo" class="form-label fw-semibold mt-3">Dispositivo</label>
            <select name="idEquipo" id="listaDispositivos" class="form-select" required>
              <option value="">Elegir equipo</option>
              <!-- TOFIX: AÑADIR UN POST PARA QUE EL CARGADOR DE EQUIPOS SEPA QUE SON PARA LAS OPCIONES DE PRESTAMO, ASI SOLO DEVUELVA EQUIPOS ACTIVOS, SIN INCIDENCIAS ACTIVAS, QUE NO ESTEN PRESTADOS -->
              <?php foreach ($equipos as $equipo): ?>
                <option value="<?= htmlspecialchars($equipo["idEquipo"]) ?>">
                  <?= htmlspecialchars("PC: " . $equipo["idEquipo"]) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="final" class="form-label fw-semibold mt-3">Cuando sera devuelto el dispositivo</label>
            <input type="datetime-local" id="final" name="final" class="form-control mb-3" required>
          </fieldset>

          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-success">Registrar prestamo</button>
            <button type="button" id="btnCancelarPrestamo" class="btn btn-danger">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
  </footer>

  <script src="../../../public/assets/js/btnMenuCelular.js"></script>
  <script src="../../../public/assets/js/gestionPrestamos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/verificarSesion.js"></script>
  <script src="../../../public/assets/js/cerrarSesion.js"></script>



</body>

</html>