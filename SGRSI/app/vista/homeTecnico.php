<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tickets registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/administracion-tecnico/ticketsRegistrados.css">
</head>

<body>
  <header class="d-flex justify-content-center align-items-center py-4">
    <img class="img-logo" src="../assets/img/logo_iti.png" alt="Logo">
  </header>

  <nav class="navbarSGRSI">
    <section class="nav-container">
      <section class="nav-primera-fila">
        <button class="btn-menu" id="btnMenu">☰</button>
        <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
        <ul class="nav-opciones-sistema">
          <li class="desplegable desplegable-derecha" id="menuUsuario" data-rol-actual="<?= htmlspecialchars($_SESSION['rolActual'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <a href="#">
              <?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?> -
              <?php switch ($_SESSION['rolActual'] ?? ''):
                case 'administrador': ?> Administrador
                <?php break;
                case 'tecnico': ?> Técnico
                <?php break;
                case 'docente': ?> Docente
              <?php endswitch; ?> 🡻
            </a>
            <ul class="desplegable-menu">
              <?php if (($_SESSION['rolActual'] ?? '') !== 'docente' && !empty($_SESSION['docente'])): ?>
                <li>
                  <form action="../../app/controlador/procesarCambioRol.php" method="post">
                    <button type="submit" class="cambiar-rol">Cambiar a Docente</button>
                    <input type="hidden" name="rol" value="docente">
                    <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  </form>
                </li>
              <?php endif; ?>
              <?php if (($_SESSION['rolActual'] ?? '') !== 'tecnico' && !empty($_SESSION['tecnico'])): ?>
                <li>
                  <form action="../../app/controlador/procesarCambioRol.php" method="post">
                    <button type="submit" class="cambiar-rol">Cambiar a Técnico</button>
                    <input type="hidden" name="rol" value="tecnico">
                    <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  </form>
                </li>
              <?php endif; ?>
              <?php if (($_SESSION['rolActual'] ?? '') !== 'administrador' && !empty($_SESSION['administrador'])): ?>
                <li>
                  <form action="../../app/controlador/procesarCambioRol.php" method="post">
                    <button type="submit" class="cambiar-rol">Cambiar a Administrador</button>
                    <input type="hidden" name="rol" value="administrador">
                    <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  </form>
                </li>
              <?php endif; ?>
              <li><a href="../../public/paginaWeb/cerrarSesion.php" id="cerrarSesion">Cerrar Sesion</a></li>
            </ul>
          </li>
        </ul>
      </section>

      <ul class="nav-menu">
        <li class="desplegable">
          <a href="#">Gestión de tickets 🡻 </a>
          <ul class="desplegable-menu">
            <li><a href="tecnico/ticketsPersonales.php">Tickets asignados</a></li>
          </ul>
        </li>
        <li><a href="tecnico/tablaPrestamos.php">Tabla de prestamos</a></li>
        <li><a href="tecnico/gestionSolicitudes.php">Gestion de solicitudes</a></li>
        <li><a href="admin_tecnico/gestionInventarioTecnologico.php">Inventario de equipos</a></li>

      </ul>
    </section>
  </nav>

  <main class="container-fluid px-0 py-3">
    <a href="admin_tecnico/historialGeneral.php?tipo=tickets" class="btn btn-primary ms-3">Historial de tickets</a>
    <h2 class="centro mt-3 text-primary">Tickets Registrados</h2>
    <span class="centro mb-4">A continuación se muestran los tickets</span>

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

    <section class="filtros">
      <form method="GET" action="homeTecnico.php">

        <label for="orden">Orden:</label>

        <select id="orden" name="orden">
          <option value="reciente" <?= ($orden === "reciente") ? "selected" : "" ?>> Recientes Primero
          </option>
          <option value="antiguo" <?= ($orden === "antiguo") ? "selected" : "" ?>> Antiguos Primero
          </option>
        </select>

        <label for="gravedad">Filtrar por Gravedad:</label>

        <select id="gravedad" name="gravedad">
          <option value=""> Todos </option>
          <option value="ligera" <?= ($gravedad === "ligera") ? "selected" : "" ?>> Ligera
          </option>
          <option value="media" <?= ($gravedad === "media") ? "selected" : "" ?>> Media
          </option>
          <option value="grave" <?= ($gravedad === "grave") ? "selected" : "" ?>> Grave
          </option>
        </select>

        <label for="tipo">Filtrar por Tipo:</label>

        <select id="tipo" name="tipo">
          <option value=""> Todos </option>
          <option value="hardware" <?= ($tipo === "hardware") ? "selected" : "" ?>> Hardware
          </option>
          <option value="software" <?= ($tipo === "software") ? "selected" : "" ?>> Software
          </option>
          <option value="red" <?= ($tipo === "red") ? "selected" : "" ?>> Red
          </option>
        </select>

        <label for="estado">Filtrar por Estado:</label>

        <select id="estado" name="estado">
          <option value=""> Todos </option>
          <option value="pendiente" <?= ($estado === "pendiente") ? "selected" : "" ?>> Pendiente
          </option>
          <option value="en proceso" <?= ($estado === "en proceso") ? "selected" : "" ?>> En proceso
          </option>
          <option value="resuelto" <?= ($estado === "resuelto") ? "selected" : "" ?>> Resuelto
          </option>
        </select>

        <label for="id">Filtrar por ID:</label>
        <input type="text" id="id" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">

        <button type="submit" class="btn btn-primary text-bold">
          Filtrar
        </button>
      </form>
    </section>

    <section class="table-responsive w-100 m-0 pb-5">
      <table id="tablaEquipos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Asunto</th>
            <th>Tipo</th>
            <th>Gravedad</th>
            <th>Estado</th>
            <th>Información de creacion</th>
            <th>Asignación de ticket</th>
          </tr>
        </thead>
        <tbody>
           <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted text-bold"> No se encontraron tickets. </td>
                        </tr>
                    <?php else: ?>
          <?php foreach ($tickets as $ticket) : ?>
            <tr>
              <td><?= $ticket["id"] ?></td>
              <td><?= $ticket["asunto"] ?></td>
              <td><?= $ticket["tipo"] ?></td>
              <td><?= $ticket["gravedad"] ?></td>
              <td><?= $ticket["estado"] ?></td>
              <td><?= $ticket["fechaCreacion"] . " - " . $ticket["horaCreacion"]  ?></td>
              <td>
                <?php if ($ticket["esColaborador"]): ?>
                  <button class="btn btn-danger"> Desasignarse </button>
                <?php else: ?>
                  <button class="btn btn-success"> Asignarse </button>
                <?php endif; ?>
              </td>
            </tr>

          <?php endforeach; ?>
          <?php endif;?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
  </footer>

  <script src="../assets/js/btnMenuCelular.js"></script>
  <script src="../assets/js/cerrarSesion.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/verificarSesion.js"></script>
  <script src="../assets/js/ticketsRegistrados.js"></script>
</body>

</html>