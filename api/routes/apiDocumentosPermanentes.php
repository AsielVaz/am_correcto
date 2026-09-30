
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
    $empresas = $adminEmpresas->dameEmpresas();
    if ($busqueda !== '') {
        $empresas = array_filter($empresas, function ($empresa) use ($busqueda) {
            return stripos($empresa->razon, $busqueda) !== false;
        });
    }
    $total = count($empresas);
    $empresas = array_slice(array_values($empresas), ($pagina - 1) * $limite, $limite);

    $resultados = [];
    foreach ($empresas as $empresa) {
        $id = $empresa->id;
        $razon = $empresa->razon;
        $acta = method_exists($adminEmpresas, 'dameActasConstitutivasPorEmpresa') ? $adminEmpresas->dameActasConstitutivasPorEmpresa($id, $clave) : null;
        $asamblea = method_exists($adminEmpresas, 'dameAsambleaPorEmpresa') ? $adminEmpresas->dameAsambleaPorEmpresa($id, $clave) : null;
        $poder = method_exists($adminEmpresas, 'dameDocumentoPoderPorEmpresa') ? $adminEmpresas->dameDocumentoPoderPorEmpresa($id, $clave) : null;
        $ine_rep = method_exists($adminEmpresas, 'dameIneRepPorEmpresa') ? $adminEmpresas->dameIneRepPorEmpresa($id, $clave) : null;
        $ine_socios = method_exists($adminEmpresas, 'dameIneSocioPorEmpresa') ? $adminEmpresas->dameIneSocioPorEmpresa($id, $clave) : null;
        $const_rep = method_exists($adminEmpresas, 'dameConsRepPorEmpresa') ? $adminEmpresas->dameConsRepPorEmpresa($id, $clave) : null;
        $const_socios = method_exists($adminEmpresas, 'dameConsSocioPorEmpresa') ? $adminEmpresas->dameConsSocioPorEmpresa($id, $clave) : null;

        $resultados[] = [
            'empresa' => $razon,
            'acta_constitutiva' => (is_array($acta) && isset($acta[0])) ? $acta[0]->documento : null,
            'rppc_acta' => null,
            'asamblea' => (is_array($asamblea) && isset($asamblea[0])) ? $asamblea[0]->documento : null,
            'rppc_asamblea' => null,
            'poder' => (is_array($poder) && isset($poder[0])) ? $poder[0]->documento : null,
            'ine_representante' => (is_array($ine_rep) && isset($ine_rep[0])) ? $ine_rep[0]->documento : null,
            'ine_socios' => (is_array($ine_socios) && isset($ine_socios[0])) ? $ine_socios[0]->documento : null,
            'constancia_rep' => (is_array($const_rep) && isset($const_rep[0])) ? $const_rep[0]->documento : null,
            'constancia_socios' => (is_array($const_socios) && isset($const_socios[0])) ? $const_socios[0]->documento : null,
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
