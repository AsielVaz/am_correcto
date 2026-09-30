<?php
header('Content-Type: application/json');
require_once '../controllers/adminEmpresas.php';

try {
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = isset($_GET['limite']) ? max(1, intval($_GET['limite'])) : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $clave = isset($_GET['clave']) ? $_GET['clave'] : '';

    $admin = new AdministradorEmpresa();
    $sellos = $admin->dameSellosSat($clave);

    // Filtro de búsqueda por nombre de empresa
    if ($busqueda !== '') {
        $sellos = array_filter($sellos, function ($sello) use ($busqueda) {
            return stripos($sello->empresaNom, $busqueda) !== false;
        });
    }

    $total = count($sellos);
    $offset = ($pagina - 1) * $limite;
    $resultados = array_slice($sellos, $offset, $limite);


    // Formatear salida para frontend (incluyendo contraseña)
    $data = array_map(function ($sello) use ($admin) {
        $contrasena = $admin->dameContraSelloSat($sello->empresa);
        return [
            'empresa' => $sello->empresaNom,
            'fecha' => $sello->fechaCreacion,
            'contrasena' => $contrasena,
            'documento' => $sello->documento1,
        ];
    }, $resultados);

    echo json_encode([
        'resultados' => $data,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
