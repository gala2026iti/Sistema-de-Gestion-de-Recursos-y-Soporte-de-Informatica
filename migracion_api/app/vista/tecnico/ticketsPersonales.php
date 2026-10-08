<!DOCTYPE html>
<html lang="es">
  <?php var_dump($tickets);?>
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
                <ul class="nav-opciones-sistema">
                    <li class="desplegable desplegable-derecha" id="menuUsuario" data-rol-actual="<?= htmlspecialchars($_SESSION['rolActual'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <a href="#"><?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?php switch ($_SESSION['rolActual'] ?? ''): case 'administrador': ?> Administrador<?php break; case 'tecnico': ?> Técnico<?php break; case 'docente': ?> Docente<?php endswitch; ?> 🡻</a>
                      <ul class="desplegable-menu">
                        <?php if (($_SESSION['rolActual'] ?? '') !== 'docente' && !empty($_SESSION['docente'])): ?><li><form action="../../../app/controlador/procesarCambioRol.php" method="post"><button type="submit" class="cambiar-rol">Cambiar a Docente</button><input type="hidden" name="rol" value="docente"><input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></form></li><?php endif; ?>
                        <?php if (($_SESSION['rolActual'] ?? '') !== 'tecnico' && !empty($_SESSION['tecnico'])): ?><li><form action="../../../app/controlador/procesarCambioRol.php" method="post"><button type="submit" class="cambiar-rol">Cambiar a Técnico</button><input type="hidden" name="rol" value="tecnico"><input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></form></li><?php endif; ?>
                        <?php if (($_SESSION['rolActual'] ?? '') !== 'administrador' && !empty($_SESSION['administrador'])): ?><li><form action="../../../app/controlador/procesarCambioRol.php" method="post"><button type="submit" class="cambiar-rol">Cambiar a Administrador</button><input type="hidden" name="rol" value="administrador"><input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></form></li><?php endif; ?>
                        <li><a href="../../../public/paginaWeb/cerrarSesion.php" id="cerrarSesion">Cerrar Sesion</a></li>
                      </ul>
                    </li>
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
                        <li><a href="../tecnico/tablaPrestamos.php">Tabla de prestamos</a></li>
                </li>
                <li><a href="../tecnico/gestionSolicitudes.php">Gestion de solicitudes</a></li>
                                <li><a href="../admin_tecnico/gestionInventarioTecnologico.php">Inventario de equipos</a></li>

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
          <?php foreach ($tickets as $ticket): ?>
            <?php if($ticket["estado"] === "pendiente"): ?>
              <tr><td class="ticket-marcado"><a href="detalleTicket.php?id=<?=$ticket["id"] ?>" class="text-decoration-none text-dark d-block w-100 h-100 py-2"><?=$ticket["asunto"]?></a></td></tr>
            <?php endif; ?>
          <?php endforeach; ?>  
            </tbody>
        </table>

        <table class="kanban-columna pb-3 text-center w-100" id="tablaEnProceso">
          <thead>
            <tr>
              <th class="bg-warning text-white py-3 fs-5 rounded-top">En progreso</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($tickets as $ticket): ?>
            <?php if($ticket["estado"] === "en proceso"): ?>
              <tr><td class="ticket-marcado"><a href="detalleTicket.php?id=<?=$ticket["id"] ?>" class="text-decoration-none text-dark d-block w-100 h-100 py-2"><?=$ticket["asunto"]?></a></td></tr>
            <?php endif; ?>
          <?php endforeach; ?>  
            </tbody>
        </table>

        <table class="kanban-columna pb-3 text-center w-100" id="tablaResuelto">
          <thead>
            <tr>
              <th class="bg-success text-white py-3 fs-5 rounded-top">Cerrado / Resuelto</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($tickets as $ticket): ?>
            <?php if($ticket["estado"] === "resuelto"): ?>
              <tr><td class="ticket-marcado"><a href="detalleTicket.php?id=<?=$ticket["id"] ?>" class="text-decoration-none text-dark d-block w-100 h-100 py-2"><?=$ticket["asunto"]?></a></td></tr>
            <?php endif; ?>
          <?php endforeach; ?>  
            </tbody>
        </table>

    </section>
  </main>

  <footer>
    <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto Tecnológico de Informática</span>
  </footer>

  <script src="../../../public/assets/js/btnMenuCelular.js"></script>
  <script src="../../../public/assets/js/cerrarSesion.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/verificarSesion.js"></script>

</body>
 
</html>