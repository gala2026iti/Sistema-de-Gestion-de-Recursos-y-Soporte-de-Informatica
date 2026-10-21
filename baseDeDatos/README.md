# Base de datos GALA / SGRSI

Esta carpeta fue construida a partir del proyecto completo entregado y de los DAO
actuales ubicados en `SGRSI/app/modelo`.

## Organización

### DDL

Contiene la definición de la estructura de la base de datos.

- `00_configuracion.sql`: zona horaria y selección de `gala`.
- `01_eliminar_tablas.sql`: elimina las tablas respetando claves foráneas.
- Un archivo `.sql` por cada una de las 24 tablas.
- `creacionBDCompletaGALA.sql`: alternativa para recrear toda la estructura en un único paso.

### DML

Se divide en:

- `poblacion/`: datos iniciales/de prueba, separados por tabla.
- `operaciones/`: INSERT, UPDATE y DELETE que ejecutan los DAO durante el funcionamiento real.

La población incluye un ejemplo de ticket creado directamente por un técnico sobre
un equipo ubicado en `(0, 'prestamo')`, porque esa funcionalidad forma parte de la
versión final del sistema.

### DQL

Contiene las consultas `SELECT` utilizadas por los DAO finales, separadas por módulo:
- equipo
- historial
- metricas
- prestamo
- solicitud
- ticket
- ubicacion
- usuario

Los `:parametro` se mantienen porque el sistema utiliza consultas preparadas PDO.
Los filtros que PHP agrega dinámicamente se documentan junto a cada consulta.

## Decisiones finales reflejadas

- `UBICACION` utiliza `activo` para la baja lógica de laboratorios y talleres.
- La ubicación `(0, 'prestamo')` es una ubicación interna reservada.
