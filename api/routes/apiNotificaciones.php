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

include_once("../controllers/adminNotificaciones.php");


$accion = $_POST['accion'];
$casoEliminar = "eliminar";
$casoMarcarCompletada = "completada";
$casoPosponer = "posponer";



function eliinarNotificacion()
{
    $id = $_POST['id'];
    $adminNotificaciones = new AdminNotificaciones();
    $adminNotificaciones->eliminarNotificacion($id);
    echo "1";
}

function marcarCompletada()
{
    $id = $_POST['id'];
    $adminNotificaciones = new AdminNotificaciones();
    $adminNotificaciones->marcarComoCompletada($id);
    echo "1";
}

function posponerNotificacion()
{
    $id = $_POST['id'];
    $adminNotificaciones = new AdminNotificaciones();
    $adminNotificaciones->posponerNotificacion($id);
    echo "1";
}

switch ($accion) {
    case $casoEliminar:
        eliinarNotificacion();
        break;
    case $casoMarcarCompletada:
        marcarCompletada();
        break;
    case $casoPosponer:
        posponerNotificacion();
        break;
    default:
        echo "0";
        break;
}
