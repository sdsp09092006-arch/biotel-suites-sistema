<?php
/**
 * API REST - Biotel Suites
 * Punto de entrada único para todas las peticiones
 */

// ============================================
// CABECERAS CORS Y JSON
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Responder a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $_POST = json_decode($input, true) ?: [];
    }
}

// ============================================
// CARGAR DEPENDENCIAS
// ============================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ReservaController.php';

// ============================================
// CONECTAR A LA BASE DE DATOS
// ============================================
$database = new Database();
$db = $database->conectar();

// ============================================
// OBTENER RUTA Y MÉTODO
// ============================================
$method = $_SERVER['REQUEST_METHOD'];

// Obtener la ruta limpia (sin el prefijo /api/)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/biotel-suites-sistema/api', '', $uri);
$uri = str_replace('/api', '', $uri);
$uri = trim($uri, '/');

// Separar segmentos: reservas/1 → ['reservas', '1']
$segmentos = explode('/', $uri);

$recurso = $segmentos[0] ?? '';
$id = $segmentos[1] ?? null;

// ============================================
// ENRUTAMIENTO
// ============================================
try {
    // Solo manejamos el recurso "reservas"
    if ($recurso !== 'reservas') {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Recurso no encontrado',
            'ruta' => $uri
        ]);
        exit;
    }

    $controller = new ReservaController($db);

    // Switch por método HTTP
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->obtener($id);
            } else {
                $controller->listar();
            }
            break;

        case 'POST':
            $controller->crear();
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Se requiere el ID para actualizar'
                ]);
                exit;
            }
            $controller->actualizar($id);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Se requiere el ID para eliminar'
                ]);
                exit;
            }
            $controller->eliminar($id);
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
        'error' => $e->getMessage()
    ]);
}