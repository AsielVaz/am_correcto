# Sistema administrativo

Aplicación web desarrollada con PHP, MySQL y JavaScript para gestionar registros, datos de contacto, información fiscal y bancaria, documentos y usuarios. La interfaz utiliza tablas, búsquedas y formularios en ventanas modales.

## Requisitos

- PHP 8.2 o superior.
- MySQL y extensión `mysqli`.
- Extensiones de PHP `json`, `mbstring` y `openssl` para procesamiento de datos y cifrado.
- Servidor web con soporte para PHP. En Apache, conservar el archivo `.htaccess` y permitir su aplicación.
- Navegador con JavaScript habilitado.

## Configuración

Copiar `.env.example` como `.env` y ajustar las variables para la instalación. El archivo `.env` está excluido de Git y contiene la configuración privada.

| Variables | Función |
| --- | --- |
| `APP_ENV`, `APP_DEBUG` | Entorno y visualización de errores. |
| `APP_URL` | URL base de la instalación, incluyendo el subdirectorio cuando corresponda. |
| `APP_TIMEZONE` | Zona horaria utilizada por PHP. |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_CHARSET` | Conexión a la base de datos principal. |
| `JWT_SECRET`, `JWT_TTL` | Firma y duración de los tokens de sesión; la clave debe tener al menos 32 caracteres. |
| `SMTP_HOST`, `SMTP_PORT`, `SMTP_SECURE`, `SMTP_EMAIL`, `SMTP_PASSWORD`, `SMTP_FROM_NAME` | Envío de correo. |
| `N8N_API_KEY` | Clave para la integración con n8n. |
| `S14_DB_HOST`, `S14_DB_PORT`, `S14_DB_DATABASE`, `S14_DB_USERNAME`, `S14_DB_PASSWORD` | Conexión secundaria para los módulos que la utilizan. |

El archivo `bootstrap.php` carga estas variables y configura la zona horaria y las cabeceras generales. Las funciones de `utils/routes.php` generan las rutas a páginas y recursos; si no se configura `APP_URL`, calculan el prefijo desde la petición actual.

## Inicio de sesión y permisos

La página inicial redirige al formulario de acceso. Al ingresar credenciales válidas, el servidor genera un token JWT que identifica al usuario y su rol. El navegador guarda la sesión en `sessionStorage` y agrega el token a las solicitudes dirigidas a la API mediante la cabecera `Authorization: Bearer`.

La sesión se cierra cuando vence el token o el servidor devuelve una respuesta `401`. Una respuesta `403` indica falta de permisos. El menú **Cuenta** permite volver a la administración principal, acceder a la gestión de usuarios cuando el rol lo permite y cerrar sesión.

| Rol | Funcionamiento |
| --- | --- |
| `Usuario` | Consulta de información; la vista principal adapta las columnas según el departamento. |
| `Capturista` | Consulta y operaciones de captura autorizadas en los formularios existentes. |
| `Admin` | Captura, administración de usuarios y edición general mediante el botón de acciones. |

La interfaz utiliza `assets/js/permissions.js` para ocultar o deshabilitar controles según el rol. Las rutas que utilizan `api/utils/auth_guard.php` también validan autenticación y permisos en el servidor.

Las contraseñas de inicio de sesión se almacenan mediante `password_hash`. Los hashes SHA-1 heredados se convierten al formato actual cuando se inicia sesión correctamente.

## Consulta de registros

La pantalla principal presenta información en una tabla con búsqueda y paginación. Los datos básicos se cargan desde la API; las secciones relacionadas se consultan al abrir sus modales y se almacenan temporalmente en una caché del navegador.

Los iconos de estado permiten distinguir información completada, sin datos, urgente, en revisión, rechazada, postergada o con archivos faltantes. Los estados dependen de los valores registrados, los periodos de actualización y la información de cada sección.

Los controles de copiar permiten obtener un dato individual o una fila completa. Las notificaciones indican el resultado de las operaciones y los formularios muestran errores de validación o de conexión.

## Captura y edición

Los formularios existentes permiten registrar datos de contacto, dirección, sitio web, información fiscal, cuentas bancarias y credenciales. Las operaciones disponibles dependen del rol y de la sección abierta.

Para `Admin`, los modales de cuentas bancarias y accesos de bancos, SAT e iofacturo muestran un único lápiz de edición general por registro en la columna **Acciones**, junto a Copiar. Los campos individuales no muestran lápices. En la columna de razón social también está disponible la edición de datos generales.

El flujo de edición funciona así:

1. Seleccionar el lápiz del registro.
2. Modificar los datos en el formulario.
3. Pulsar **Guardar** para enviar los cambios al servidor.
4. El servidor valida los valores y la relación del registro con su registro principal, y guarda dentro de una transacción.
5. La interfaz refresca la tabla y la caché para mostrar los valores actualizados.

**Cancelar** descarta lo escrito. Si falla el guardado, el formulario conserva los valores para reintentar. Al cerrar con cambios pendientes se solicita confirmar su descarte.

La edición incluye banco, número de cuenta, CLABE, moneda, usuarios y credenciales. Los campos protegidos se presentan vacíos: escribir un nuevo valor reemplaza el existente y dejarlos vacíos lo conserva. Las nuevas credenciales se cifran con el formato utilizado por los módulos actuales.

La API de edición no devuelve contraseñas ni valores cifrados al formulario. Exige el rol `Admin`, utiliza consultas preparadas y comprueba que el registro editado corresponda al contexto seleccionado. Los cambios de razón social mantienen la relación bancaria heredada; se rechazan coincidencias ambiguas con otros registros.

## Documentos y periodos

Las secciones documentales permiten cargar, consultar, descargar y gestionar archivos según los controles disponibles. Incluyen documentación permanente, comprobantes, constancias, opiniones de cumplimiento, archivos fiscales, estados de cuenta y carátulas bancarias.

Los archivos se almacenan en `Documentos` e `Imagenes`. La interfaz resuelve sus rutas respecto a la instalación y señala los documentos referenciados que no se encuentran en el almacenamiento.

Los periodos de actualización y las postergaciones se utilizan para calcular estados y necesidades de revisión. Al modificar teléfono, correo o sitio web mediante la API de edición, se actualiza su periodo y se eliminan las postergaciones del campo correspondiente.

## Arquitectura

| Ruta | Responsabilidad |
| --- | --- |
| `index.php` | Entrada y redirección al acceso. |
| `bootstrap.php` | Configuración compartida. |
| `utils/routes.php` | Construcción de URLs. |
| `components` | Estructura visual compartida y menú de cuenta. |
| `pages/auth` | Inicio y cierre de sesión. |
| `pages/business` | Tabla principal, modales y scripts de gestión. |
| `pages/users` | Administración de usuarios. |
| `assets` | Estilos, iconos y JavaScript compartido. |
| `api/routes` | Puntos de entrada HTTP. |
| `api/controllers` | Acceso a datos y lógica de gestión. |
| `api/config/conectorBD.php` | Conexión MySQL, consultas preparadas y transacciones. |
| `api/utils` | Autenticación, cifrado y utilidades. |
| `database/migrations` | Cambios de esquema. |

La edición general se implementa en `pages/business/scripts/inline-edicion.js` y `pages/business/modals/modal-edicion.php`. Las solicitudes se procesan mediante `api/routes/apiEdicion.php` y `api/controllers/adminEdicion.php`.

## API de edición

`api/routes/apiEdicion.php` acepta solicitudes autenticadas de administradores y devuelve JSON con cabeceras que impiden almacenar la respuesta en caché.

- `GET`: búsqueda paginada o consulta de datos editables del contexto seleccionado.
- `POST`: actualización del registro indicado mediante su contexto, tipo, identificador y campos editados.
- Otros métodos: respuesta `405`.

| Código HTTP | Significado |
| --- | --- |
| `200` | Operación completada. |
| `401` | Token ausente, inválido o vencido. |
| `403` | Rol sin autorización. |
| `404` | Registro inexistente o ajeno al contexto seleccionado. |
| `422` | Datos de edición inválidos. |
| `500` | Error al completar la operación. |

## Instalación y mantenimiento

Publicar los archivos de la aplicación en el directorio elegido, configurar `.env` y ajustar `APP_URL` a esa ubicación. La base de datos debe contar con el esquema de los módulos y las migraciones correspondientes; `database/migrations/001_security.sql` contiene ajustes para autenticación y control de intentos, no el esquema completo.

Conservar el almacenamiento de documentos y las reglas de `.htaccess` al actualizar la aplicación. Usar credenciales propias para cada entorno y mantener `APP_DEBUG=false` cuando no se deban mostrar errores al navegador.

Los scripts `api/script_*.php` contienen procesos de actualización, validación y notificación. Su ejecución depende de la configuración externa del servidor; la aplicación no instala automáticamente un programador de tareas.
