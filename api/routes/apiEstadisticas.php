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

include_once("../controllers/adminEstadisticas.php");

$accion = $_POST['accion'];
$casoVerPorAnio = "verPorAnio";


function verEstadisticasPorAnio()
{
    $adminEstadisticas = new AdministradorEstadisticas();
    $anioVisitas = array();
    for ($i = 0; $i < 12; $i++) {
        $anioVisitas[$i] = $adminEstadisticas->cuentaVisitasTotalesPorMes(date("Y"), ($i + 1));
    }
    echo json_encode($anioVisitas);
}


switch ($accion) {
    case $casoVerPorAnio:
        verEstadisticasPorAnio();
        break;
}

//verEstadisticasPorAnio();
