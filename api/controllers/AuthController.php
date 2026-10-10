<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private $modelo;

    public function __construct($db)
    {
        $this->modelo = new Usuario($db);
    }

    // ============================================
    // POST /api/auth/login
    // ============================================
    public function login()
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

            if (empty($body['email']) || empty($body['password'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Correo y contraseña son obligatorios'
                ]);
                return;
            }

            $usuario = $this->modelo->login($body['email'], $body['password']);

            if (!$usuario) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ]);
                return;
            }

            // Guardar sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $usuario['UsuarioID'];
            $_SESSION['usuario_nombre'] = $usuario['NombreCompleto'];
            $_SESSION['usuario_email'] = $usuario['Email'];
            $_SESSION['usuario_rol'] = $usuario['Rol'];

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => '¡Bienvenido ' . $usuario['NombreCompleto'] . '!',
                'usuario' => $usuario
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    // ============================================
    // POST /api/auth/registro
    // ============================================
    public function registrar()
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

            $errores = $this->validar($body);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errores' => $errores
                ]);
                return;
            }

            $nuevoID = $this->modelo->registrar($body);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => '¡Registro exitoso! Ya puedes iniciar sesión',
                'id' => $nuevoID
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'registrado') !== false) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                $this->errorInterno($e);
            }
        }
    }

    // ============================================
    // GET /api/auth/me
    // ============================================
    public function me()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            return;
        }
        echo json_encode([
            'success' => true,
            'usuario' => [
                'id' => $_SESSION['usuario_id'],
                'nombre' => $_SESSION['usuario_nombre'],
                'email' => $_SESSION['usuario_email'],
                'rol' => $_SESSION['usuario_rol'] ?? 'Recepcionista'
            ]
        ]);
    }

    // ============================================
    // POST /api/auth/logout
    // ============================================
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
    }

    // ============================================
    // VALIDACIONES
    // ============================================
    private function validar($datos)
    {
        $errores = [];

        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 3) {
            $errores['nombre'] = 'Nombre obligatorio (mín. 3 caracteres)';
        }

        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Correo electrónico inválido';
        }

        if (empty($datos['password']) || strlen($datos['password']) < 6) {
            $errores['password'] = 'Contraseña mínimo 6 caracteres';
        }

        return $errores;
    }

    private function errorInterno($e)
    {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor',
            'error' => $e->getMessage()
        ]);
    }
}