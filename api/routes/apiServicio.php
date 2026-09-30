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
include_once("../controllers/adminServicio.php");

$accion = $_POST['accion'];

$casoAgregar = "agregar";
$casoEliminar = "eliminar";
$casoModificar = "modificar";



function procesarImagen()
{

    $carpetaDestino = '/Imagenes/Servicios/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['archivo']['name']);
    $tipo_Imgaen = $_FILES['archivo']['type'];
    $tamanio_imagen = $_FILES['archivo']['size'];

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprobadores de peso y tipo de imagen

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

function agregarServicio()
{
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen = procesarImagen();
    $adminServicio = new AdministradorServicios();
    $adminServicio->agregarServicio($nombre, $descripcion, $imagen);
    echo "1";
}

function eliminarServicio()
{
    $id = $_POST['id'];
    $adminServicio = new AdministradorServicios();
    $adminServicio->eliminarServicio($id);
    echo "1";
}

function modificarServicio()
{
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen = procesarImagen();
    $adminServicio = new AdministradorServicios();
    $adminServicio->modificarServicio($id, $nombre, $descripcion, $imagen);
    echo "1";
}

switch ($accion) {
    case $casoAgregar:
        agregarServicio();
        break;
    case $casoEliminar:
        eliminarServicio();
        break;
    case $casoModificar:
        modificarServicio();
        break;
}
