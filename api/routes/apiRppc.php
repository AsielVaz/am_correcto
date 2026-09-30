<?php
header('Content-Type: application/json');
require_once '../controllers/adminEmpresas.php';

try {
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = isset($_GET['limite']) ? max(1, intval($_GET['limite'])) : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $clave = isset($_GET['clave']) ? $_GET['clave'] : '';

    $admin = new AdministradorEmpresa();
    $rppc = $admin->dameRppd($clave);

    // Filtro de búsqueda por nombre de empresa
    if ($busqueda !== '') {
        $rppc = array_filter($rppc, function ($item) use ($busqueda) {
            return stripos($item->empresaNom, $busqueda) !== false;
        });
    }

    $total = count($rppc);
    $offset = ($pagina - 1) * $limite;
    $resultados = array_slice($rppc, $offset, $limite);

    // Formatear salida para frontend
    $data = array_map(function ($item) {
        return [
            'empresa' => $item->empresaNom,
            'fecha' => $item->fechaCreacion,
            'contenido' => $item->contenido,
            'documento' => $item->documento,
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
