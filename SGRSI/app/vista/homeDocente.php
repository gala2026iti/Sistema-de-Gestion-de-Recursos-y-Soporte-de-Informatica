<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Incidencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/formularioTickets.css">
    <link rel="stylesheet" href="assets/css/formulariospopup.css">
</head>

<body data-rol-permitido="docente">
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="assets/img/logo_iti.png" alt="Logo">
    </header>

    <nav class="navbarSGRSI">
        <section class="nav-container">
            <section class="nav-primera-fila">
                <button class="btn-menu" id="btnMenu">☰</button>
                <button class="btn-cerrar-lateral" id="btnCerrar">X</button>
            <ul class="nav-opciones-sistema">
                    <li><a href="../../public/paginaWeb/cerrarSesion.php" method="post" id="cerrarSesion">Cerrar Sesion</a></li>
            </ul>
            </section>
            <ul class="nav-menu">
                <li><a href="../paginaWeb/pagsolicitudes.html" id="btnServicios">Solicitud de servicios</a></li>
            </ul>
            <ul class="nav-opciones-sistema">
                <li><a href="cerrarSesion.php">Cerrar Sesion</a></li>
            </ul>
        </section>
    </nav>

    <main class="main-formulario d-flex justify-content-center align-items-center py-5 px-3">
        <section class="w-100 container-formulario">
            <h2 class="mb-4 fw-bold text-center texto-azul">Registro de incidencias</h2>

            <form class="p-4 shadow border-0 bg-Formulario rounded-4" id="formIncidencia">

                <div class="campo mb-4">
                    <label class="form-label fw-semibold texto-azul-dark">Seleccione taller o laboratorio</label>
                    <select id="ubicacionSalon" name="ubicacionSalon" class="form-select form-select-lg" required>
                        <option value="">Seleccione una opción</option>
                        <optgroup label="Laboratorios" id="grupoLaboratorio"></optgroup>
                        <optgroup label="Talleres" id="grupoTalleres"></optgroup>
                    </select>
                </div>

                <div id="contenedorEquipos"></div>

                <button type="submit" class="btn btn-warning w-100 py-3 mt-3 fw-bold text-dark fs-5 shadow-sm">
                    Registrar estado del salón
                </button>
            </form>
        </section>

        <div id="incidencia" class="modal-incidencia oculto w-100 h-100 d-flex justify-content-center align-items-center">

            <form class="from modal-contenido w-100 p-4 bg-white">

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

                    <label for="persona" class="form-label fw-semibold texto-azul-dark">Persona que estaba haciendo uso de la PC</label>
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
                    <textarea id="descripcion" name="descripcion02" class="form-control mb-3" placeholder="Información más detallada si así lo precisa" rows="3" maxlength="300"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" id="btnAceptar" class="btn btn-success">Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn btn-danger">Cancelar</button>
                </div>

            </form>
        </div>
    </main>

    <footer>
        <span>Copyright 2026 - SGRSI | Instituto tecnológico de Informática</span>
    </footer>

    <script src="assets/js/registroIncidencias.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/btnMenuCelular.js"></script>
</body>

</html>