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

// Manejo de creación / eliminación vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    $resp = ['estatus' => 'Error', 'mensaje' => 'Acción inválida'];
    try {
        switch ($accion) {
            case 'crear':
                $idEmpresa = isset($_POST['id']) ? trim($_POST['id']) : '';
                $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
                $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
                $rfc = isset($_POST['rfc']) ? trim($_POST['rfc']) : '';
                if ($idEmpresa === '' || $usuario === '' || $contrasena === '' || $rfc === '') {
                    $resp['mensaje'] = 'Parámetros incompletos (id, usuario, contrasena, rfc)';
                    echo json_encode($resp);
                    exit;
                }
                $adminEmpresa->agregaContraIofacturo($rfc, $contrasena, $usuario, $idEmpresa);
                $resp = ['estatus' => 'Exito', 'mensaje' => 'Cuenta IOFacturo creada correctamente'];
                break;
            case 'eliminar':
                $id = isset($_POST['id']) ? trim($_POST['id']) : '';
                if ($id === '') {
                    $resp['mensaje'] = 'ID requerido para eliminar';
                    echo json_encode($resp);
                    exit;
                }
                $adminEmpresa->eliminarContasIofacturo($id);
                $resp = ['estatus' => 'Exito', 'mensaje' => 'Cuenta IOFacturo eliminada correctamente'];
                break;
            default:
                break;
        }
    } catch (Exception $e) {
        $resp = ['estatus' => 'Error', 'mensaje' => 'Excepción: ' . $e->getMessage()];
    }
    echo json_encode($resp);
    exit;
}

$cuentas = $adminEmpresa->dameCuentasIofact($clave);

if ($search !== '') {
    $cuentas = array_filter($cuentas, function ($row) use ($search) {
        return (
            stripos($row->empresa, $search) !== false ||
            stripos($row->usuario, $search) !== false ||
            stripos($row->contrasena, $search) !== false ||
            stripos($row->rfc, $search) !== false
        );
    });
}

$total = count($cuentas);
$pages = max(1, ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;
$data = array_slice(array_values($cuentas), $offset, $limit);

$out = array_map(function ($row) {
    return [
        'id' => $row->id,
        'empresa' => $row->empresa,
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
