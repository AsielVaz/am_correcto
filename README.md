# Sistema AM modernizado

Aplicación administrativa PHP compatible con los módulos del sistema `dev`: autenticación, roles, usuarios, empresas, información fiscal y bancaria, documentos, notificaciones, proyectos, servicios y tareas.

## Acceso local

- URL: `http://localhost/am/new/`
- PHP: 8.2 o superior
- Base de datos: MySQL `am_dev`
- Configuración local: `.env`

El archivo `.env` contiene las credenciales locales y está excluido de Git. Para otra instalación se debe copiar `.env.example` como `.env` y completar las variables.

## Arquitectura

- `bootstrap.php`: carga de entorno, zona horaria, cabeceras de seguridad y utilidades de rutas físicas.
- `api/config/conectorBD.php`: conexión MySQL reutilizable, `utf8mb4`, errores controlados y soporte de consultas preparadas.
- `api/controllers`: acceso a datos y lógica heredada.
- `api/routes`: contratos HTTP compatibles con el frontend anterior.
- `api/utils`: autenticación, JWT, correo y utilidades compartidas.
- `pages`: interfaz de empresas, usuarios y autenticación.
- `assets`: estilos, JavaScript, fuentes e imágenes.
- `database/migrations`: cambios de esquema necesarios para las mejoras de seguridad.
- `Documentos`: unión local hacia `../dev/Documentos`; evita duplicar 3.1 GB y conserva todos los archivos existentes.

## Mejoras incorporadas

- Configuración y secretos fuera del código fuente mediante variables de entorno.
- Conexión centralizada y persistente durante cada petición.
- Consultas preparadas en autenticación, usuarios y control de intentos.
- Contraseñas con `password_hash`; los hashes SHA-1 existentes se migran automáticamente al iniciar sesión correctamente.
- JWT HS256 con codificación base64url, `iat`, `nbf`, `exp`, `jti` y validación de firma en tiempo constante.
- Roles `Usuario`, `Capturista` y `Admin` conservados.
- Sesión del frontend en `sessionStorage`, migración transparente desde el almacenamiento anterior y cierre únicamente ante respuestas 401.
- Cabeceras HTTP de seguridad, bloqueo de listados y bloqueo de ejecución PHP en carpetas de archivos.
- Rutas compatibles tanto con `/am/new` como con una instalación bajo otro prefijo configurable.
- Resolución automática de URLs heredadas de `Documentos` e `Imagenes`.
- Cargas apuntando al directorio de la aplicación, nombres saneados con `basename` y permisos de archivo restringidos.
- Procesos programados y correo configurables desde `.env`.
- Interfaz glassmorphism responsive para tarjetas, tablas, formularios, navegación, menús y modales, con variantes clara y oscura.
- Animaciones de entrada, transición de modales, fondos ambientales y efecto ripple; se desactivan automáticamente cuando el sistema solicita reducir movimiento.
- Notificaciones glass accesibles con estados de éxito, información, advertencia y error, temporizador, pausa al interactuar y cierre manual.
- Diálogos de confirmación personalizados para acciones destructivas.

## Base de datos

La migración `database/migrations/001_security.sql` amplía el campo de contraseña, crea la tabla de recuperación heredada y agrega el índice de intentos de acceso. Esta migración ya fue aplicada a la base local `am_dev`.

## Seguridad operativa

Antes de publicar fuera de localhost se deben reemplazar `JWT_SECRET`, las credenciales SMTP, la clave de n8n y la conexión secundaria S14. Los archivos de `Documentos` contienen información sensible y deben quedar detrás de autenticación en una fase de endurecimiento para producción.

## Verificación

Por solicitud del propietario no se ejecutaron suites de pruebas ni pruebas funcionales. La implementación se realizó conservando los contratos existentes y mediante revisión estática del código y del esquema.
