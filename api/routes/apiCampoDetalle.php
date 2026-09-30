<?php
include_once('../utils/auth_guard.php');

requireAuth();

header('Content-Type: application/json');

include_once('../controllers/adminEmpresas.php');
include_once('../controllers/adminCamposPostergados.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$campo = isset($_GET['campo']) ? trim($_GET['campo']) : '';

if (!$id || $campo === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Parámetros inválidos'
    ]);
    exit;
}

$campoConfig = [
    'comprobanteDomicilio' => [
        'label' => 'Comprobante de domicilio',
        'documentKey' => 'comprobanteDom',
        'periodKey' => 'periodoComprobanteDom',
        'ultimaKey' => null
    ],
    'constanciaSituacionFiscal' => [
        'label' => 'Constancia de situación fiscal',
        'documentKey' => 'constanciaSf',
        'periodKey' => 'periodoConstancia',
        'ultimaKey' => 'ultimaConstancia'
    ],
    '32D' => [
        'label' => '32D',
        'documentKey' => 'pdf',
        'periodKey' => 'periodo32d',
        'ultimaKey' => 'ultimoPdf'
    ],
    'telefono' => [
        'label' => 'Teléfono',
        'documentKey' => 'telefono',
        'periodKey' => null,
        'ultimaKey' => null
    ],
    'correoContacto' => [
        'label' => 'Correo de contacto',
        'documentKey' => 'correo',
        'periodKey' => null,
        'ultimaKey' => null
    ],
    'sitioWeb' => [
        'label' => 'Sitio web',
        'documentKey' => 'sitioWeb',
        'periodKey' => null,
        'ultimaKey' => null
    ],
];

if (!isset($campoConfig[$campo])) {
    echo json_encode([
        'success' => false,
        'error' => 'Campo no permitido'
    ]);
    exit;
}

try {
    $adminEmpresas = new AdministradorEmpresa();
    $adminPostergados = new AdministradorCamposPostergados();

    $empresa = $adminEmpresas->dameEmpresa($id);
    if (!$empresa) {
        echo json_encode([
            'success' => false,
            'error' => 'Empresa no encontrada'
        ]);
        exit;
    }

    $config = $campoConfig[$campo];
    $documento = isset($empresa->{$config['documentKey']}) ? $empresa->{$config['documentKey']} : null;
    $periodo = isset($empresa->{$config['periodKey']}) ? $empresa->{$config['periodKey']} : null;
    $ultima = $config['ultimaKey'] && isset($empresa->{$config['ultimaKey']}) ? $empresa->{$config['ultimaKey']} : null;

    $totalPostergaciones = $adminPostergados->contarPostergaciones($id, $campo);
    $historial = $adminPostergados->dameHistorialPostergacion($id, $campo);

    echo json_encode([
        'success' => true,
        'data' => [
            'campo' => $campo,
            'label' => $config['label'],
            'documento' => $documento,
            'periodo' => $periodo,
            'ultimaActualizacion' => $ultima,
            'postergaciones' => [
                'total' => $totalPostergaciones,
                'maximo' => 3,
                'restante' => max(0, 3 - $totalPostergaciones),
                'historial' => $historial
            ]
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor'
    ]);
}
