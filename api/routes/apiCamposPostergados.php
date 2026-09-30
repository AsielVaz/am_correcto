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

include_once('../controllers/adminCamposPostergados.php');
include_once('../utils/correo.php');


$accion = $_POST['accion'];

$casoAgregarCampoAPostergar = 'agregarCampoAPostergar';
$casoVerificarlimiteDePostergacion = 'verificarlimiteDePostergacion';
$casoCompletarTarea = 'completarTarea';
$casoCambiarEstadoPorExcesoDePostergacion = 'cambiarEstadoPorExcesoDePostergaciones';

function agregarCampoAPostergar($id, $campo, $observaciones)
{
    $adminCamposPostergados = new AdministradorCamposPostergados();
    if ($adminCamposPostergados->agregarCampoPostergado($id, $campo, $observaciones)) {
        echo json_encode(array('status' => 'success', 'message' => 'Campo agregado correctamente'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No se pudo agregar el campo'));
    }
}



function verificarlimiteDePostergacion($id, $campo)
{
    $id = $_POST['id'];
    $campo = $_POST['campo'];
    if ($id != '' && $campo != '') {
        $adminCamposPostergados = new AdministradorCamposPostergados();
        $comprobacion = $adminCamposPostergados->comprobarPostergado($id, $campo);
        $observaciones = $adminCamposPostergados->dameObservaciones($id, $campo);
        if ($comprobacion) {
            switch ($campo) {
                case 'comprobanteDomicilio':
                    $campo = 'Comprobante de domicilio';
                    break;
                case 'constanciaSituacionFiscal':
                    $campo = 'Constancia de situación fiscal';
                    break;
                case '32D':
                    $campo = '32D';
                    break;
                case 'finDominio':
                    $campo = 'Fin de dominio de sitio web';
                    break;
                case 'telefono':
                    $campo = 'Teléfono de contacto';
                    break;
                case 'correo':
                    $campo = 'Correo electrónico de contacto';
                    break;
                case 'descripcion':
                    $campo = 'Descripción';
                    break;
                case 'sitioWeb':
                    $campo = 'Sitio web';
                    break;
            }
            $correo = new Correo("10h.mx", 'ssl', 465, 'carhunter@10h.mx', '+f4APsu(Uzgt', 'Tecnología 10H');
            $adminEmpresa = new AdministradorCamposPostergados();
            $empresa = $adminEmpresa->dameEmpresa($id);
            $mensajeHtml = '
            <html>
            <head>
            <title>Limite de postergación</title>
            </head>
            <body>
            <h4>La empresa ' . $empresa['razon'] . ' ha alcanzado el limite de postergación para el campo ' . $campo . ' con las siguientes observaciones:</h4>';
            for ($i = 0; $i < count($observaciones); $i++) {
                // mostrar observaciones enumeradas
                $mensajeHtml .= '<p>' . ($i + 1) . '. ' . $observaciones[$i] . '</p>';
            }
            $mensajeHtml .= '
            <img src="' . $empresa['logo'] . '" alt="Logo Empresa" width="200" height="200" style="display: block; margin-left: auto; margin-right: auto;">
            <p>Será necesario que se contacte con el usuario para que se ponga al día con sus tareas diarias.</p>
            </body>
            </html>
            ';
            $correo->mailer('10h.mx.service@gmail.com', 'IOHANES DURAN', 'Limite de postergación', $mensajeHtml, '', '', '');
            echo json_encode(array('status' => 'error', 'message' => 'El campo ya se ha postergado más de 3 veces'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => 'El campo está disponible para postergar'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al verificar limite de postergacion'));
    }
}

function completarTarea($id, $campo)
{
    if ($id != '' && $campo != '') {
        $adminCamposPostergados = new AdministradorCamposPostergados();
        if ($adminCamposPostergados->completarTarea($id, $campo)) {
            echo json_encode(array('status' => 'success', 'message' => 'Tarea completada correctamente'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No se pudo completar la tarea'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al completar tarea'));
    }
}

function cambiarEstadoPorExcesoDePostergacion($id, $campo)
{
    if ($id != '' && $campo != '') {
        $adminCamposPostergados = new AdministradorCamposPostergados();
        if ($adminCamposPostergados->cambiarEstadoPorExcesoDePostergacion($id, $campo)) {
            echo json_encode(array('status' => 'success', 'message' => 'Estado cambiado correctamente'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No se pudo cambiar el estado'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error al cambiar estado'));
    }
}


switch ($accion) {
    case $casoVerificarlimiteDePostergacion:
        verificarlimiteDePostergacion($_POST['id'], $_POST['campo']);
        break;
    case $casoAgregarCampoAPostergar:
        agregarCampoAPostergar($_POST['id'], $_POST['campo'], $_POST['observaciones']);
        break;
    case $casoCompletarTarea:
        completarTarea($_POST['id'], $_POST['campo']);
        break;
    case $casoCambiarEstadoPorExcesoDePostergacion:
        cambiarEstadoPorExcesoDePostergacion($_POST['id'], $_POST['campo']);
        break;
    default:
        echo json_encode('No se ha encontrado la acción');
        break;
}
