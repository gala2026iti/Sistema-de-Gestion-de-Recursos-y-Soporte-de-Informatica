const API_METRICAS =
    "../index.php/metricas/equipos";

const API_USUARIOS =
    "../index.php/usuarios";


const cantTotalEquipos =
    document.getElementById("cantTotalEquipos");

const cantEquiposFallados =
    document.getElementById("cantEquiposFallados");

const fechaUltimoIncidente =
    document.getElementById("fechaUltimoIncidente");

const tablaFallas =
    document.getElementById("tablaFallas");

const equipoMasFallado =
    document.getElementById("equipoMasFallado");

const cantEquipoMasFallado =
    document.getElementById("cantEquipoMasFallado");

const cantIncidencias =
    document.getElementById("cantIncidencias");

const tablaUsuarios =
    document.getElementById("tablaUsuarios");


document.addEventListener("DOMContentLoaded", () => {
    cargarDatos();
});


async function cargarDatos()
{
    try {

        const [
            respuestaMetricas,
            respuestaUsuarios
        ] = await Promise.all([
            fetch(API_METRICAS),
            fetch(API_USUARIOS)
        ]);


        const datosMetricas =
            await respuestaMetricas.json();

        const datosUsuarios =
            await respuestaUsuarios.json();


        if (!respuestaMetricas.ok) {
            throw new Error(
                datosMetricas.mensaje ??
                "No se pudieron cargar las métricas."
            );
        }


        if (!respuestaUsuarios.ok) {
            throw new Error(
                datosUsuarios.mensaje ??
                "No se pudieron cargar los usuarios."
            );
        }


        mostrarMetricas(
            datosMetricas.datos
        );

        mostrarUsuarios(
            datosUsuarios.datos
        );

    } catch (error) {

        console.error(
            "Error al cargar las métricas:",
            error
        );
    }
}


function mostrarMetricas(metricas)
{
    cantTotalEquipos.textContent =
        metricas.totalEquipos;

    cantEquiposFallados.textContent =
        metricas.equiposFallados;

    fechaUltimoIncidente.textContent =
        formatearFechaHora(
            metricas.ultimoIncidente
        );

    cantIncidencias.textContent =
        metricas.incidenciasGraves;


    mostrarRanking(
        metricas.rankingEquipos
    );


    if (metricas.rankingEquipos.length > 0) {

        const primerEquipo =
            metricas.rankingEquipos[0];

        equipoMasFallado.textContent =
            `Equipo #${primerEquipo.idEquipo}`;

        cantEquipoMasFallado.textContent =
            primerEquipo.fallasRegistradas;

    } else {

        equipoMasFallado.textContent =
            "Ninguno";

        cantEquipoMasFallado.textContent =
            "0";
    }
}


function mostrarRanking(equipos)
{
    tablaFallas.innerHTML = "";

    if (equipos.length === 0) {

        const fila =
            document.createElement("tr");

        fila.innerHTML = `
            <td colspan="3" class="text-center text-muted py-4">
                No hay incidencias registradas.
            </td>
        `;

        tablaFallas.appendChild(fila);

        return;
    }


    equipos.forEach(equipo => {

        const fila =
            document.createElement("tr");


        let ubicacion =
            "Sin ubicación";


        if (
            equipo.idUbicacion !== null &&
            equipo.tipoUbicacion !== null
        ) {

            if (equipo.tipoUbicacion === "prestamo") {

                ubicacion = "Préstamo";

            } else {

                ubicacion =
                    `${capitalizar(equipo.tipoUbicacion)} ${equipo.idUbicacion}`;
            }
        }


        fila.innerHTML = `
            <td>
                Equipo #${equipo.idEquipo}
            </td>

            <td>
                ${ubicacion}
            </td>

            <td>
                ${equipo.fallasRegistradas}
            </td>
        `;


        tablaFallas.appendChild(fila);
    });
}


function mostrarUsuarios(usuarios)
{
    tablaUsuarios.innerHTML = "";


    usuarios.forEach(usuario => {

        const fila =
            document.createElement("tr");


        const activo =
            usuario.activo === true ||
            usuario.activo === 1 ||
            usuario.activo === "1";


        fila.innerHTML = `
            <td class="ps-4">
                ${usuario.nombre}
            </td>

            <td>
                ${usuario.correo ?? "-"}
            </td>

            <td>
                ${obtenerRoles(usuario)}
            </td>

            <td class="text-center pe-4">
                ${
                    activo
                        ? "Activo"
                        : "Inactivo"
                }
            </td>
        `;


        tablaUsuarios.appendChild(fila);
    });
}


function obtenerRoles(usuario)
{
    if (Array.isArray(usuario.roles)) {
        return usuario.roles
            .map(rol => capitalizar(rol))
            .join(", ");
    }


    if (
        typeof usuario.roles === "string" &&
        usuario.roles !== ""
    ) {
        return usuario.roles;
    }


    if (
        typeof usuario.rol === "string" &&
        usuario.rol !== ""
    ) {
        return capitalizar(usuario.rol);
    }


    const roles = [];


    if (usuario.administrador) {
        roles.push("Administrador");
    }

    if (usuario.tecnico) {
        roles.push("Técnico");
    }

    if (usuario.docente) {
        roles.push("Docente");
    }


    if (roles.length === 0) {
        return "Sin rol";
    }


    return roles.join(", ");
}


function formatearFechaHora(valor)
{
    if (!valor) {
        return "N/A";
    }


    const partes =
        valor.split(" ");

    const fecha =
        partes[0];

    const hora =
        partes[1] ?? "";


    const [
        anio,
        mes,
        dia
    ] = fecha.split("-");


    if (!anio || !mes || !dia) {
        return valor;
    }


    if (hora === "") {
        return `${dia}/${mes}/${anio}`;
    }


    return `${dia}/${mes}/${anio} ${hora.substring(0, 5)}`;
}


function capitalizar(texto)
{
    if (!texto) {
        return "";
    }

    return texto.charAt(0).toUpperCase() +
        texto.slice(1);
}