<?php
include_once('../utils/auth_guard.php');

requireAuth();

header('Content-Type: application/json');
include_once('../controllers/adminEmpresas.php');

$CAMPO_POSTERGADO_MAP = [
    'comprobanteDom' => 'comprobanteDomicilio',
    'constanciaSf' => 'constanciaSituacionFiscal',
    'pdf' => '32D',
    'finDominio' => 'finDominio',
    'telefono' => 'telefono',
    'correo' => 'correoContacto',
    'sitioWeb' => 'sitioWeb',
];

$BASE_URGENCIA_MESES = [
    'comprobanteDom' => 3,
    'constanciaSf' => 2,
    'pdf' => 1,
];

function extraerFechaDesdePeriodo($registro)
{
    if (!$registro || !is_string($registro)) {
        return null;
    }

    if (preg_match('/(\d{4}-\d{2}-\d{2})$/', $registro, $coincidencias)) {
        $fecha = $coincidencias[1];
        return $fecha === '0000-00-00' ? null : $fecha;
    }

    return null;
}

function obtenerFechaCampoBase($empresa, $campo)
{
    switch ($campo) {
        case 'comprobanteDom':
            return extraerFechaDesdePeriodo($empresa->periodoComprobanteDom);
        case 'constanciaSf':
            if ($empresa->ultimaConstancia && $empresa->ultimaConstancia !== '0000-00-00') {
                return $empresa->ultimaConstancia;
            }
            return extraerFechaDesdePeriodo($empresa->periodoConstancia);
        case 'pdf':
            if ($empresa->ultimoPdf && $empresa->ultimoPdf !== '0000-00-00') {
                return $empresa->ultimoPdf;
            }
            return extraerFechaDesdePeriodo($empresa->periodo32d);
        default:
            return null;
    }
}

function fechaExcedeMeses($fechaCadena, $meses)
{
    if (!$fechaCadena || !$meses) {
        return false;
    }

    try {
        $fecha = new \DateTime($fechaCadena);
    } catch (\Exception $e) {
        return false;
    }

    $limite = clone $fecha;
    $limite->modify('+' . (int) $meses . ' months');
    $hoy = new \DateTime('now');
    return $limite <= $hoy;
}

function fechaHaVencido($fechaCadena)
{
    if (!$fechaCadena) {
        return false;
    }

    try {
        $fecha = new \DateTime($fechaCadena);
    } catch (\Exception $e) {
        return false;
    }

    $hoy = new \DateTime('today');
    return $fecha < $hoy;
}

