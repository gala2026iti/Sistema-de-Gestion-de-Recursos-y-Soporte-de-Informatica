<?php

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaUsuario.php";

/**
 * @brief Procesa el formulario de alta de usuarios.
 *
 * Recibe los datos enviados mediante POST, valida la información
 * ingresada, genera el hash de la contraseña y solicita al modelo
 * el registro del usuario junto con sus roles.
 *
 * Si ocurre un error de validación o de acceso a la base de datos,
 * redirige nuevamente a la página de gestión de usuarios mostrando
 * el mensaje correspondiente.
 */

/*
 * Solamente se permite POST.
 */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


$cedula = trim($_POST["cedula"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$clave = $_POST["clave"] ?? "";
$confirmarClave = $_POST["confirmarClave"] ?? "";

/*
 * Los roles se reciben como un array porque
 * un usuario puede tener más de un rol.
 */
$roles = $_POST["roles"] ?? [];


/*
 * Verificamos que los roles recibidos tengan
 * el formato esperado.
 */
if (!is_array($roles)) {

    $mensaje = "Los roles seleccionados no son válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Normalizamos los roles para trabajar con
 * valores consistentes.
 */
$roles = array_map(
    function ($rol) {
        return strtolower(trim($rol));
    },
    $roles
);


/*
 * Evitamos procesar un mismo rol más de una vez.
 */
$roles = array_unique($roles);


/*
 * Validamos campos vacíos.
 */
if (
    $cedula === "" ||
    $nombre === "" ||
    $correo === "" ||
    $clave === "" ||
    $confirmarClave === "" ||
    empty($roles)
) {

    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos la cédula de 8 dígitos.
 */
if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {

    $mensaje = "La cédula debe contener exactamente 8 dígitos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos el nombre.
 */
if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/", $nombre)) {

    $mensaje = "El nombre contiene caracteres no válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos el formato del correo electrónico.
 */
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $mensaje = "El correo electrónico no es válido.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos la longitud mínima de la contraseña.
 */
if (strlen($clave) < 12) {

    $mensaje = "La contraseña debe contener al menos 12 caracteres.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Verificamos que ambas contraseñas coincidan.
 */
if ($clave !== $confirmarClave) {

    $mensaje = "Las contraseñas no coinciden.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Roles disponibles en el sistema.
 */
$rolesValidos = [
    "administrador",
    "tecnico",
    "docente"
];


/*
 * Verificamos que todos los roles seleccionados
 * pertenezcan a los roles permitidos.
 */
foreach ($roles as $rol) {

    if (!in_array($rol, $rolesValidos, true)) {

        $mensaje = "Uno de los roles seleccionados no es válido.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );

        exit();
    }
}


/*
 * La contraseña se almacena mediante un hash
 * y no como texto plano.
 */
$claveHash = password_hash($clave, PASSWORD_DEFAULT);


$conectorPDO = new ConectorPDO(
    "127.0.0.1:3306",
    "root",
    "",
    "sgrsi"
);

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {

    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


$altaDatosUsuario = new AltaDatosUsuario($conexion);


/*
 * El modelo recibe el usuario y el array completo
 * de roles para realizar las inserciones correspondientes.
 */
$resultado = $altaDatosUsuario->registrarUsuario(
    $cedula,
    $nombre,
    $correo,
    $claveHash,
    $roles
);


$conectorPDO->desconectar();


if (!$resultado) {

    $mensaje = "No se pudo registrar el usuario. La cédula o el correo pueden estar ya registrados.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


$mensaje = "Usuario registrado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();