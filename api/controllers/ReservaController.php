<?php
/**
 * Controlador Reserva
 * Maneja las peticiones HTTP y orquesta las respuestas
 */

require_once __DIR__ . '/../models/Reserva.php';

class ReservaController
{
    private $modelo;

    public function __construct($db)
    {
        $this->modelo = new Reserva($db);
    }

    // ============================================
    // GET /api/reservas → Listar todas
    // ============================================
    public function listar()
    {
        try {
            $reservas = $this->modelo->listar();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'total' => count($reservas),
                'data' => $reservas
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    // ============================================
    // GET /api/reservas/{id} → Obtener una
    // ============================================
    public function obtener($id)
    {
        try {
            $reserva = $this->modelo->obtenerPorId($id);

            if (!$reserva) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Reserva no encontrada'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $reserva
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    // ============================================
    // POST /api/reservas → Crear nueva
    // ============================================
    public function crear()
    {
        try {
            // Leer JSON del body
            $body = json_decode(file_get_contents('php://input'), true);

            // Validar campos obligatorios
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

            $nuevoID = $this->modelo->crear($body);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Reserva creada correctamente',
                'id' => $nuevoID
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Habitación') !== false) {
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
    // PUT /api/reservas/{id} → Actualizar
    // ============================================
   public function actualizar($id)
{
    try {
        // ✅ CAMBIO: Usar $_POST (ya viene del index.php)
        $body = $_POST;

        if (empty($body)) {
            // Fallback: leer de php://input
            $body = json_decode(file_get_contents('php://input'), true);
        }

        // Verificar que existe
        $reserva = $this->modelo->obtenerPorId($id);
        if (!$reserva) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ]);
            return;
        }

        // Validar campos
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
        echo json_encode([
            'success' => true,
            'message' => 'Reserva actualizada correctamente'
        ]);
    } catch (Exception $e) {
        $this->errorInterno($e);
    }
}
    // ============================================
    // DELETE /api/reservas/{id} → Eliminar
    // ============================================
    public function eliminar($id)
    {
        try {
            $reserva = $this->modelo->obtenerPorId($id);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Reserva no encontrada'
                ]);
                return;
            }

            $this->modelo->eliminar($id);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Reserva eliminada correctamente'
            ]);
        } catch (Exception $e) {
            $this->errorInterno($e);
        }
    }

    // ============================================
    // HELPERS
    // ============================================
    private function validar($datos, $requiereTodos = true)
    {
        $errores = [];

        if ($requiereTodos && empty($datos['huesped'])) {
            $errores['huesped'] = 'El nombre del huésped es obligatorio';
        }

        if ($requiereTodos && empty($datos['cedula'])) {
            $errores['cedula'] = 'La cédula es obligatoria';
        } elseif (!empty($datos['cedula']) && !preg_match('/^[VEJvej]-?\d{6,10}$/', $datos['cedula'])) {
            $errores['cedula'] = 'Formato de cédula inválido';
        }

        if ($requiereTodos && empty($datos['habitacion'])) {
            $errores['habitacion'] = 'La habitación es obligatoria';
        }

        if ($requiereTodos && empty($datos['checkin'])) {
            $errores['checkin'] = 'La fecha de check-in es obligatoria';
        }

        if ($requiereTodos && empty($datos['checkout'])) {
            $errores['checkout'] = 'La fecha de check-out es obligatoria';
        }

        if (!empty($datos['checkin']) && !empty($datos['checkout'])) {
            if ($datos['checkout'] <= $datos['checkin']) {
                $errores['checkout'] = 'Check-out debe ser posterior al check-in';
            }
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