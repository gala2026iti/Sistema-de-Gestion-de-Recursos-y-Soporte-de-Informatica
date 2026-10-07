<?php $existe = false ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Incidencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/formularioTickets.css">
    <link rel="stylesheet" href="../assets/css/formulariospopup.css">
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
                <li><a href="docente/pagsolicitudes.php" id="btnServicios">Solicitud de servicios</a></li>
            </ul>
        </section>
    </nav>

    <main class="main-formulario d-flex justify-content-center align-items-center py-5 px-3">
        <section class="w-100 container-formulario">
            <h2 class="mb-4 fw-bold text-center texto-azul">Registro de incidencias</h2>

            <form class="p-4 shadow border-0 bg-Formulario rounded-4" id="formIncidencia" action="../../app/controlador/tickets/procesarAltaTicket.php" method="POST">

                <div class="campo mb-4">
                    <label class="form-label fw-semibold texto-azul-dark">Seleccione taller o laboratorio</label>
                    <select id="ubicacionSalon" name="ubicacionSalon" class="form-select form-select-lg" required>
                        <option value="">Seleccione una opción</option>
                        <optgroup label="Laboratorios" id="grupoLaboratorio"></optgroup>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                            <?php if (strtolower((string)$ubicacion['tipo']) === 'laboratorio'): ?>
                                <option class="opcion-laboratorio" value="<?= $ubicacion['id'] ?>"
                                    <?php if ((string)$ubicacion['id'] === (string)($_GET['ubicacion'] ?? '') && strtolower((string)$ubicacion['tipo']) === strtolower((string)($_GET['tipo'] ?? ''))): ?>
                                    selected
                                    <?php $existe = true; ?>
                                    <?php endif; ?>><?= ucfirst($ubicacion['tipo']) . " " . $ubicacion['id'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <optgroup label="Talleres" id="grupoTalleres"></optgroup>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                            <?php if (strtolower((string)$ubicacion['tipo']) === 'taller'): ?>
                                <option class="opcion-taller" value="<?= $ubicacion['id'] ?>"
                                    <?php if ((string)$ubicacion['id'] === (string)($_GET['ubicacion'] ?? '') && strtolower((string)$ubicacion['tipo']) === strtolower((string)($_GET['tipo'] ?? ''))): ?>
                                    selected
                                    <?php $existe = true; ?>
                                    <?php endif; ?>><?= ucfirst($ubicacion['tipo']) . " " . $ubicacion['id'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="contenedorEquipos">
                    <?php if ($existe && !empty($_GET['ubicacion']) && !empty($_GET['tipo'])): ?>
                        <?php foreach ($equipos as $equipo): ?>
                            <div class="espacioEquipo-body d-flex flex-column justify-content-between p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h5 mb-0 fw-bold text-secondary">PC: <?= $equipo['posicion'] ?> (ID: <?= $equipo['idEquipo'] ?>)</h4>
                                </div>
                                <div class="bg-light p-2 rounded-3 d-flex justify-content-around">
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="estado-<?= $equipo['idEquipo'] ?>" id="ok-<?= $equipo['idEquipo'] ?>" value="ok">
                                        <label class="form-check-label text-success fw-semibold" for="ok-<?= $equipo['idEquipo'] ?>">Sin problemas</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="estado-<?= $equipo['idEquipo'] ?>" id="inc-<?= $equipo['idEquipo'] ?>" value="incidencia">
                                        <label class="form-check-label text-danger fw-semibold" for="inc-<?= $equipo['idEquipo'] ?>">Hay incidencia</label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                                <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"]) ?>">

                <button  type="submit" class="btn btn-warning w-100 py-3 mt-3 fw-bold text-dark fs-5 shadow-sm">
                    Registrar estado del salón
                </button>
            </form>
        </section>

        <dialog id="incidencia" class="modal-contenido rounded shadow p-4">
            <form class="from">

                <h3 id="titulo" class="h5 fw-bold texto-azul mb-3">Registro de incidencia</h3>

                <div class="mt-3 pt-3 border-top">
                    <label for="tipo" class="form-label fw-semibold texto-azul-dark">Tipo de incidencia</label>
                    <select id="tipo" name="tipo02" class="form-select mb-3">
                        <option value="">Seleccione</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Red">Red</option>
                    </select>

                    <label for="asunto" class="form-label fw-semibold texto-azul-dark">Asunto:</label>
                    <input type="text" id="asunto" class="form-control mb-3" placeholder="ej: Pantalla de monitor rosa">

                    <label for="persona" class="form-label fw-semibold texto-azul-dark">Persona que estaba haciendo uso
                        de la PC</label>
                    <input type="text" id="persona" class="form-control mb-3" placeholder="ej: Maria Jose Martinez">

                    <label class="form-label fw-semibold texto-azul-dark d-block mb-2">Gravedad de la incidencia</label>
                    <div class="grupo-radios d-flex flex-wrap gap-4 mb-3">
                        <div class="opcion-radio d-flex align-items-center gap-2">
                            <input type="radio" id="ligera" name="gravedad" value="ligera" class="form-check-input">
                            <label for="ligera" class="form-check-label text-success">Ligera</label>
                        </div>
                        <div class="opcion-radio d-flex align-items-center gap-2">
                            <input type="radio" id="media" name="gravedad" value="media" class="form-check-input">
                            <label for="media" class="form-check-label text-warning">Media</label>
                        </div>
                        <div class="opcion-radio d-flex align-items-center gap-2">
                            <input type="radio" id="grave" name="gravedad" value="grave" class="form-check-input">
                            <label for="grave" class="form-check-label text-danger">Grave</label>
                        </div>
                    </div>

                    <label for="descripcion" class="form-label fw-semibold texto-azul-dark">Descripción</label>
                    <textarea id="descripcion" name="descripcion02" class="form-control mb-3"
                        placeholder="Información más detallada si así lo precisa" rows="3" maxlength="300"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" id="btnAceptar" class="btn btn-success">Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
                </div>

            </form>
        </dialog>
    </main>

    <footer>
        <span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnológico de Informática</span>
    </footer>

    <script src="../assets/js/registroIncidencias.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/btnMenuCelular.js"></script>
    <script src="../assets/js/verificarSesion.js"></script>
    <script src="../assets/js/cerrarSesion.js"></script>
</body>

</html>