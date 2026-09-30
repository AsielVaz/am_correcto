<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../utils/jwt_helper.php';
require_once __DIR__ . '/../utils/auth_guard.php';

$accion = isset($_POST['accion']) ? $_POST['accion'] : null;

if (!$accion) {
  respondWithError(400, 'Acción requerida');
}

if ($accion !== 'inicio') {
  requireAuth();
  ensureAdmin();
}

require_once __DIR__ . '/../controllers/adminUsuarios.php';
require_once __DIR__ . '/../controllers/adminLoginLog.php';
require_once __DIR__ . '/../utils/limite_intentos.php';
require_once __DIR__ . '/../utils/validarCaracteres.php';

$accion = $_POST['accion'];
$casoAgregar = "agregar";
$casoEliminar = "eliminar";
$casoModificar = "modificar";
$casoVerRegistros = "ver";
$casoInicio = "inicio";
$casoVerUnRegistro  = "verUno";
$casoActualizar = "actualizar";
$casoDireccion = "direccion";
$casoSubirImagen = "imagen";
$casoRecuperar = "recuperar-contrasena";
$casoAcender = "acender";
$casoDecender = "desender";
$casoDeshabilitar = "deshabilitar";

function procesarDeshabilitar()
{
  $id = $_POST['id'];
  $admin = new AdministradorUsuario();
  $admin->deshabilitaUsuario($id);
  echo "1";
}


