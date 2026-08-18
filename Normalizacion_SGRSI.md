# Documento de Normalización - SGRSI

Este documento contiene la transformación y proceso de normalización de las entidades y relaciones del sistema **SGRSI** (Sistema de Gestión de Recursos y Servicios Informáticos), pasando por la Primera Formas Normal (1FN), Segunda Forma Normal (2FN) y Tercera Forma Normal (3FN).

---

## 1. Entidades

### Entidad USUARIO
#### Pasaje a tablas
`USUARIO (ci, correo, nombre, clave, activo)`

#### 1FN
`USUARIO (ci, nombre, correo, clave, activo)`
> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
*(DFT: Dependencia funcional total) (DFP: Dependencia funcional parcial)*

- `{ci, correo} ➙ {nombre}`: Presenta DFP, ya que, sin 'correo', el atributo puede ser identificado.
- `{ci, correo} ➙ {clave}`: Presenta DFP, ya que, sin 'correo', el atributo puede ser identificado.
- `{ci, correo} ➙ {activo}`: Presenta DFP, ya que, sin 'correo', el atributo puede ser identificado.

`USUARIO` presenta la clave primaria 'correo' la cual presenta DFP con respecto a los atributos no primos de la tabla, por lo que se identifica la necesidad de generar el atributo **"CORREO"** para que se cumpla la dependencia funcional total:

- `USUARIO (ci, nombre, correo, clave, activo)`
- `CORREO (ci, correo)` — `ci` es FK de `USUARIO`.

> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
- `USUARIO (ci, nombre, correo, clave, activo)`
- `CORREO (ci, correo)`
  - `ci` es FK de `USUARIO`

---

### Entidad DOCENTE
`DOCENTE (ci)`
- `ci` es FK de `USUARIO`
- `ci` es PK de `DOCENTE`

---

### Entidad ADMINISTRADOR
`ADMINISTRADOR (ci)`
- `ci` es FK de `USUARIO`
- `ci` es PK de `ADMINISTRADOR`

---

### Entidad TECNICO
`TECNICO (ci)`
- `ci` es FK de `USUARIO`
- `ci` es PK de `TECNICO`

---

### Entidad SOLICITUD
#### Pasaje a tablas
`SOLICITUD (id, asunto, descripcion, fechaLimite, horaLimite, finalizada)`

#### 1FN
> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
- `{id} ➙ {asunto}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {descripcion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {fechaLimite}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {horaLimite}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {finalizada}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.

> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
`SOLICITUD (id, asunto, descripcion, fechaLimite, horaLimite, finalizada)`

---

### Entidad PRESTAMO
#### Pasaje a tablas
`PRESTAMO (id, nombrePrestado, ciPrestado, fechaInicio, horaInicio, fechaFin, horaFin, devuelto, entregaAtrasada)`

#### 1FN
> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
- `{id} ➙ {nombrePrestado}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {ciPrestado}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {fechaInicio}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {horaInicio}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {fechaFin}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {horaFin}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {devuelto}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {entregaAtrasada}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.

> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
`PRESTAMO (id, nombrePrestado, ciPrestado, fechaInicio, horaInicio, fechaFin, horaFin, devuelto, entregaAtrasada)`

---

### Entidad TICKET
#### Pasaje a tablas
`TICKET (id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, colaboradores, justificacion)`

#### 1FN
`TICKET` presenta el atributo `colaboradores`, el cual es multivaluado, por lo que se debe descomponer el atributo multivaluado en tuplas únicas para cumplir con 1FN:

- `TICKET (id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, justificacion)`
- `COLABORADOR (idTicket, ciTecnico)`
  - `idTicket` es FK de `TICKET`
  - `ciTecnico` es FK de `TECNICO`

> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
- `{id} ➙ {tipo}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {asunto}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {descripcion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {gravedad}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {estado}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {fechaCreacion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {horaCreacion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {justificacion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.

> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
- `TICKET (id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, justificacion)`
- `COLABORADOR (idTicket, ciTecnico)`
  - `idTicket` es FK de `TICKET`
  - `ciTecnico` es FK de `TECNICO`

---

### Entidad EQUIPO
#### Pasaje a tablas
`EQUIPO (id, fechaCreacion, horaCreacion, ultimaIntervencion, activo)`

#### 1FN
> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
- `{id} ➙ {fechaCreacion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {horaCreacion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {ultimaIntervencion}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.
- `{id} ➙ {activo}`: Presenta DFT, ya que, sin la clave primaria, el atributo no puede ser identificado.

> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
`EQUIPO (id, fechaCreacion, horaCreacion, ultimaIntervencion, activo)`

---

### Entidad UBICACION
#### Pasaje a tablas
`UBICACION (id, tipo)`

#### 1FN
> Pertenece a 1FN debido a que no hay atributos multivaluados ni compuestos, es decir, son atómicos.

#### 2FN
> Pertenece a 2FN debido a que está en 1FN y todos los atributos tienen una dependencia funcional total con respecto a la clave primaria.

#### 3FN
> Pertenece a 3FN, esto debido a que está en 2FN y ningún atributo presenta una dependencia funcional transitiva con respecto a la clave primaria.

#### Resultado
`UBICACION (id, tipo)`

---

## 2. Relaciones

### Relación `docente_ingresa_solicitud`
#### Pasaje a tablas
`docente_ingresa_solicitud (ciDocente, idSolicitud, fecha, hora)`
- `ciDocente` es FK de `DOCENTE`
- `idSolicitud` es FK de `SOLICITUD`

#### 1FN / 2FN / 3FN
Pertenece a 1FN, 2FN y 3FN.

#### Resultado
`docente_ingresa_solicitud (ciDocente, idSolicitud, fecha, hora)`
- `ciDocente` es FK de `DOCENTE`
- `idSolicitud` es FK de `SOLICITUD`

---

### Relación `tecnico_finaliza_solicitud`
#### Pasaje a tablas
`tecnico_finaliza_solicitud (ciTecnico, idSolicitud, fecha, hora)`
- `ciTecnico` es FK de `TECNICO`
- `idSolicitud` es FK de `SOLICITUD`

#### 2FN
- `{ciTecnico, idSolicitud} ➙ {fecha}`: Presenta DFT, ya que, sin alguna clave primaria, el atributo no puede ser identificado.
- `{ciTecnico, idSolicitud} ➙ {hora}`: Presenta DFT, ya que, sin alguna clave primaria, el atributo no puede ser identificado.

> Pertenece a 2FN y 3FN.

#### Resultado
`tecnico_finaliza_solicitud (ciTecnico, idSolicitud, fecha, hora)`
- `ciTecnico` es FK de `TECNICO`
- `idSolicitud` es FK de `SOLICITUD`

---

### Relación `tecnico_tramita_prestamo`
#### Pasaje a tablas / Resultado
`tecnico_tramita_prestamo (id, ciTecnico, idPrestamo, fecha, hora, tipoInteraccion)`
- `ciTecnico` es FK de `TECNICO`
- `idPrestamo` es FK de `PRESTAMO`

---

### Relación `prestamo_corresponde_equipo`
#### Pasaje a tablas / Resultado
`prestamo_corresponde_equipo (idPrestamo, idEquipo)`
- `idPrestamo` es FK de `PRESTAMO`
- `idEquipo` es FK de `EQUIPO`

---

### Relación `administrador_maneja_equipo`
#### Pasaje a tablas / Resultado
`administrador_maneja_equipo (id, ciAdministrador, idEquipo, fecha, hora, tipoInteraccion)`
- `ciAdministrador` es FK de `ADMINISTRADOR`
- `idEquipo` es FK de `EQUIPO`

---

### Relación `administrador_controla_ubicacion`
#### Pasaje a tablas / Resultado
`administrador_controla_ubicacion (id, ciAdministrador, idUbicacion, fecha, hora, tipoInteraccion)`
- `ciAdministrador` es FK de `ADMINISTRADOR`
- `idUbicacion` es FK de `UBICACION`

---

### Relación `equipo_reside_ubicacion`
#### Pasaje a tablas / Resultado
`equipo_reside_ubicacion (idEquipo, idUbicacion)`
- `idEquipo` es FK de `EQUIPO`
- `idUbicacion` es FK de `UBICACION`

---

### Relación `equipo_ubicacion_genera_ticket`
#### Pasaje a tablas / Resultado
`equipo_ubicacion_genera_ticket (idEquipo, idUbicacion, idTicket)`
- `idEquipo` es FK de `EQUIPO`
- `idUbicacion` es FK de `UBICACION`
- `idTicket` es FK de `TICKET`

---

### Relación `docente_reporta_ticket`
#### Pasaje a tablas / Resultado
`docente_reporta_ticket (ciDocente, idTicket)`
- `ciDocente` es FK de `DOCENTE`
- `idTicket` es FK de `TICKET`

---

### Relación `tecnico_gestiona_ticket`
#### Pasaje a tablas / Resultado
`tecnico_gestiona_ticket (id, ciTecnico, idTicket, fecha, hora, tipoInteraccion)`
- `ciTecnico` es FK de `TECNICO`
- `idTicket` es FK de `TICKET`

---

### Relación `administrador_modifica_usuario`
#### Pasaje a tablas / Resultado
`administrador_modifica_usuario (id, ciAdministrador, ciUsuario, fecha, hora, tipoInteraccion)`
- `ciAdministrador` es FK de `ADMINISTRADOR`
- `ciUsuario` es FK de `USUARIO`

---

### Relación `tecnico_comenta_ticket`
#### Pasaje a tablas / Resultado
`tecnico_comenta_ticket (id, ciTecnico, idTicket, fecha, hora, texto)`
- `ciTecnico` es FK de `TECNICO`
- `idTicket` es FK de `TICKET`

---

## 3. Restricciones No Estructurales

- El atributo `finalizada` de `SOLICITUD` puede tener alguno de los siguientes valores: `"true"`, `"false"`.
- El atributo `tipo` de `SOLICITUD` puede tener alguno de los siguientes valores: `"asistencia"`, `"reunion"`, `"administracion"`.
- El atributo `devuelto` de `PRESTAMO` puede tener alguno de los siguientes valores: `"true"`, `"false"`.
- El atributo `entregaAtrasada` de `PRESTAMO` puede tener alguno de los siguientes valores: `"true"`, `"false"`.
- El atributo `activo` de `USUARIO` puede tener alguno de los siguientes valores: `"true"`, `"false"`.
- El atributo `activo` de `EQUIPO` puede tener alguno de los siguientes valores: `"true"`, `"false"`.
- El atributo `tipo` de `UBICACION` puede tener alguno de los siguientes valores: `"laboratorio"`, `"taller"`.
- El atributo `tipo` de `TICKET` puede tener alguno de los siguientes valores: `"equipo"`, `"ubicacion"`, `"red"`.
- El atributo `estado` de `TICKET` puede tener alguno de los siguientes valores: `"pendiente"`, `"en proceso"`, `"resuelto"`.
- El atributo `gravedad` de `TICKET` puede tener alguno de los siguientes valores: `"baja"`, `"media"`, `"alta"`.
- El atributo `tipoInteraccion` de `tramita` puede tener alguno de los siguientes valores: `"prestamo"`, `"devolucion"`.
- El atributo `tipoInteraccion` de `maneja` puede tener alguno de los siguientes valores: `"creacion"`, `"modificacion"`.
- El atributo `tipoInteraccion` de `controla` puede tener alguno de los siguientes valores: `"creacion"`, `"eliminacion"`.
- El atributo `tipoInteraccion` de `gestiona` puede tener alguno de los siguientes valores: `"creacion"`, `"modificacion"`, `"resolucion"`.
- El atributo `tipoInteraccion` de `modifica` puede tener alguno de los siguientes valores: `"creacion"`, `"modificacion"`, `"desactivacion"`.
