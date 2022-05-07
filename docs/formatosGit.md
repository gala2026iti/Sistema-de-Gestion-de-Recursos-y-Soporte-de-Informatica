# Formato de Git

## Formato de Issues

### Lineamientos generales

- **Títulos:** deben ser concretos y concisos, describiendo únicamente la acción requerida.
- **Problema:** especificar brevemente qué se debe resolver.
- **Descripción:** incluir un desglose detallado y los parámetros del problema.
- **Entregable:** definir qué evidencia confirma la resolución del issue (por ejemplo, una sección de página actualizada).

## Plantilla

```
+ ------------------------------------ +
|                                      |
|             Título     (H1)          |
|   Problema: ......     (H2)          |
|             (cont. con texto normal) |
|   Descripción: ...                   |
|                                      |
|   Entregable: ....                   |
|                                      |  
+ ------------------------------------ +
```

### Ejemplo: `Issue #10`

```
# Implementar estilos globales responsivos

## Problema
El cliente quiere que el diseño visual del sistema tenga colores y bordeados, y necesita la adaptación de texto en distintos tamaños de pantalla.

## Descripción
Se debe implementar un archivo global.css para brindar los estilos de bordeado y de colores solicitados por el cliente a todo el sistema y mantener, entre página y página, el texto adaptable a la ventana.

El archivo deberá incluir:

- Paleta de colores beige.
- Bordes sólidos en las secciones principales de las páginas.
- Tamaños de texto utilizando medidas relativas (`rem` y `em`).
- Configuración responsive para distintas resoluciones.

Además, todas las páginas `.html` deberán importar correctamente el archivo `global.css`.

## Entregable
Archivo `global.css` implementado y correctamente vinculado en todas las páginas `.html` del proyecto.
```
Los issues deben ser creados dentro del tablero KanBan correspondiente, y dentro de los mismos se deben incluir etiquetas (o labels) para una mejor clasificación del issue, y colaboradores responsables de resolver cada issue.

## Formato de Commits

A la hora de realizar cambios o implementaciones, ya sea en documentación o en código alojado en el repositorio, se requiere hacer uso de un commit para registrar los cambios realizados.

Se debe explicar de manera clara y específica qué implementaciones incluye dicho commit, esto para asegurar un registro adecuado de los cambios realizados en el sistema, permitiendo un mejor seguimiento de los avances realizados por los distintos integrantes.



### Commits relacionados a Issues

El commit debe citar el/los issues correspondientes al problema a resolver, explicando qué modificaciones se realizaron para llegar a la solución deseada.

### Manejo de prefijos

Al inicio del mensaje de cada commit se deberá colocar uno de los siguientes prefijos:

- **feat**: Nueva funcionalidad
- **fix**: Corrección de error
- **docs**: Cambios en documentación
- **style**: Cambios visuales o de formato
- **refactor**: Reescritura sin cambiar comportamiento
- **test**: Cambios en pruebas
- **chore**: Tareas menores
- **perf**: Optimización de rendimiento
- **build**: Cambios en build o dependencias
- **ci**: Cambios en integración continua

### Closing keywords

Al final de cada mensaje se deberá colocar una de las siguientes closing keywords seguido de su número de issue.

- **close**: Cierra la issue indicada cuando el commit llega a la rama principal
- **closes**: Versión plural de *close*, con el mismo efecto
- **closed**: Variante en pasado, también válida para cerrar la issue
- **fix**: Cierra la issue asociada al corregir un error
- **fixes**: Versión plural de *fix*, con el mismo efecto
- **fixed**: Variante en pasado, también válida para cerrar la issue
- **resolve**: Cierra la issue al indicar que fue resuelta
- **resolves**: Versión plural de *resolve*, con el mismo efecto
- **resolved**: Variante en pasado, también válida para cerrar la issue


#### Ejemplo: `Issue 10`

```
fix: Resolve #10. Se realiza la implementación del archivo global.css,
donde quedará alojado el contenido de bordes a utilizar en todas las páginas .html.

Se implementa la paleta de colores beige en distintas secciones de las páginas y se modifican los textos presentes en las mismas,
usando medidas relativas "rem" y "em" para que el tamaño de las letras sea correspondiente al tamaño de la ventana, facilitando la lectura.
```

### Commits NO relacionados a Issues

Este tipo de commits no se relaciona con ningún issue relacionado al sistema, únicamente incluyen nuevas implementaciones dentro del sistema,
sin el fin de resolver ningún problema en el sistema.

Varios ejemplos de esto pueden ser la creación de un archivo con fines de documentación o la implementación de nuevas carpetas o archivos relacionados al sistema, ya sea para implementar nuevas herramientas, mejorar algún aspecto relacionado al código u organización del repositorio, entre otros, aclarando que estos nuevos archivos no constituyen la resolución de ningún inconveniente existente.

#### Ejemplo:

```
feat: Se crea la estructura de carpetas que van a contener el código del sistema,
incluyendo carpetas para el contenido .html y los assets.

Este último incluye las carpetas img, css y js.

También se implementa una carpeta aparte correspondiente a documentación relacionada a Git.
```

## Uso y Justificación de Ramas

Para una correcta administración de código por parte de los distintos integrantes, se hará uso de 6 ramas:

**Rama principal (main):** Esta rama tiene como fin almacenar todo lo relacionado al sistema, incluyendo documentación final y archivos .html, .css, .js, imágenes o cualquier otro archivo considerado requerido.

**Rama borrador (beta):** Esta rama tiene como fin almacenar todo el contenido relacionado a versiones beta o "borrador" tanto del sistema como de la posible documentación a implementar, funcionando más como una "sandbox", donde se puede experimentar con nuevas funciones, realizar modificaciones al sistema o modificar código y archivos libremente sin el riesgo de corromper, dañar  o perjudicar la integridad del sistema real.

Esta rama, en términos de código, se utiliza para implementar funciones experimentales, probarlas, ver los resultados y considerar si serán implementadas, serán descartadas o si requieren de modificaciones.

**Ramas Individuales (4 ramas extra):** Cada rama corresponde al espacio personal de cada integrante, donde cada quien puede realizar distintos aportes relacionados a subir documentación, subir código correspondiente al sistema, implementar nuevos archivos como imágenes, vectores o contenido de la página, o experimentar con funciones nuevas.

Estas ramas ayudan a evitar mezclar la información, tanto entre estudiantes como en las ramas contenedoras del sistema. Además, ayudan a identificar de mejor forma las contribuciones y/o aportes de cada integrante de manera más organizada, ayudando a tener un mejor seguimiento de su actividad dentro del repositorio.

## Sistema de Versiones
El proyecto utilizará un sistema de versionado semántico para identificar y organizar los distintos cambios realizados sobre el sistema.

La estructura utilizada será:

MAJOR.MINOR.PATCH

- **PATCH** ("0.0.1"): representa cambios pequeños, correcciones menores 
o ajustes simples dentro del sistema.
- **MINOR** ("0.1.0"): representa incorporaciones de nuevas 
funcionalidades o cambios moderadamente importantes.
- **MAJOR** ("1.0.0"): representa cambios grandes dentro del sistema, 
modificaciones importantes en el funcionamiento general o nuevas versiones principales del proyecto.

Este sistema permite mantener un mejor control de la evolución del proyecto, 
facilitando el seguimiento de cambios y la organización del desarrollo 
dentro del repositorio.
