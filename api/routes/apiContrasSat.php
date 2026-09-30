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
header('Content-Type: application/json');
include_once('../controllers/adminEmpresas.php');
include_once('../utils/encriptador.php');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$clave = isset($_GET['clave']) ? $_GET['clave'] : '';

$adminEmpresa = new AdministradorEmpresa();
$encriptador = new Encriptador();

// Obtener todas las contraseñas SAT (puedes filtrar por empresa si lo deseas)
$contras = $adminEmpresa->dameContrasSat($clave);

// Filtrar por búsqueda
if ($search !== '') {
    $contras = array_filter($contras, function ($row) use ($search) {
        return (
            stripos($row->usuario, $search) !== false ||
            stripos($row->contrasena, $search) !== false ||
            stripos($row->rfc, $search) !== false ||
            stripos($row->nombreEmpresa, $search) !== false
        );
    });
}

$total = count($contras);
$pages = max(1, ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;
$data = array_slice(array_values($contras), $offset, $limit);

// Formatear salida
$out = array_map(function ($row) {
    return [
        'id' => $row->id,
        'empresa' => isset($row->nombreEmpresa) ? $row->nombreEmpresa : $row->empresa,
        'usuario' => $row->usuario,
        'contrasena' => $row->contrasena,
        'rfc' => $row->rfc
    ];
}, $data);

$response = [
    'data' => $out,
    'page' => $page,
    'pages' => $pages,
    'total' => $total,
    'limit' => $limit
];
echo json_encode($response);
