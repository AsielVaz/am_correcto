<?php
header('Content-Type: application/json');
require_once '../controllers/adminEmpresas.php';

try {
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = isset($_GET['limite']) ? max(1, intval($_GET['limite'])) : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

    $admin = new AdministradorEmpresa();
    $empresas = $admin->dameEmpresas();

    // Filtro de búsqueda por nombre de empresa
    if ($busqueda !== '') {
        $empresas = array_filter($empresas, function ($empresa) use ($busqueda) {
            return stripos($empresa->razon, $busqueda) !== false;
        });
    }

    $total = count($empresas);
    $offset = ($pagina - 1) * $limite;
    $resultados = array_slice($empresas, $offset, $limite);

    $data = array_map(function ($empresa) use ($admin) {
        // Carátula reciente
        $caratula = $admin->dameUltiaCaratula($empresa->id);
        $caratulaUrl = $caratula && $caratula->documento1 ? $caratula->documento1 : null;
        // Estado de cuenta reciente
        $estado = $admin->dameUltimoEstadoDeCuenta($empresa->id);
        $estadoUrl = $estado && $estado->documento1 ? $estado->documento1 : null;
        return [
            'empresa' => $empresa->razon,
            'caratula' => $caratulaUrl,
            'estado' => $estadoUrl,
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
