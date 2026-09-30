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

require '../controllers/adminCorreo.php';
require '../utils/encriptador.php';

$accion = $_POST['accion'];
$alta = "alta";
$baja = "baja";
$update = "update";

function altaCorreo()
{
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $empresa = $_POST['empresa'];
    $encriptador = new Encriptador();
    $password = $encriptador->encriptar($password);
    $adminCorreo = new adminCorreo();
    $adminCorreo->agregaCorreo($empresa, $correo, $password);
    return array("mensaje" => "Alta exitosa", "status" => "success");
}

function bajaCorreo()
{
    $id = $_POST['id'];
    $adminCorreo = new adminCorreo();
    $adminCorreo->bajaCorreo($id);
    return array("mensaje" => "Baja exitosa", "status" => "success");
}

function updateCorreo()
{
    $id = $_POST['id'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $empresa = $_POST['empresa'];
    $adminCorreo = new adminCorreo();
    if (count($password) > 0) {
        $encriptador = new Encriptador();
        $password = $encriptador->encriptar($password);
        $adminCorreo->modificaCorreoPassword($id, $password);
    }
    $adminCorreo->modificaCorreo($id, $empresa, $correo);
}

$respuesta = array();
switch ($accion) {
    case $accion: {
            $respuesta = altaCorreo();
        }
        break;
    case $baja: {
            $respuesta = bajaCorreo();
        }
        break;
    case $update: {
            $respuesta = updateCorreo();
        }
        break;
    default: {
            $respuesta = array("mensaje" => "Acción no valida", "status" => "error");
        }
}
return json_encode($respuesta);
