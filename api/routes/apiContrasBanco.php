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
require_once __DIR__ . '/../controllers/adminEmpresas.php';
header('Content-Type: application/json');

$admin = new AdministradorEmpresa();

// Parámetros de paginación y búsqueda
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$clave = isset($_GET['clave']) ? $_GET['clave'] : '';
$where = [];
if ($search !== '') {
    $search = addslashes($search);
    $where[] = "(emp.razon LIKE '%$search%' OR cb.banco LIKE '%$search%' OR cb.usuario LIKE '%$search%' OR cb.id LIKE '%$search%')";
}
$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Contar total
$countQuery = "SELECT COUNT(*) as total FROM contras_banco cb LEFT JOIN empresas emp ON cb.id_empresa = emp.id $whereSql";
$countResult = $admin->ejecutar($countQuery);
$total = 0;
if ($row = mysqli_fetch_assoc($countResult)) {
    $total = intval($row['total']);
}

// Obtener datos paginados
$offset = ($page - 1) * $limit;
$query = "SELECT cb.id, cb.usuario, cb.contrasena, cb.clave_op, cb.nip, cb.banco, emp.razon as empresa FROM contras_banco cb LEFT JOIN empresas emp ON cb.id_empresa = emp.id $whereSql ORDER BY emp.razon ASC LIMIT $offset, $limit";
$result = $admin->ejecutar($query);
$encriptador = new Encriptador();
$contras = [];
while ($row = mysqli_fetch_assoc($result)) {
    $contras[] = [
        'id' => $row['id'],
        'usuario' => $row['usuario'],
        'contrasena' => $encriptador->desencriptar($row['contrasena'], $clave),
        'claveOp' => $encriptador->desencriptar($row['clave_op'], $clave),
        'nip' => $encriptador->desencriptar($row['nip'], $clave),
        'banco' => $row['banco'],
        'empresa' => $row['empresa']
    ];
}

$pages = max(1, ceil($total / $limit));
$response = [
    'success' => true,
    'data' => $contras,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'pages' => $pages
];
echo json_encode($response);
