<?php
$estado = trim($_GET["estado"] ?? "");
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Solicitudes - Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/formulariospopup.css">
  <link rel="stylesheet" href="../../assets/css/global.css">
</head>

<body>
  <header class="d-flex justify-content-center align-items-center py-4">
    <img class="img-logo" src="../../assets/img/logo_iti.png" alt="Logo">
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
        <li class="desplegable-padding">
          <a href="#">Gestion de prestamos 🡻</a>
          <ul class="desplegable-menu">
            <li><a href="tablaPrestamos.php">Tabla de prestamos</a></li>
            <li><a href="inventarioEquipos.php">Inventario de equipos</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </nav>

  <main class="container-fluid px-0 py-3">
    <a href="../admin_tecnico/historialGeneral.php?tipo=solicitudes" class="btn btn-primary mx-3">Historial de solicitudes</a>
    <h2 class="centro mt-3 text-primary">Solicitudes registradas</h2>
    <span class="centro mb-4">A continuación se muestran las solicitudes del personal docente</span>

    <section class="filtros">
            <form method="GET" action="gestionSolicitudes.php">
<!-- TOFIX: IMPLEMENTAR POSIBLE BUSQUEDA O POR ID, O POR ASUNTO -->
                <label for="estado">Filtrar por Estado:</label>

                <select id="estado" name="estado">
                    <option value=""> Todos </option>
                    <option value="pendiente" <?= ($estado === "pendiente") ? "selected" : "" ?>> Pendiente
                    </option>
                    <option value="finalizada" <?= ($estado === "finalizada") ? "selected" : "" ?>> Finalizada
                    </option>
                </select>

                <button type="submit" class="btn btn-primary text-bold">
                    Filtrar
                </button>
            </form>
        </section>

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


    <section class="table-responsive w-100 m-0 pb-3">
      <table class="ajustar-td tabla-contenedor m-0" id="tablaSolicitudes">
        <thead>
          <tr>
            <th>ID</th>
            <th>Asunto</th>
            <th>Fecha límite</th>
            <th>Descripción</th>
            <th>Ingresada Por</th>
            <th>Finalización</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($solicitudes)): ?>
            <tr>
              <td colspan="7" class="text-center py-4 text-muted text-bold"> No se encontraron solicitudes. </td>
            </tr>
          <?php else: ?>
            <?php foreach ($solicitudes as $solicitud): ?>
              <tr>
                <td><?= htmlspecialchars($solicitud["id"]); ?></td>
                <td><?= htmlspecialchars($solicitud["asunto"]); ?></td>
                <td><?= htmlspecialchars($solicitud["fechaLimite"] . " - " . $solicitud['horaLimite']); ?></td>
                <td><?= htmlspecialchars($solicitud["descripcion"]); ?></td>
                <td><?= htmlspecialchars($solicitud["nombre"] . " - ( " . $solicitud['ciDocente'] . " )"); ?></td>
                <td><?= htmlspecialchars($solicitud["finalizada"] ? "Finalizada" : "Pendiente"); ?></td>
                <td>
                  <?php if (!htmlspecialchars($solicitud["finalizada"])): ?>
                    <form method="POST" action="../../../app/controlador/solicitudes/procesarEstadoSolicitud.php"
                      class="d-inline form-estado">
                      <input type="hidden" name="csrfToken"
                        value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($solicitud["id"]) ?>">
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
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnológico de Informática</span>
  </footer>

  <script src="../../../public/assets/js/btnMenuCelular.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/verificarSesion.js"></script>
  <script src="../../../public/assets/js/cerrarSesion.js"></script>
  <script src="../../../public/assets/js/gestionSolicitudes.js"></script>


</body>

</html>