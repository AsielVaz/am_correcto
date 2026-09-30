<?php
include_once('../utils/auth_guard.php');

requireAuth();
ensureAdmin();

header('Content-Type: application/json');
include_once('../controllers/adminUsuarios.php');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$adminUsuario = new AdministradorUsuario();

if ($search !== '') {
    $usuarios = $adminUsuario->buscaUsuario($search);
} else {
    $usuarios = $adminUsuario->dameUsuarios();
}

$total = count($usuarios);
$pages = max(1, ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;
$data = array_slice(array_values($usuarios), $offset, $limit);

$out = array_map(function ($row) {
    return [
        'id' => $row->id,
        'nombre' => $row->nombre,
        'apellidoPaterno' => $row->apellidoPaterno,
        'apellidoMaterno' => $row->apellidoMaterno,
        'email' => $row->email,
        'telefono' => $row->telefono,
        'calle' => $row->calle,
        'ciudad' => $row->ciudad,
        'pais' => $row->pais,
        'direccion' => $row->direccion,
        'tipoUsuario' => $row->tipoUsuario,
        'rol' => $row->rol,
        'departamento' => isset($row->departamento) ? $row->departamento : null,
        'imagen' => $row->imagen,
        'permisos' => $row->permisos,
        'activo' => isset($row->activo) ? $row->activo : 1
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
