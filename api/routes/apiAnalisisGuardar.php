<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/conectorBD.php';

// Validar API Key exclusiva para n8n
$config = require __DIR__ . '/../config/apikey_n8n.php';
$apiKeyN8N = $config['API_KEY_N8N'];
$apiKeyRequest = isset($_GET['apiKey']) ? $_GET['apiKey'] : (isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null);
if ($apiKeyRequest !== $apiKeyN8N) {
    http_response_code(401);
    echo json_encode(['error' => 'Acceso no autorizado: API Key inválida']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id_documento']) || !is_numeric($input['id_documento'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Falta el id_documento o es inválido']);
        exit;
    }
    $id_documento = intval($input['id_documento']);
    $data = isset($input['data']) ? $input['data'] : null;
    if (!$data || !is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Falta el campo data o no es un objeto válido']);
        exit;
    }
    $con = new conector();
    // Insertar nombres
    if (isset($data['nombres']) && is_array($data['nombres'])) {
        foreach ($data['nombres'] as $item) {
            $nombre = isset($item['nombre']) ? addslashes($item['nombre']) : '';
            $tipo = isset($item['tipo']) ? addslashes($item['tipo']) : '';
            if ($nombre && $tipo) {
                $query = "INSERT INTO analisis_nombres (id_documento, nombre, tipo) VALUES ($id_documento, '$nombre', '$tipo')";
                $con->ejecutar($query);
            }
        }
    }
    // Insertar fechas
    if (isset($data['fechas']) && is_array($data['fechas'])) {
        foreach ($data['fechas'] as $item) {
            $fecha = isset($item['fecha']) ? addslashes($item['fecha']) : '';
            $tipo = isset($item['tipo']) ? addslashes($item['tipo']) : '';
            if ($fecha && $tipo) {
                $query = "INSERT INTO analisis_fechas (id_documento, fecha, tipo) VALUES ($id_documento, '$fecha', '$tipo')";
                $con->ejecutar($query);
            }
        }
    }
    // Insertar numeros
    if (isset($data['numeros']) && is_array($data['numeros'])) {
        foreach ($data['numeros'] as $item) {
            $numero = isset($item['numero']) ? intval($item['numero']) : null;
            $tipo = isset($item['tipo']) ? addslashes($item['tipo']) : '';
            if (!is_null($numero) && $tipo) {
                $query = "INSERT INTO analisis_numeros (id_documento, numero, tipo) VALUES ($id_documento, $numero, '$tipo')";
                $con->ejecutar($query);
            }
        }
    }
    echo json_encode(['success' => true, 'message' => 'Datos almacenados correctamente']);
    exit;
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}
