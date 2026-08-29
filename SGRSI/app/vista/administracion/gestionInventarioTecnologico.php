<!-- TOFIX : MOVER PHP A PROCESARCARGAREQUIPOS.PHP, ADEMAS DE AÑADIRLE TRIM Y HTMLSPECIALCHARS -->
<?php 
$estado = trim($_GET["estado"] ?? "");
$orden = trim($_GET["orden"] ?? "");

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario Tecnológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../../public/assets/css/administracion-tecnico/gestionEquipos.css">
    <link rel="stylesheet" href="../../../public/assets/css/formulariospopup.css">
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="img-logo" src="../../../public/assets/img/logo_iti.png" alt="Logo">
    </header>

<!-- TOFIX : CORRECCIÓN EN BARRAS DE NAVEGACIÓN, SE ADAPTARON AL NUEVO SISTEMA DE BUSQUEDA MEDIANTE GET -->    

    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
<ul class="nav-menu">
<li><a href="../../../public/paginaWeb/cerrarSesion.php" method="get" id="cerrarSesion">Cerrar Sesion</a></li>
</ul>
            </section>
            <ul class="nav-menu">
                <li class="desplegable"><a href="../homeAdmin.php">Dashboard</a></li>
                <li class="desplegable"><a href="estadoEquipos.php">Estado de equipos</a></li>
                <li class="desplegable"><a href="reportes.php">Reportes y estadisticas</a></li>
                <li><a href="metricas.php">Metricas del sistema</a></li>
            </ul>
            <ul class="nav-menu">
                <li class="desplegable-padding" id="opcionesAdmin">
                    <a href="#">Administracion y control 🡻</a>
                    <ul class="desplegable-menu">
                        <li><a href="gestionUsuarios.php">Gestion de usuarios</a></li>
                    </ul>
                </li>
            </ul>
        </section>
    </nav>

    <main class="container-fluid px-0 py-3">
        <section class="px-4 mb-3">
            <button id="btnAbrirUbicaciones" class="btn btn-primary d-block mb-2">☰ Ver Ubicaciones / Salones</button>
        </section>

        <section class="barralateral-ubicaciones" id="barraLateralSalones">
            <div class="barralateral-header">
                <h3>Ubicaciones</h3>
                <button id="btnCerrarUbicaciones" class="btn-cerrar-lateral">X</button>
            </div>
            <div class="barralateral-contenido">
                <h4>Laboratorios</h4>
                <ul id="listaLaboratorios">
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                        <?php if($ubicacion["tipo"] === "laboratorio"): ?>
                            <li><a href="gestionInventarioTecnologico.php?tipoUbicacion=laboratorio&ubicacion=<?= $ubicacion['id'] ?>"><?= ucfirst($ubicacion['tipo']) . " " . $ubicacion['id'] ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button class="btn-agregar-salon" id="btnAgregarL">+ Añadir Laboratorio</button>

                <h4>Talleres</h4>
                <ul id="listaTalleres">
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                        <?php if($ubicacion["tipo"] === "taller"): ?>
                            <li><a href="gestionInventarioTecnologico.php?tipoUbicacion=taller&ubicacion=<?= $ubicacion['id'] ?>"><?= ucfirst($ubicacion['tipo']) . " " . $ubicacion['id'] ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button class="btn-agregar-salon" id="btnAgregarT">+ Añadir Taller</button>

                <h4>Otros</h4>
                <ul>
                    <li><a href="gestionInventarioTecnologico.php?tipoUbicacion=prestamo" class="filtro-ubicacion-directa" salones-ubicacion="prestamo">Dispositivos para
                            prestar</a></li>
                    <li><a href="gestionInventarioTecnologico.php" class="filtro-ubicacion-directa" salones-ubicacion="todos">Todos los
                            Dispositivos</a>
                    </li>
                </ul>
            </div>
        </section>

        <h2 class="centro mt-3 text-warning">Gestion de Equipos Tecnologicos</h2>
        <span class="centro mb-4">A continuacion se muestran los equipos registrados en el sistema</span>

                <section class="filtros">
            <form method="GET" action="gestionInventarioTecnologico.php">
                <label for="estado">Estado:</label>

                <select id="estado" name="estado">
                    <option value=""> Todos </option>
                    <option value="activo" <?= ($estado === "activo") ? "selected" : "" ?>> Activo
                    </option>
                    <option value="inactivo" <?= ($estado === "inactivo") ? "selected" : "" ?>> Inactivo
                    </option>
                </select>

                <label for="orden"> Ordenar por: </label>
                <select id="orden" name="orden">
                    <option value="" selected> ID </option>
                <!-- FIX: SE EVITA USAR TIPADO CAMELCASE DEBIDO A QUE SE CONVIERTE TODO A MINUSCULAS AL SER ENVIADO POR GET -->

                    <option value="masincidencias" <?= ($orden === "masincidencias") ? "selected" : "" ?>> Incidencias (Más) </option>
                    <option value="menosincidencias" <?= ($orden === "menosincidencias") ? "selected" : "" ?>> Incidencias (Menos)</option>
                    <option value="reciente" <?= ($orden === "reciente") ? "selected" : "" ?>> Intervenciones (Recientes)</option>
                    <option value="antiguo" <?= ($orden === "antiguo") ? "selected" : "" ?>> Intervenciones (Antiguas)</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    Filtrar
                </button>
            </form>
        </section>

        <section class="table-responsive w-100 m-0 overflow-x-auto">

        <!-- TOFIX : INSERTAR COLUMNA "ULTIMA INCIDENCIA" FALTANTE, ADEMÁS DE INCLUIR SU OBTENCION EN LOS TD -->

            <table class="tabla-contenedor m-0" id="tablaEquipos">
                <thead>
                    <tr>
                        <th>Codigo (ID Global)</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Incidencias</th>
                        <th>Última Intervención</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php foreach ($equipos as $equipo) { ?>
                        <tr>
                            <td><?= $equipo['idEquipo'] ?></td>
                            <?php if ($equipo['tipoUbicacion']) { ?>
                            <?php if($equipo['tipoUbicacion'] === "prestamo") { ?>
                                <td>Equipo de Préstamo</td>
                            <?php } else { ?>
                                <td><?= ucfirst($equipo['tipoUbicacion']) . " " . $equipo['idUbicacion'] . " (PC-" . $equipo["posicion"] . ")" ?></td>
                            <?php } ?>
                            <?php } else { ?>
                                <td>Sin ubicación</td>
                            <?php } ?>
                            <td><?= $equipo['activo'] ? "Activo" : "Inactivo" ?></td>
                            <td><?= $equipo['totalIncidencias'] ?></td>
                            <?php if($equipo['ultimaIntervencion']) { ?>
                                <td><?= $equipo['ultimaIntervencion'] ?></td>
                            <?php } else { ?>
                                <td>Sin intervenciones previas</td>
                            <!-- TOFIX: AGREGAR LOS BOTONES CORRESPONDIENTES AL ÁREA DE ACCIONES: EDITAR, REMOVER DEL SALON, DESACTIVAR, VER INCIDENCIAS -->
                            <?php } ?>
                            <td> <a href="paginaNoExistente.php?id=<?= $equipo['idEquipo'] ?>" class="btn btn-info btn-sm">Botón Programable</a> </td>
                        </tr> 
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="w-100 d-flex px-3 mt-4">
            <a href="../admin_tecnico/historialGeneral.php?tipo=equipos" id="btnHistorialEquipos" class="btn btn-warning me-2">Historial
                de Equipos</a>
            <a href="../admin_tecnico/historialGeneral.php?tipo=salones" id="btnHistorialEquipos" class="btn btn-warning me-2">Historial
                de Salones</a>
            <button id="btnRegistrarEquipo" class="btn btn-success ms-auto" type="button">Registrar PC</button>
        </section>

        <div id="modalEquipo"
            class="modal-incidencia d-none fixed-top w-100 h-100 justify-content-center align-items-center">

            <form id="formEquipo" class="bg-white p-4 rounded shadow w-100" style="max-width: 450px;">
                <h2 class="text-primary border-bottom pb-3">Registro de PC</h2>

                <fieldset class="tarjeta">
                    <label class="form-label fw-semibold mb-2" for="idPC">
                        Código de identificación:
                    </label>
                    <input class="form-control mb-3" type="text" inputmode="numeric" id="idPC" placeholder="ej: 123456"
                        maxlength="6" required>

                    <label class="form-label fw-semibold mb-2" for="ubicacion">Ubicacion del Dispositivo</label>
                    <select id="ubicacion" class="form-select mb-3" required>
                        <option value="">Elegir ubicacion</option>
                        <optgroup label="Laboratorios" id="grupoLaboratorio"></optgroup>
                        <optgroup label="Talleres" id="grupoTalleres"></optgroup>
                        <optgroup label="Otros">
                            <option value="prestamo">Dispositivo de prestamo</option>
                            <option value="ninguna">Sin ubicacion</option>
                        </optgroup>
                    </select>

                    <div id="contenedorLugar" class="d-none">
                        <label class="form-label fw-semibold mb-2" for="posicionPC">
                            Lugar de la PC:
                        </label>
                        <input class="form-control mb-3" type="number" id="posicionPC" min="1" placeholder="ej: 5">
                    </div>
                </fieldset>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-success" id="btnGuardarEquipo">Guardar PC</button>
                    <button type="button" id="btnCancelarEquipo" class="btn btn-danger">Volver</button>
                </div>
            </form>

        </div>
    </main>

    <footer><span class="footer-bold">Copyright 2026 - S.G.R.S.I - Instituto tecnologico de Informática</span></footer>
    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>
    <script src="../../../public/assets/js/gestionEquipos.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>


</body>

</html>