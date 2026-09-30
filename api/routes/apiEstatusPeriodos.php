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

include_once('../controllers/adminEstatusPeriodos.php');


$accion = $_POST['accion'];

$casoActualizarEstatusPeriodo = 'actualizarEstatusPeriodo';


function actualizarEstatusPeriodo()
{
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $estatus = $_POST['estatus'];
    $adminEstatusPeriodos = new AdministradorEstatusPeriodos();
    $adminEstatusPeriodos->actualizarEstatusPeriodo($id, $estado, $estatus);
    if ($adminEstatusPeriodos->actualizarEstatusPeriodo($id, $estado, $estatus)) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Estatus actualizado correctamente'
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Error al actualizar el estatus'
        ));
    }
}

switch ($accion) {
    case $casoActualizarEstatusPeriodo:
        actualizarEstatusPeriodo();
        break;
}
