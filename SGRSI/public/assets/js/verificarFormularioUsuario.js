{
  const modificarFormulario = document.getElementById("formModificarUsuario");

  if (modificarFormulario) {
    modificarFormulario.addEventListener("submit", (e) => {
      
      const correoIngresadoModificado = document.getElementById("modificarCorreo");
      const claveModificada = document.getElementById("modificarClave");
      const confirmarClaveModificada = document.getElementById("modificarConfirmarClave");

      if(correoIngresadoModificado && correoIngresadoModificado.value.trim() !== ""){
        if(!(correoIngresadoModificado.value.includes("@") && correoIngresadoModificado.value.includes("."))) {
          alert('Error: Ingresa un correo válido, que incluya "@" y puntos correspondientes.')
          e.preventDefault();
          return;
        }
      }

      if (claveModificada && claveModificada.value.trim() !== "") {
        if (claveModificada.value !== confirmarClaveModificada.value) {
          alert("Error: Las contraseñas no coinciden.");
          e.preventDefault();
          return;
        }
      }
      const checkboxes = document.querySelectorAll(".form-check-input"); 
      let casillasMarcadas = 0;

      checkboxes.forEach(c => {
        if (c.checked) {
          casillasMarcadas++;
        }
      });

      if (casillasMarcadas === 0) {
        alert("Error: Seleccione al menos un rol para el usuario.");
        e.preventDefault();
        return;
      }

    });
  }

  const formulario = document.getElementById("formUsuario");

  if (formulario) {
    formulario.addEventListener("submit", (e) => {
      const cedula = document.getElementById("cedula")
      const correo = document.getElementById("correo");
      const clave = document.getElementById("clave");
      const confirmarClave = document.getElementById("confirmarClave");

      if(cedula){
      if(!(is_numeric(cedula.value) && cedula.value.length === 8)) {
        alert('Error: Ingresa una cédula valida de 8 dígitos.')
        e.preventDefault();
        return
      }
      }

      if(correo && correo.value.trim() !== ""){
        if(!(correo.value.includes("@") && correo.value.includes("."))) {
          alert('Error: Ingresa un correo válido, que incluya "@" y puntos correspondientes.')
          e.preventDefault();
          return;
        }
      }

      if (clave && clave.value.trim() !== "") {
        if (clave.value !== confirmarClave.value) {
          alert("Error: Las contraseñas no coinciden.");
          e.preventDefault();
          return;
        }
      }
      const checkboxes = document.querySelectorAll(".form-check-input");
      let casillasMarcadas = 0;

      checkboxes.forEach(c => {
        if (c.checked) {
          casillasMarcadas++;
        }
      });

      if (casillasMarcadas === 0) {
        alert("Error: Seleccione al menos un rol para el usuario.");
        e.preventDefault();
        return;
      }

    });
  }
}