function determinarEstadoCampoBase($campo, $valor, $postergacion, $empresa)
{
    $valorNormalizado = $valor;
    if (is_string($valorNormalizado)) {
        $valorNormalizado = trim($valorNormalizado);
    }

    $tieneValor = false;
    switch ($campo) {
        case 'telefono':
            $tieneValor = !empty($valorNormalizado);
            $estado = $tieneValor ? 'completado' : 'sin_datos';
            break;
        case 'finDominio':
            $tieneValor = !empty($valorNormalizado) && $valorNormalizado !== '0000-00-00';
            if ($tieneValor && fechaHaVencido($valorNormalizado)) {
                $estado = 'sin_datos';
            } else {
                $estado = $tieneValor ? 'completado' : 'sin_datos';
            }
            break;
        default:
            $tieneValor = !empty($valorNormalizado);
            $estado = $tieneValor ? 'completado' : 'sin_datos';
            break;
    }

    global $BASE_URGENCIA_MESES;
    if ($tieneValor && isset($BASE_URGENCIA_MESES[$campo])) {
        $fechaReferencia = obtenerFechaCampoBase($empresa, $campo);
        if ($fechaReferencia && fechaExcedeMeses($fechaReferencia, $BASE_URGENCIA_MESES[$campo])) {
            $estado = 'urgente';
        }
    }

    if (is_array($postergacion) && !empty($postergacion)) {
        $total = isset($postergacion['total']) ? (int) $postergacion['total'] : 0;
        $status = isset($postergacion['status']) ? (int) $postergacion['status'] : null;

        if ($total >= 3 || $status === 3) {
            return 'postergado_3';
        }

        if ($status === 1) {
            return 'postergado';
        }
    }

    return $estado;
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$clave = isset($_GET['clave']) ? $_GET['clave'] : null; // Para desencriptar documentos si aplica

$admin = new AdministradorEmpresa();
$empresas = $admin->dameEmpresas();

// Filtro de búsqueda
if ($search !== '') {
    $empresas = array_filter($empresas, function ($e) use ($search) {
        $s = mb_strtolower($search);
        // Buscar en razón social, RFC, correo, teléfono
        $found = (
            mb_strpos(mb_strtolower($e->razon), $s) !== false ||
            mb_strpos(mb_strtolower($e->rfc), $s) !== false ||
            mb_strpos(mb_strtolower($e->correo), $s) !== false ||
            mb_strpos(mb_strtolower($e->telefono), $s) !== false
        );
        // Buscar en cuentas bancarias (número de cuenta, clabe, banco)
        if (!$found && isset($e->cuentas) && is_array($e->cuentas)) {
            foreach ($e->cuentas as $cuenta) {
                if (
                    (isset($cuenta->cuenta) && mb_strpos(mb_strtolower($cuenta->cuenta), $s) !== false) ||
                    (isset($cuenta->clave) && mb_strpos(mb_strtolower($cuenta->clave), $s) !== false) ||
                    (isset($cuenta->banco) && mb_strpos(mb_strtolower($cuenta->banco), $s) !== false)
                ) {
                    $found = true;
                    break;
                }
            }
        }
        return $found;
    });
}

$total = count($empresas);
$pages = max(1, ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;
$data = array_slice(array_values($empresas), $offset, $limit);

// Mapear postergaciones por empresa/campo para calcular estados
$ids = array_map(function ($empresa) {
    return $empresa->id;
}, $data);
$postergaciones = $admin->damePostergacionesPorEmpresas($ids);

$out = [];
foreach ($data as $empresa) {
    $row = [
        'id' => $empresa->id,
        'razon' => $empresa->razon,
        'rfc' => $empresa->rfc,
        'calle' => $empresa->calle,
        'numero' => $empresa->numero,
        'colonia' => $empresa->colonia,
        'cp' => $empresa->cp,
        'estado' => $empresa->estado,
        'logo' => $empresa->logo,
        'regimen' => $empresa->regimen,
        'telefono' => $empresa->telefono,
        'correo' => $empresa->correo,
        'descripcion' => $empresa->descripcion,
        'sitioWeb' => $empresa->sitioWeb,
        'inicioDominio' => $empresa->inicioDominio,
        'finDominio' => $empresa->finDominio,
        'prioridad' => $empresa->prioridad,
        'comprobanteDom' => $empresa->comprobanteDom,
        'constanciaSf' => $empresa->constanciaSf,
        'pdf' => $empresa->pdf,
        'periodoTel' => $empresa->periodoTel,
        'periodoCorreo' => $empresa->periodoCorreo,
        'periodoDireccion' => $empresa->periodoDireccion,
        'periodoWeb' => $empresa->periodoWeb,
        // Campos snake_case para compatibilidad frontend
        'periodo_constancia' => $empresa->periodoConstancia,
        'periodo_32d' => $empresa->periodo32d,
        'periodo_sitiow' => $empresa->periodoSitio,
        // Solo datos mínimos para la tabla principal
        'aplica_imss' => isset($empresa->aplicaImss) ? $empresa->aplicaImss : 1,
    ];

    $estadoCampos = [];
    foreach ($CAMPO_POSTERGADO_MAP as $campoKey => $campoPostergado) {
        $postInfo = isset($postergaciones[$empresa->id][$campoPostergado]) ? $postergaciones[$empresa->id][$campoPostergado] : null;
        $valorCampo = isset($row[$campoKey]) ? $row[$campoKey] : null;
        $estadoCampos[$campoKey] = determinarEstadoCampoBase($campoKey, $valorCampo, $postInfo, $empresa);
    }

    $row['estadosCampos'] = $estadoCampos;
    $row['postergaciones'] = isset($postergaciones[$empresa->id]) ? $postergaciones[$empresa->id] : [];

    $out[] = $row;
}

$detalleEstados = $admin->dameResumenDetalleEmpresas($ids);

foreach ($out as &$row) {
    $empresaId = $row['id'];
    if (isset($detalleEstados[$empresaId])) {
        $row['detalleEstados'] = $detalleEstados[$empresaId];
    }
}
unset($row);

$response = [
    'success' => true,
    'data' => $out,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'pages' => $pages
];
echo json_encode($response);
