<?php
/**
 * API REST - Biotel Suites
 * Punto de entrada único — Router multi-recurso
 */

// ============================================
// CABECERAS CORS Y JSON
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parsear body JSON para PUT/PATCH/DELETE
if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE'])) {
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
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/HabitacionController.php';
require_once __DIR__ . '/controllers/AuthController.php';

// ============================================
// CONECTAR A LA BASE DE DATOS
// ============================================
$database = new Database();
$db = $database->conectar();

// ============================================
// PARSEAR RUTA
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/biotel-suites-sistema/api', '', $uri);
$uri = str_replace('/api', '', $uri);
$uri = trim($uri, '/');

$segmentos = explode('/', $uri);
$recurso = $segmentos[0] ?? '';
$id = $segmentos[1] ?? null;
$accion = $segmentos[2] ?? null;

// ============================================
// SELECCIONAR CONTROLADOR
// ============================================
try {
    switch ($recurso) {
        case 'reservas':
            $controller = new ReservaController($db);
            break;
        case 'clientes':
        case 'huespedes':
            $controller = new ClienteController($db);
            break;
        case 'habitaciones':
            $controller = new HabitacionController($db);
            break;
        case 'auth':
            $controller = new AuthController($db);
            break;
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'ruta' => $uri,
                'recursos_disponibles' => ['reservas', 'clientes', 'habitaciones', 'auth']
            ]);
            exit;
    }

    // ============================================
    // ACCIONES ESPECIALES PARA AUTH
    // ============================================
    if ($recurso === 'auth') {
        if ($method === 'POST' && $id === 'login') {
            $controller->login();
            exit;
        }
        if ($method === 'POST' && $id === 'registro') {
            $controller->registrar();
            exit;
        }
        if ($method === 'POST' && $id === 'logout') {
            $controller->logout();
            exit;
        }
        if ($method === 'GET' && $id === 'me') {
            $controller->me();
            exit;
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Acción de auth no encontrada']);
        exit;
    }

    // ============================================
    // ACCIONES ESPECIALES PARA HABITACIONES
    // ============================================
    if ($recurso === 'habitaciones' && $accion === 'estado' && $method === 'PATCH') {
        $controller->cambiarEstado($id);
        exit;
    }

    // ============================================
    // ENRUTAMIENTO POR MÉTODO HTTP
    // ============================================
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
                echo json_encode(['success' => false, 'message' => 'Se requiere el ID']);
                exit;
            }
            $controller->actualizar($id);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Se requiere el ID']);
                exit;
            }
            $controller->eliminar($id);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
        'error' => $e->getMessage()
    ]);
}