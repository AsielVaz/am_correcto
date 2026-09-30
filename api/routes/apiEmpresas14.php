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
require_once __DIR__ . '/../controllers/adminEmpresas14.php';
header('Content-Type: application/json');

$admin = new AdminEmpresas14();

// Parámetros de paginación y filtros
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = [];
if ($search !== '') {
    $search = addslashes($search);
    $where[] = "(b.banco LIKE '%$search%' OR b.numero_cuenta LIKE '%$search%' OR b.clabe_interbancaria LIKE '%$search%' OR b.nombre_corto LIKE '%$search%' OR e.razon LIKE '%$search%')";
}
$where[] = "status_banco != 'BA'";
$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Contar total
$countQuery = "SELECT COUNT(*) as total FROM bancos b INNER JOIN empresas e ON b.razon = e.id $whereSql";
$countResult = $admin->ejecutar($countQuery);
$total = 0;
if ($row = mysqli_fetch_assoc($countResult)) {
    $total = intval($row['total']);
}

// Obtener datos paginados
$offset = ($page - 1) * $limit;
$query = "SELECT b.id, b.banco, b.numero_cuenta, b.clabe_interbancaria, b.nombre_corto, b.moneda, e.razon as empresaN FROM bancos b INNER JOIN empresas e ON b.razon = e.id $whereSql ORDER BY e.razon LIMIT $offset, $limit";
$result = $admin->ejecutar($query);
$cuentas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cuentas[] = [
        'id' => $row['id'],
        'empresa' => $row['empresaN'],
        'banco' => $row['banco'],
        'numero_cuenta' => $row['numero_cuenta'],
        'clabe_interbancaria' => $row['clabe_interbancaria'],
        'nombre_corto' => $row['nombre_corto'],
        'moneda' => $row['moneda']
    ];
}


// Calcular cantidad total de páginas (siempre al menos 1)
$pages = max(1, ceil($total / $limit));

// Respuesta
$response = [
    'success' => true,
    'data' => $cuentas,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'pages' => $pages
];
echo json_encode($response);
