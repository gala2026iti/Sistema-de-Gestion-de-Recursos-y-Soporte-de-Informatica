const incidencias = {};

let pcActual = "";

const modal = document.getElementById("modal-incidencia");


const btnCancelar =
    document.getElementById("btnCancelarModal");


const btnAceptar =
    document.getElementById("btnAceptarModal");

document
    .querySelectorAll('input[value="incidencia"]')
    .forEach(radio => {


        radio.addEventListener("change", () => {


            const pc = radio.closest(".pc");


            pcActual = pc.querySelector("h3").textContent;


            document.getElementById("tipo").value = "";


            document.getElementById("asunto-modal").value = "";


            document.getElementById("persona-modal").value = "";


            document.getElementById("descripcion-modal").value = "";


            document
                .querySelectorAll('input[name="gravedad"]')
                .forEach(radio => radio.checked = false);


            modal.classList.remove("oculto");


        });


    });


document
    .querySelectorAll('input[value="ok"]')
    .forEach(radio => {


        radio.addEventListener("change", () => {


            const pc = radio.closest(".pc");


            const nombrePC =
                pc.querySelector("h3").textContent;


            delete incidencias[nombrePC];


            const btnModificar =
                pc.querySelector(".btn-modificar");


            btnModificar.classList.add("oculto");


        });


    });


btnCancelar.addEventListener("click", () => {


    modal.classList.add("oculto");


    const pc = [...document.querySelectorAll(".pc")]
        .find(pc => pc.querySelector("h3").textContent === pcActual);


    if (!incidencias[pcActual]) {


        pc.querySelector('input[value="ok"]').checked = true;


    }


});


btnAceptar.addEventListener("click", () => {


    const tipo =
        document.getElementById("tipo").value;


    const asunto =
        document.getElementById("asunto-modal").value.trim();


    const persona =
        document.getElementById("persona-modal").value.trim();


    const descripcion =
        document.getElementById("descripcion-modal").value.trim();


    const gravedad =
        document.querySelector(
            'input[name="gravedad"]:checked'
        );





    if (
        !tipo ||
        !asunto ||
        !persona ||
        !descripcion ||
        !gravedad
    ) {


        alert(
            "Complete todos los campos obligatorios."
        );


        return;
    }
    const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;


    if (!soloLetras.test(persona)) {


        alert(
            "'Persona que estaba usando la PC' solo puede contener letras."
        );


        return;
    }


    incidencias[pcActual] = {


        tipo,
        asunto,
        persona,
        gravedad: gravedad.value,
        descripcion


    };


    const pc = [...document.querySelectorAll(".pc")]
        .find(pc => pc.querySelector("h3").textContent === pcActual);


    const btnModificar =
        pc.querySelector(".btn-modificar");


    btnModificar.classList.remove("oculto");


    console.log(incidencias);


    modal.classList.add("oculto");


});


document
    .querySelectorAll(".btn-modificar")
    .forEach(boton => {


        boton.addEventListener("click", () => {


            pcActual = boton.dataset.pc;


            const datos = incidencias[pcActual];


            if (!datos) return;


            document.getElementById("tipo").value =
                datos.tipo;


            document.getElementById("asunto-modal").value =
                datos.asunto;


            document.getElementById("persona-modal").value =
                datos.persona;


            document.getElementById("descripcion-modal").value =
                datos.descripcion;


            document.querySelector(
                `input[name="gravedad"][value="${datos.gravedad}"]`
            ).checked = true;


            document.getElementById("titulo-modal")
                .textContent =
                "Incidencia de " + pcActual;


            modal.classList.remove("oculto");


        });


    });
