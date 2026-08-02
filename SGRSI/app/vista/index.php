<!DOCTYPE html >    
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/index.css">
</head>

<body>
    <header class="d-flex justify-content-center align-items-center py-4">
        <img class="imagen img-fluid" src="../assets/img/logo_iti.png" alt="Logo">
    </header>

    <main class="d-flex flex-column justify-content-center align-items-center my-5 px-3">
        
        <h2 class="mb-4 fw-bold text-center texto-azul">Ingreso al sistema</h2>
        
        <section class="Tarjeta-login card p-4 shadow border-0 w-100">

            <form id="formInicio" action="../../app/controlador/procesarLogin.php" method="post">
                <fieldset class="border-0 p-0 m-0">
                    <span class="d-block h4 fw-bold mb-4 texto-azul-dark">Inicio de sesión</span>
                    
                    <div class="mb-3 text-start">
                        <input type="hidden" id="rol" name="rol">
                        <label for="cedula" class="form-label fw-semibold texto-azul-dark">Cédula</label>
                        <input type="text" id="cedula" name="cedula" class="form-control form-control-lg" placeholder="ej:12345678" autocomplete="username"
                            pattern="[1-9][0-9]{7}" title="Ingrese exactamente 8 dígitos sin puntos ni guiones"
                            inputmode="numeric" maxlength="8" required>
                    </div>
                    
                        <label for="clave" class="form-label fw-semibold texto-azul-dark">Contraseña</label>
                        <div class="input-group">
                            <input type="password" id="clave" name="clave" class="form-control" placeholder="Ingrese contraseña" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnMostrarClave">Mostrar Contraseña</button>
                        </div>

                </fieldset>

                <button type="submit" class="btn btn-warning w-100 py-2 mt-5 fw-bold text-dark fs-5">Iniciar Sesión</button>
            </form>
        </section>
    </main>

    <footer>
        <span>Copyright 2026 - SGRSI | Instituto Tecnológico de Informática</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="../assets/js/inicioSesion.js"></script>
</body>

</html>