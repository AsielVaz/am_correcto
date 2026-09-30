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


include_once '../controllers/adminTareas.php';


$accion = $_POST['accion'];
$casoAgregarTarea = 'agregarTarea';
$casoObtenerTareas = 'obtenerTareas';
$casoEliminarTarea = 'eliminarTarea';
$casoEliminarTareas = 'eliminarTareas';



function agregarTarea($idEmpresa, $tarea, $campo)
{
    $adminTareas = new AdministradorTareas();
    $result = $adminTareas->agregarTarea($idEmpresa, $tarea, $campo);
    if ($result) {
        echo json_encode(array('status' => 'success', 'message' => 'Tarea agregada correctamente'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo agregar la tarea'));
    }
}


function obtenerTareas()
{
    $adminTareas = new AdministradorTareas();
    $result = $adminTareas->obtenerTareas();
    if ($result) {
        echo json_encode(array('status' => 'success', 'message' => 'Tareas obtenidas correctamente', 'data' => $result));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo obtener las tareas'));
    }
}

function eliminarTarea($idTarea)
{
    $adminTareas = new AdministradorTareas();
    $result = $adminTareas->eliminarTarea($idTarea);
    if ($result) {
        echo json_encode(array('status' => 'success', 'message' => 'Tarea eliminada correctamente'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo eliminar la tarea'));
    }
}

function eliminarTareas()
{
    $adminTareas = new AdministradorTareas();
    $result = $adminTareas->eliminarTareas();
    if ($result) {
        echo json_encode(array('status' => 'success', 'message' => 'Tareas eliminadas correctamente'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo eliminar las tareas'));
    }
}


switch ($accion) {
    case $casoAgregarTarea:
        agregarTarea($_POST['idEmpresa'], $_POST['tarea'], $_POST['campo']);
        break;
    case $casoObtenerTareas:
        obtenerTareas();
        break;
    case $casoEliminarTarea:
        eliminarTarea($_POST['id']);
        break;
    case  $casoEliminarTareas:
        eliminarTareas();
        break;
    default:
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo realizar la acción'));
        break;
}