function GUIDv4($trim = true)
{
  // Windows
  if (function_exists('com_create_guid') === true) {
    if ($trim === true)
      return trim(com_create_guid(), '{}');
    else
      return com_create_guid();
  }

  // OSX/Linux
  if (function_exists('openssl_random_pseudo_bytes') === true) {
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  // Fallback (PHP 4.2+)
  mt_srand((float)microtime() * 10000);
  $charid = strtolower(md5(uniqid(rand(), true)));
  $hyphen = chr(45);                  // "-"
  $lbrace = $trim ? "" : chr(123);    // "{"
  $rbrace = $trim ? "" : chr(125);    // "}"
  $guidv4 = $lbrace .
    substr($charid,  0,  8) . $hyphen .
    substr($charid,  8,  4) . $hyphen .
    substr($charid, 12,  4) . $hyphen .
    substr($charid, 16,  4) . $hyphen .
    substr($charid, 20, 12) .
    $rbrace;
  return $guidv4;
}

function normalizarRol($valor)
{
  if (!is_string($valor)) {
    return 'Usuario';
  }
  $map = [
    'usuario' => 'Usuario',
    'capturista' => 'Capturista',
    'admin' => 'Admin',
    'administrador' => 'Admin'
  ];
  $clave = strtolower(trim($valor));
  return $map[$clave] ?? 'Usuario';
}



function procesarImagen($id)
{

  $carpetaDestino = '/Imagenes/Usuarios/';
  $pesoMaxicoImagen = 2000000;
  $nombreCompuestoImagen = "default.txt";
  $nombre_imagen = basename($_FILES['archivo']['name']);
  $tipo_Imgaen = $_FILES['archivo']['type'];
  $tamanio_imagen = $_FILES['archivo']['size'];

  $casoPng = ".png";
  $casoJpeg = ".jpg";
  //comprovadores de peso y tipo de imagen

  if ($tamanio_imagen < $pesoMaxicoImagen) {
    if ($tipo_Imgaen == "image/jpeg" || $tipo_Imgaen == "image/png") {
      //mueve la imagen a la carpeta seleccionada 
      move_uploaded_file($_FILES['archivo']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
      chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
      switch ($tipo_Imgaen) {
        case "image/jpeg":
          $nombreCompuestoImagen = $carpetaDestino . "Imagen" . $id . $casoJpeg;
          break;
        case "image/png":
          $nombreCompuestoImagen = $carpetaDestino . "Imagen" . $id .  $casoPng;
          break;
      }
      rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
      chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
      return $nombreCompuestoImagen;
    } else {
      echo json_encode("El formato de la imagen no esta permitido");
    }
  } else {
    echo json_encode("La imagen supera el tamaño establecido");
  }
}

function procesarAlta()
{
  $nombre = $_POST['nombre'];
  $apellidoPaterno = $_POST['appat'];
  $apellidoMaterno = $_POST['apmat'];
  $email = $_POST['email'];
  $rol = normalizarRol($_POST['rol'] ?? '');
  $contrasena = $_POST['pass'];
  $departamento = isset($_POST['departamento']) ? $_POST['departamento'] : null;
  $tipo = 0;
  $empleado = isset($_POST['empleado']) ? $_POST['empleado'] : "0";
  $permisos = "";
  if (isset($_POST['RolAdmin'])) {
    $permisos .= "admin,";
  }
  if (isset($_POST['RolCap'])) {
    $permisos .= "cap,";
  }
  if (isset($_POST['RolCont1'])) {
    $permisos .= "cont1,";
  }
  if (isset($_POST['RolCont2'])) {
    $permisos .= "cont2,";
  }
  if (isset($_POST['RolFact'])) {
    $permisos .= "fact,";
  }
  if (isset($_POST['RolBancos'])) {
    $permisos .= "ban,";
  }
  if (isset($_POST['RodDocumentos'])) {
    $permisos .= "doc,";
  }
  $permisos .= "user";
  $admin = new AdministradorUsuario();
  $admin->insertaUsuario($nombre, $apellidoPaterno, $apellidoMaterno, $email, $contrasena, "3333333333", "No definido", "No definido", "No definido", "No definido", 50, $rol, $departamento, $empleado, GUIDv4(), $permisos);
  echo "1";
}

function procesarActualizacion()
{
  $validarCaracteres = new ValidarCaracteres();
  $id = $_POST['id'];
  $nombre = $validarCaracteres->validarTexto($_POST['nombre']);
  $apellidoPaterno = $validarCaracteres->validarTexto($_POST['appat']);
  $apellidoMaterno = $validarCaracteres->validarTexto($_POST['apmat']);
  $email = $_POST['email'];
  $rol = normalizarRol($_POST['rol'] ?? '');
  $departamento = isset($_POST['departamento']) ? $_POST['departamento'] : null;
  $permisos = "";
  if (isset($_POST['RolAdmin'])) {
    $permisos .= "admin,";
  }
  if (isset($_POST['RolCap'])) {
    $permisos .= "cap,";
  }
  if (isset($_POST['RolCont1'])) {
    $permisos .= "cont1,";
  }
  if (isset($_POST['RolCont2'])) {
    $permisos .= "cont2,";
  }
  if (isset($_POST['RolFact'])) {
    $permisos .= "fact,";
  }
  if (isset($_POST['RodDocumentos'])) {
    $permisos .= "doc,";
  }
  if (isset($_POST['RolBancos'])) {
    $permisos .= "ban,";
  }
  $permisos .= "user";
  $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
  $admin = new AdministradorUsuario();
  $admin->actualizarUsuario($id, $nombre, $apellidoPaterno, $apellidoMaterno, $email, "No definido", "No definido", "No definido", "No definido", "No definido", $permisos, $rol, $departamento, $activo);
  echo "1";
}

function procesarActualizacionContrasena()
{
  $validarCaracteres = new ValidarCaracteres();
  $id = $_POST['id'];
  $token = $_POST['token'];
  $pass = $validarCaracteres->validarContrasena($_POST['pass']);
  if ($pass) {
    $admin = new AdministradorUsuario();
    $admin->actualizarContrasena($id, $pass);
    $admin->mataToken($token);
    echo "1";
  } else {
    echo "0";
  }
}

function procesarActualizacionDireccion()
{

  $id = $_POST['id'];
  $direccion = $_POST['direccion'];
  $calle = $_POST['calle'];
  $ciudad = $_POST['ciudad'];
  $pais = $_POST['pais'];
  $email = $_POST['email'];
  $admin = new AdministradorUsuario();
  $admin->actualizarDireccion($id, $email, $calle, $ciudad, $pais, $direccion);
  echo "1";
}

function procesarBaja()
{
  $id = $_POST['id'];
  $admin = new AdministradorUsuario();
  $admin->eliminaUsuario($id);
  echo "1";
}

function procesarAcender()
{
  $id = $_POST['id'];
  $admin = new AdministradorUsuario();
  $admin->acenderUsuario($id);
  echo "1";
}

function procesarDesender()
{
  $id = $_POST['id'];
  $admin = new AdministradorUsuario();
  $admin->desenderUsuario($id);
  echo "1";
}

function procesarImagenUsuario()
{
  $id = $_POST['id'];
  $imagen = procesarImagen($id);
  $admin = new AdministradorUsuario();
  $admin->asignarImagen($id, $imagen);
  echo "1";
}

function inicioSesion()
{
  try {
    $email = $_POST['email'];
    $constrasena = $_POST['pass'];
    $admin = new AdministradorUsuario();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $idFallido = $admin->existeUsuario($email);
    $limite = new LimiteIntentos();
    $loginLog = new AdminLoginLog();
    if ($limite->excedido($idFallido ? $idFallido : null, $ip)) {
      $loginLog->registrar($idFallido ? $idFallido : null, $ip, 0, 'ban');
      echo json_encode(['error' => 'Demasiados intentos fallidos. Intenta de nuevo en 1 hora.']);
      return;
    }
    $usuario = $admin->dameUsuario($email, $constrasena);
    if (!$usuario || !isset($usuario->id)) {
      $loginLog->registrar($idFallido ? $idFallido : null, $ip, 0);
      echo json_encode(['error' => 'Usuario o contraseña incorrectos']);
      return;
    }
    if ($usuario->id == -1) {
      // Usuario inactivo
      $loginLog->registrar($idFallido ? $idFallido : null, $ip, 0, 'inactivo');
      echo json_encode(['error' => 'El usuario está inactivo. Contacta al administrador.']);
      return;
    }
    if ($usuario->id != 0) {
      $loginLog->registrar($usuario->id, $ip, 1);
      // Generar JWT
      $payload = [
        'id' => $usuario->id,
        'email' => $usuario->email,
        'permisos' => $usuario->permisos,
        'rol' => $usuario->rol,
        'departamento' => $usuario->departamento
      ];
      $token = JwtHelper::generar($payload);
      $expire = time() + (int) env('JWT_TTL', 3600);
      $respuestas = array(
        'id' => $usuario->id,
        'tipoUsuario' => $usuario->tipoUsuario,
        'permisos' => $usuario->permisos,
        'rol' => $usuario->rol,
        'departamento' => $usuario->departamento,
        'token' => $token,
        'expire' => $expire
      );
      echo json_encode($respuestas);
    } else {
      $loginLog->registrar($idFallido ? $idFallido : null, $ip, 0);
      echo json_encode(['error' => 'Usuario o contraseña incorrectos']);
    }
  } catch (Throwable $e) {
    http_response_code(500);
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['error' => 'No fue posible iniciar sesión. Intenta nuevamente.']);
  }
}

switch ($accion) {
  case $casoAgregar:

    procesarAlta();

    break;
  case $casoActualizar:
    procesarActualizacion();
    break;
  case $casoEliminar:
    procesarBaja();
    break;
  case $casoDeshabilitar:
    procesarDeshabilitar();
    break;
  case $casoInicio:
    inicioSesion();
    break;
  case $casoVerUnRegistro:
    break;
  case $casoDireccion:
    procesarActualizacionDireccion();
    break;
  case $casoSubirImagen:
    procesarImagenUsuario();
    break;
  case $casoRecuperar:
    procesarActualizacionContrasena();
    break;
  case $casoAcender:
    procesarAcender();
    break;
  case $casoDecender:
    procesarDesender();
    break;
  default:
    echo '0';
    break;
}
