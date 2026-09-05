<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../../public/assets/css/formulariospopup.css">
</head>

<body>

    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="img-logo" src="../../../public/assets/img/logo_iti.png" alt="Logo">
    </header>
    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">
                    ☰
                </button>
                <button class="btn-cerrar-lateral" id="btnCerrar">
                    X
                </button>
                <ul class="nav-menu">
                    <li>
                        <a href="../../../public/paginaWeb/cerrarSesion.php" id="cerrarSesion">
                            Cerrar Sesion
                        </a>
                    </li>
                </ul>
            </section>
            <ul class="nav-menu">
                <li class="desplegable">
                    <a href="../homeAdmin.php">
                        Dashboard
                    </a>
                </li>
                <li class="desplegable">
                    <a href="estadoEquipos.php">
                        Estado de equipos
                    </a>
                </li>

                <li class="desplegable">
                    <a href="reportes.php">
                        Reportes y estadisticas
                    </a>
                </li>

                <li>
                    <a href="metricas.php">
                        Metricas del sistema
                    </a>
                </li>

            </ul>
            <ul class="nav-menu">

                <li class="desplegable-padding">

                    <a href="#">
                        Administracion y control 🡻
                    </a>

                    <ul class="desplegable-menu">

                        <li>
                            <a href="gestionInventarioTecnologico.php">
                                Gestion de inventario de equipos
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
    </nav>
    <main class="container-fluid px-0 py-3">
        <a href="../admin_tecnico/historialGeneral.php?tipo=usuarios" class="btn btn-primary mx-3 text-bold">
            Historial de cambios
        </a>
        <h2 class="centro mt-3 text-warning">
            Gestion de usuarios
        </h2>
        <span class="centro mb-4">
            A continuacion se muestran los usuarios registrados en el sistema
        </span>

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
            <form method="GET" action="gestionUsuarios.php">
                <label for="rol">Rol:</label>

                <select id="rol" name="rol">
                    <option value=""> Todos </option>
                    <option value="administrador" <?= ($rol === "administrador") ? "selected" : "" ?>> Administrador
                    </option>
                    <option value="tecnico" <?= ($rol === "tecnico") ? "selected" : "" ?>>Técnico </option>
                    <option value="docente" <?= ($rol === "docente") ? "selected" : "" ?>> Docente </option>
                </select>

                <label for="estado"> Estado: </label>
                <select id="estado" name="estado">
                    <option value="">Todos </option>
                    <option value="activo" <?= ($estado === "activo") ? "selected" : "" ?>> Activo </option>
                    <option value="inactivo" <?= ($estado === "inactivo") ? "selected" : "" ?>> De baja</option>
                </select>

                <button type="submit" class="btn btn-primary text-bold">
                    Filtrar
                </button>
            </form>
        </section>

        <section class="table-responsive w-100 m-0">
            <table class="tabla-contenedor m-0 text-bold" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>Cedula/Usuario</th>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted text-bold"> No se encontraron usuarios. </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <?php
                            $roles = [];
                            if ($usuario["administrador"]) {
                                $roles[] = "Administrador";
                            }
                            if ($usuario["tecnico"]) {
                                $roles[] = "Técnico";
                            }
                            if ($usuario["docente"]) {
                                $roles[] = "Docente";
                            }
                            $rolesTexto = empty($roles)
                                ? "Sin rol"
                                : implode(", ", $roles);
                            ?>
                            <tr>
                                <td> <?= htmlspecialchars($usuario["cedula"]) ?> </td>
                                <td> <?= htmlspecialchars($usuario["nombre"]) ?> </td>
                                <td> <?= htmlspecialchars($usuario["correo"]) ?> </td>
                                <td> <?= htmlspecialchars($rolesTexto) ?> </td>
                                <td>
                                    <?php if ($usuario["activo"]): ?> Activo
                                    <?php else: ?>
                                        De baja
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btnModificarUsuario text-bold"
                                        data-cedula="<?= htmlspecialchars($usuario["cedula"]) ?>"
                                        data-nombre="<?= htmlspecialchars($usuario["nombre"]) ?>"
                                        data-correo="<?= htmlspecialchars($usuario["correo"]) ?>"
                                        data-administrador="<?= $usuario["administrador"] ? "1" : "0" ?>"
                                        data-tecnico="<?= $usuario["tecnico"] ? "1" : "0" ?>"
                                        data-docente="<?= $usuario["docente"] ? "1" : "0" ?>">
                                        Modificar
                                    </button>
                                    <?php if ($usuario["activo"]): ?>
                                        <form method="POST" action="../../../app/controlador/usuarios/procesarEstadoUsuario.php"
                                            class="d-inline form-estado">
                                            <input type="hidden" name="csrfToken"
                                                value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">

                                            <input type="hidden" name="cedula" value="<?= htmlspecialchars($usuario["cedula"]) ?>">
                                            <input type="hidden" name="accion" value="desactivar">
                                            <button type="submit" class="btn btn-danger ms-1 text-bold">
                                                Desactivar
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        <form method="POST" action="../../../app/controlador/usuarios/procesarEstadoUsuario.php"
                                            class="d-inline form-estado">
                                            <input type="hidden" name="csrfToken"
                                                value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">

                                            <input type="hidden" name="cedula" value="<?= htmlspecialchars($usuario["cedula"]) ?>">
                                            <input type="hidden" name="accion" value="activar">
                                            <button type="submit" class="btn btn-success ms-1 text-bold">
                                                Activar
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
        <section class="d-flex justify-content-end px-3 mt-4">
            <button id="btnRegistrarUsuario" class="btn btn-success text-bold" type="button">
                Registrar usuario
            </button>
        </section>
                <div id="modalUsuario" class="modal-incidencia d-none fixed-top w-100 h-100 justify-content-center align-items-center">
            <form id="formUsuario" method="POST" action="../../../app/controlador/usuarios/procesarAltaUsuario.php">
                <input type="hidden" name="csrfToken"
                    value="<?= htmlspecialchars($_SESSION["csrfToken"], ENT_QUOTES, "UTF-8") ?>">

                <h2 id="tituloFormulario" class="text-primary border-bottom pb-2 mb-4">
                    Registro de usuario
                </h2>

                <fieldset>
                    <label for="usuario" class="form-label">
                        Usuario / Cédula
                    </label>

                    <input type="text" inputmode="numeric" id="usuario" name="cedula" class="form-control"
                        placeholder="ej: 12345678" minlength="8" maxlength="8" pattern="[1-9][0-9]{7}" required>

                    <label for="nombre" class="form-label mt-3">
                        Nombre completo
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="ej: Javier Martinez"
                        maxlength="100" required>

                    <label for="correo" class="form-label mt-3">
                        Correo electrónico
                    </label>
                    <input type="email" id="correo" name="correo" class="form-control"
                        placeholder="ej: javierM1998@gmail.com" maxlength="100" autocomplete="email" required>

                    <label for="clave" class="form-label mt-3" id="labelContra">
                        Contraseña
                    </label>
                    <input type="password" id="clave" name="clave" class="form-control" placeholder="Ingrese contraseña"
                        minlength="12" required>

                    <label for="confirmarClave" class="form-label mt-3" id="labelConfirmarContra">
                        Confirmar contraseña
                    </label>
                    <input type="password" id="confirmarClave" name="confirmarClave" class="form-control"
                        placeholder="Repita la contraseña" minlength="12" required>
                    <button type="button" class="btn btn-outline-secondary mt-2 text-bold" id="btnMostrarClave">
                        Mostrar contraseña
                    </button>
                    <div class=" my-3">
                        <span class="form-label mb-2 d-block">
                            Roles del sistema
                        </span>
                        <label class="form-check-label px-2" for="rolDocente">
                            Docente
                        </label>
                        <input class="form-check-input" type="checkbox" id="rolDocente" name="roles[]" value="docente">

                        <label class="form-check-label px-2" for="rolTecnico">
                            Técnico
                        </label>
                        <input class="form-check-input" type="checkbox" id="rolTecnico" name="roles[]" value="tecnico">
                        <label class="form-check-label px-2" for="rolAdministrador">
                            Administrador
                        </label>
                        <input class="form-check-input" type="checkbox" id="rolAdministrador" name="roles[]"
                            value="administrador">
                    </div>

                </fieldset>
                <button type="submit" class="btn btn-success text-bold" id="btnGuardarUsuario">
                    Guardar usuario
                </button>
                <button type="button" id="btnCancelarUsuario" class="btn btn-danger text-bold">
                    Cancelar
                </button>
            </form>
                </div>
    </main>
    <footer>
        <span>
            Copyright 2026 - DGTEP | Instituto Tecnológico de Informática
        </span>
    </footer>

    <script src="../../../public/assets/js/btnMenuCelular.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/assets/js/gestionUsuarios.js"></script>
    <script src="../../../public/assets/js/cerrarSesion.js"></script>
    <script src="../../../public/assets/js/verificarSesion.js"></script>

    <script src="../../../public/assets/js/verificarFormularioUsuario.js"> </script>
</body>

</html>