<?php
declare(strict_types=1);
require_once __DIR__ . '/../utils/auth_guard.php';
requireAuth();
ensureAdmin();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../controllers/adminEdicion.php';

try {
    $admin = new AdministradorEdicion();
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
        $id = filter_var($_GET['empresa'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (isset($_GET['empresa']) && !$id) throw new InvalidArgumentException('Empresa inválida.');
        if ($id) {
            $result = $admin->detalle($id);
        } else {
            $search = $_GET['search'] ?? '';
            if (!is_string($search) || mb_strlen($search) > 150) throw new InvalidArgumentException('Búsqueda inválida.');
            $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
            $result = $admin->buscar(trim($search), $page);
        }
        echo json_encode(['success' => true] + $result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } elseif ($method === 'POST') {
        $id = filter_var($_POST['empresa'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $tipo = $_POST['tipo'] ?? '';
        $registro = filter_var($_POST['registro'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        if (!$id || !is_string($tipo)) throw new InvalidArgumentException('Datos de edición inválidos.');
        $admin->guardar($id, $tipo, $registro, $_POST);
        echo json_encode(['success' => true, 'message' => 'Cambios guardados correctamente.']);
    } else {
        header('Allow: GET, POST');
        respondWithError(405, 'Método no permitido.');
    }
} catch (InvalidArgumentException $error) {
    respondWithError(422, $error->getMessage());
} catch (OutOfBoundsException $error) {
    respondWithError(404, $error->getMessage());
} catch (Throwable $error) {
    error_log('Edición: ' . $error->getMessage());
    respondWithError(500, 'No fue posible completar la operación. Intenta nuevamente.');
}
