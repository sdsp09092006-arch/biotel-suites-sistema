<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private $modelo;

    public function __construct($db)
    {
        $this->modelo = new Cliente($db);
    }

    public function listar()
    {
        try {
            $clientes = $this->modelo->listar();
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'total' => count($clientes),
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    public function obtener($id)
    {
        try {
            $cliente = $this->modelo->obtenerPorId($id);
            if (!$cliente) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                return;
            }
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $cliente]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    public function crear()
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

            $errores = $this->validar($body, true);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errores' => $errores
                ]);
                return;
            }

            $nuevoID = $this->modelo->crear($body);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Cliente registrado correctamente',
                'id' => $nuevoID
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'cédula') !== false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $this->errorInterno($e);
            }
        }
    }

    public function actualizar($id)
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

            $cliente = $this->modelo->obtenerPorId($id);
            if (!$cliente) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                return;
            }

            $errores = $this->validar($body, false);
            if (!empty($errores)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errores' => $errores
                ]);
                return;
            }

            $this->modelo->actualizar($id, $body);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'cédula') !== false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $this->errorInterno($e);
            }
        }
    }

    public function eliminar($id)
    {
        try {
            $cliente = $this->modelo->obtenerPorId($id);
            if (!$cliente) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                return;
            }
            $this->modelo->eliminar($id);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    private function validar($datos, $requiereTodos = true)
    {
        $errores = [];

        if ($requiereTodos && empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (!empty($datos['nombre']) && strlen(trim($datos['nombre'])) < 3) {
            $errores['nombre'] = 'El nombre es demasiado corto';
        }

        if ($requiereTodos && empty($datos['cedula'])) {
            $errores['cedula'] = 'La cédula es obligatoria';
        } elseif (!empty($datos['cedula']) && !preg_match('/^[VEJvej]-?\d{6,10}$/', $datos['cedula'])) {
            $errores['cedula'] = 'Formato de cédula inválido (ej: V-12345678)';
        }

        if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Correo electrónico inválido';
        }

        if (!empty($datos['telefono']) && !preg_match('/^[0-9\-\+\s]{10,20}$/', $datos['telefono'])) {
            $errores['telefono'] = 'Teléfono inválido';
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