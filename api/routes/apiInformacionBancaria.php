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

$admin = new AdministradorEmpresa();

// Parámetros de paginación y búsqueda
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Obtener todas las empresas
$empresas = $admin->dameEmpresas();

// Filtrar por búsqueda (razón social)
if ($search !== '') {
    $empresas = array_filter($empresas, function ($e) use ($search) {
        return stripos($e->razon, $search) !== false;
    });
}

$total = count($empresas);
$empresas = array_values($empresas); // Reindexar

// Paginación
$start = ($page - 1) * $perPage;
$paged = array_slice($empresas, $start, $perPage);

// Para cada empresa, obtener carátula y estado de cuenta más recientes
$rows = array_map(function ($e) use ($admin) {
    // Carátula reciente
    $caratula = $admin->dameUltiaCaratula($e->id);
    $caratulaDoc = ($caratula && isset($caratula->documento1)) ? $caratula->documento1 : null;
    // Estado de cuenta reciente
    $estado = $admin->dameUltimoEstadoDeCuenta($e->id);
    $estadoDoc = ($estado && isset($estado->documento1)) ? $estado->documento1 : null;
    return [
        'empresa' => $e->razon,
        'caratula' => $caratulaDoc,
        'estadoCuenta' => $estadoDoc
    ];
}, $paged);

echo json_encode([
    'success' => true,
    'data' => $rows,
    'total' => $total,
    'page' => $page,
    'perPage' => $perPage
]);
