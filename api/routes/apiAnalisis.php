
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/conectorBD.php';
require_once __DIR__ . '/../utils/encriptador.php';
require_once dirname(__DIR__, 2) . '/utils/routes.php';

// Validar API Key exclusiva para n8n
$config = require __DIR__ . '/../config/apikey_n8n.php';
$apiKeyN8N = $config['API_KEY_N8N'];
$apiKeyRequest = isset($_GET['apiKey']) ? $_GET['apiKey'] : (isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null);
if ($apiKeyRequest !== $apiKeyN8N) {
    http_response_code(401);
    echo json_encode(['error' => 'Acceso no autorizado: API Key inválida']);
    exit;
}

// Endpoint para análisis de documentos permanentes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $idDocumento = isset($_GET['idDocumento']) ? $_GET['idDocumento'] : null;
    require_once __DIR__ . '/../config/conectorBD.php';
    require_once __DIR__ . '/../utils/encriptador.php';
    $con = new conector();

    if (!$idDocumento) {
        // Buscar el documento más antiguo con analizado = 0 o false
        $query = "SELECT id, fec_creacion, documento, analizado, analisis_token FROM actas_const WHERE analizado = 0 ORDER BY fec_creacion ASC LIMIT 1";
        $result = $con->ejecutar($query);
        if (!$result || mysqli_num_rows($result) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No hay documentos pendientes de analizar']);
            exit;
        }
        $row = mysqli_fetch_assoc($result);
        $idDocumento = $row['id'];
    } else {
        $query = "SELECT fec_creacion, documento, analizado, analisis_token FROM actas_const WHERE id = $idDocumento";
        $result = $con->ejecutar($query);
        if (!$result || mysqli_num_rows($result) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Documento no encontrado']);
            exit;
        }
        $row = mysqli_fetch_assoc($result);
        // Agregar idDocumento al array si viene por id
        $row['id'] = $idDocumento;
    }

    $fechaCarga = $row['fec_creacion'];
    $rutaEncriptada = $row['documento'];
    $analizado = $row['analizado'];
    $analisisToken = $row['analisis_token'];
    $idDocumento = $row['id'];

    if ($analizado) {
        // Marcar como analizado en la base de datos
        $con->ejecutar("UPDATE actas_const SET analizado = 1 WHERE id = $idDocumento");
        echo json_encode([
            'idDocumento' => $idDocumento,
            'analizado' => true,
            'mensaje' => 'El documento ya fue analizado.'
        ]);
        exit;
    }

    // Desencriptar la ruta
    $encriptador = new Encriptador();
    $rutaDocumento = $encriptador->desencriptar($rutaEncriptada);

    // Token de 10 caracteres
    if ($analisisToken && strlen($analisisToken) === 15) {
        $token = $analisisToken;
    } else {
        $token = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 15);
        // Guardar el token en la base de datos
        $updateToken = "UPDATE actas_const SET analisis_token = '$token' WHERE id = $idDocumento";
        $con->ejecutar($updateToken);
    }

	// Crear carpeta Analisis/token dentro de la instalación actual.
	$analisisDir = appFilesystemPath('/Documentos/DocumentosPermanentes/Analisis');
    if (!is_dir($analisisDir)) {
        mkdir($analisisDir, 0777, true);
    }
    $tokenDir = $analisisDir . '/' . $token;
    if (!is_dir($tokenDir)) {
        mkdir($tokenDir, 0777, true);
    }

	// URLs públicas basadas en APP_URL, sin depender del servidor anterior.
	$carpetaAnalisisUrl = absoluteUrl('Documentos/DocumentosPermanentes/Analisis/' . rawurlencode($token));
	$rutaNormalizada = str_replace('\\', '/', (string) $rutaDocumento);
	$rootNormalizado = rtrim(str_replace('\\', '/', APP_ROOT), '/');
	if (str_starts_with($rutaNormalizada, $rootNormalizado)) {
		$rutaNormalizada = substr($rutaNormalizada, strlen($rootNormalizado));
	}
	$rutaDocumentoUrl = absoluteUrl(ltrim($rutaNormalizada, '/'));

    // Respuesta
    echo json_encode([
        'idDocumento' => $idDocumento,
        'fechaCarga' => $fechaCarga,
        'rutaDocumento' => $rutaDocumentoUrl,
        'token' => $token,
        'carpetaAnalisis' => $carpetaAnalisisUrl,
        'analizado' => false
    ]);
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
    // Marcar como analizado en la base de datos después de responder
    $con->ejecutar("UPDATE actas_const SET analizado = 1 WHERE id = $idDocumento");
    exit;
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}
