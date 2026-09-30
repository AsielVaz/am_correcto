<?php
include_once('../utils/jwt_helper.php');
$headers = getallheaders();
$token = null;
if (isset($headers['Authorization'])) {
    $auth = $headers['Authorization'];
    if (stripos($auth, 'Bearer ') === 0) {
        $token = trim(substr($auth, 7));
    }
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
}
if (!$token || !JwtHelper::validar($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o expirado']);
    exit;
}

include_once('../controllers/adminProyecto.php');

$accion = $_POST['accion'];

$casoAgregar = "agregar";
$casoEliminar = "eliminar";
$casoModificar = "modificar";
$casoAsignar = "asignar";
$casoDesasignar = "desasignar";

function procesarImagen()
{

    $carpetaDestino = '/Imagenes/Proyectos/';
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
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . rand(0, 40000) . $casoJpeg;
                    break;
                case "image/png":
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . rand(0, 40000) .  $casoPng;
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


function agregarProyecto()
{
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen = procesarImagen();
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $presupuesto = $_POST['presupuesto'];
    $empresa = $_POST['empresa'];
    $tipoProyecto = $_POST['tipoProyecto'];
    $sitioWeb = $_POST['sitioWeb'];
    $adminProyecto = new AdministradorProyecto();
    $adminProyecto->agregarProyecto($nombre, $descripcion, $imagen, $fechaInicio, $fechaFin, $presupuesto, $empresa, $tipoProyecto, $sitioWeb);
    echo "1";
}

function casoEliminarProyecto()
{
    $id = $_POST['id'];
    $adminProyecto = new AdministradorProyecto();
    $adminProyecto->eliminarProyecto($id);
    echo "1";
}

function casoModificarProyecto()
{
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen'];
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $presupuesto = $_POST['presupuesto'];
    $empresa = $_POST['empresa'];
    $tipoProyecto = $_POST['tipoProyecto'];
    $sitioWeb = $_POST['sitioWeb'];
    $adminProyecto = new AdministradorProyecto();
    $adminProyecto->modificarProyecto($id, $nombre, $descripcion, $imagen, $fechaInicio, $fechaFin, $presupuesto, $empresa, $tipoProyecto, $sitioWeb);
    echo "1";
}

function asignarEmpleado()
{
    $id = $_POST['id'];
    $empleado = $_POST['empleado'];
    $adminProyecto = new AdministradorProyecto();
    $adminProyecto->asignarEmpleado($id, $empleado);
    echo "1";
}

function desAginarEmpleado()
{
    $id = $_POST['id'];
    $empleado = $_POST['empleado'];
    $adminProyecto = new AdministradorProyecto();
    $adminProyecto->desAsignarEmpleadoPorProyecto($id, $empleado);
    echo "1";
}

switch ($accion) {
    case $casoAgregar:
        agregarProyecto();
        break;
    case $casoEliminar:
        casoEliminarProyecto();
        break;
    case $casoAsignar:
        asignarEmpleado();
        break;
    case $casoDesasignar:
        desAginarEmpleado();
        break;
    case $casoModificar:
        casoModificarProyecto();
        break;
}
