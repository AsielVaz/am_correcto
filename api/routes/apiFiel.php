<?php
require_once __DIR__ . '/../controllers/adminEmpresas.php';
require_once __DIR__ . '/../utils/auth_middleware.php';
header('Content-Type: application/json');

try {
    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $clave = isset($_GET['clave']) ? $_GET['clave'] : '';

    $adminEmpresas = new AdministradorEmpresa();
    $fiels = $adminEmpresas->dameFiels($clave); // array de objetos Fiel

    // Filtrar por búsqueda (empresa)
    if ($busqueda !== '') {
        $fiels = array_filter($fiels, function ($fiel) use ($busqueda) {
            return stripos($fiel->empresaNom, $busqueda) !== false;
        });
    }
    $total = count($fiels);
    $fiels = array_slice(array_values($fiels), ($pagina - 1) * $limite, $limite);

    $resultados = [];
    foreach ($fiels as $fiel) {
        $resultados[] = [
            'empresa' => $fiel->empresaNom,
            'tipo' => $fiel->tipo,
            'fecha' => $fiel->fechaCreacion,
            'contrasena' => $adminEmpresas->dameContraFiel($fiel->empresa),
            'documento' => $fiel->documento1
        ];
    }

    $response = [
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite,
        'resultados' => $resultados
    ];
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
