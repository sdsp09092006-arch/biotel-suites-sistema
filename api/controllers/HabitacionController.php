<?php
require_once __DIR__ . '/../models/Habitacion.php';

class HabitacionController
{
    private $modelo;

    public function __construct($db)
    {
        $this->modelo = new Habitacion($db);
    }

    public function listar()
    {
        try {
            $habitaciones = $this->modelo->listar();
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'total' => count($habitaciones),
                'data' => $habitaciones
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    public function obtener($id)
    {
        try {
            $habitacion = $this->modelo->obtenerPorId($id);
            if (!$habitacion) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Habitación no encontrada']);
                return;
            }
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $habitacion]);
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
                'message' => 'Habitación creada correctamente',
                'id' => $nuevoID
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'número') !== false) {
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

            $habitacion = $this->modelo->obtenerPorId($id);
            if (!$habitacion) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Habitación no encontrada']);
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
            echo json_encode(['success' => true, 'message' => 'Habitación actualizada correctamente']);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'número') !== false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $this->errorInterno($e);
            }
        }
    }

    public function cambiarEstado($id)
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            if (empty($body['estado'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Falta el campo estado']);
                return;
            }
            $this->modelo->cambiarEstado($id, $body['estado']);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function eliminar($id)
    {
        try {
            $habitacion = $this->modelo->obtenerPorId($id);
            if (!$habitacion) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Habitación no encontrada']);
                return;
            }
            $this->modelo->eliminar($id);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Habitación eliminada correctamente']);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'reservas') !== false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $this->errorInterno($e);
            }
        }
    }

    private function validar($datos, $requiereTodos = true)
    {
        $errores = [];
        $tiposValidos = [
            'Suite Deluxe', 'Suite Ejecutiva', 'Doble Estándar',
            'Suite Presidencial', 'Suite Junior', 'Familiar'
        ];
        $estadosValidos = ['Disponible', 'Ocupada', 'Mantenimiento'];

        if ($requiereTodos && empty($datos['numero'])) {
            $errores['numero'] = 'El número es obligatorio';
        }

        if ($requiereTodos && empty($datos['tipo'])) {
            $errores['tipo'] = 'El tipo es obligatorio';
        } elseif (!empty($datos['tipo']) && !in_array($datos['tipo'], $tiposValidos)) {
            $errores['tipo'] = 'Tipo de habitación no válido';
        }

        if ($requiereTodos && empty($datos['precio_noche'])) {
            $errores['precio_noche'] = 'El precio es obligatorio';
        } elseif (!empty($datos['precio_noche']) && (!is_numeric($datos['precio_noche']) || $datos['precio_noche'] <= 0)) {
            $errores['precio_noche'] = 'El precio debe ser un número mayor a 0';
        }

        if (!empty($datos['capacidad']) && (!is_numeric($datos['capacidad']) || $datos['capacidad'] < 1)) {
            $errores['capacidad'] = 'La capacidad mínima es 1';
        }

        if (!empty($datos['estado']) && !in_array($datos['estado'], $estadosValidos)) {
            $errores['estado'] = 'Estado no válido';
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