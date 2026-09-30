<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conectorBD.php';
require_once __DIR__ . '/../controllers/adminEmpresas.php';

function getEmpresaDocumentos($adminEmpresas, $empresa)
{
    // 32D vigente
    $doc32d = $empresa->pdf ?? null;
    // Comprobante de domicilio
    $comprobante = $empresa->comprobanteDom ?? null;
    // Constancia de situación fiscal
    $constancia = $empresa->constanciaSf ?? null;
    // Carátula reciente
    $caratula = null;
    $caratulaObj = $adminEmpresas->dameUltiaCaratula($empresa->id);
    if ($caratulaObj && isset($caratulaObj->documento)) {
        $caratula = $caratulaObj->documento;
    }
    // Estado de cuenta reciente
    $estado = null;
    $estadoObj = $adminEmpresas->dameUltimoEstadoDeCuenta($empresa->id);
    if ($estadoObj && isset($estadoObj->documento)) {
        $estado = $estadoObj->documento;
    }
    return [
        '32d' => $doc32d,
        'comprobante_domicilio' => $comprobante,
        'constancia_fiscal' => $constancia,
        'caratula' => $caratula,
        'estado_cuenta' => $estado,
    ];
}
try {
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = isset($_GET['limite']) ? max(1, intval($_GET['limite'])) : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $offset = ($pagina - 1) * $limite;

    $adminEmpresas = new AdministradorEmpresa();
    $empresas = $adminEmpresas->dameEmpresas();

    // Filtrar por búsqueda (razon social)
    if ($busqueda !== '') {
        $empresas = array_filter($empresas, function ($empresa) use ($busqueda) {
            return stripos($empresa->razon, $busqueda) !== false;
        });
    }
    $total = count($empresas);
    $empresas = array_slice(array_values($empresas), $offset, $limite);

    $resultados = [];
    foreach ($empresas as $empresa) {
        $docs = getEmpresaDocumentos($adminEmpresas, $empresa);
        $resultados[] = [
            'empresa' => $empresa->razon,
            'correo' => $empresa->correo ?? '',
            'sitio_web' => $empresa->sitioWeb ?? '',
            'documento_32d' => $docs['32d'],
            'comprobante_domicilio' => $docs['comprobante_domicilio'],
            'constancia_fiscal' => $docs['constancia_fiscal'],
            'caratula' => $docs['caratula'],
            'estado_cuenta' => $docs['estado_cuenta'],
        ];
    }

    echo json_encode([
        'resultados' => $resultados,
        'pagina' => $pagina,
        'limite' => $limite,
        'total' => $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
