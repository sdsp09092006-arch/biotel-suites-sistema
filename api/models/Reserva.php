<?php
/**
 * Modelo Reserva
 * Gestiona el acceso a datos de la tabla reservas
 */

class Reserva
{
    private $conn;
    private $table = 'reservas';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Listar todas las reservas con datos de huésped y habitación
    public function listar()
    {
        $sql = "SELECT
                    r.ReservaID as id,
                    h.NombreCompleto as huesped,
                    h.Cedula as cedula,
                    hab.Numero as habitacion_numero,
                    hab.Tipo as habitacion_tipo,
                    CONCAT(hab.Numero, ' - ', hab.Tipo) as habitacion,
                    hab.PrecioNoche as precio_noche,
                    r.FechaCheckin as checkin,
                    r.FechaCheckout as checkout,
                    r.Estado as estado,
                    r.FechaCreacion as fecha_creacion
                FROM {$this->table} r
                INNER JOIN huespedes h ON r.HuespedID = h.HuespedID
                INNER JOIN habitaciones hab ON r.HabitacionID = hab.HabitacionID
                ORDER BY r.ReservaID DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener una reserva por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    r.ReservaID as id,
                    h.NombreCompleto as huesped,
                    h.Cedula as cedula,
                    CONCAT(hab.Numero, ' - ', hab.Tipo) as habitacion,
                    r.FechaCheckin as checkin,
                    r.FechaCheckout as checkout,
                    r.Estado as estado
                FROM {$this->table} r
                INNER JOIN huespedes h ON r.HuespedID = h.HuespedID
                INNER JOIN habitaciones hab ON r.HabitacionID = hab.HabitacionID
                WHERE r.ReservaID = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear nueva reserva
    public function crear($datos)
    {
        $this->conn->beginTransaction();

        try {
            // 1. Buscar o crear huésped por cédula
            $sqlHuesped = "SELECT HuespedID FROM huespedes WHERE Cedula = :cedula";
            $stmtH = $this->conn->prepare($sqlHuesped);
            $stmtH->bindParam(':cedula', $datos['cedula']);
            $stmtH->execute();
            $huesped = $stmtH->fetch();

            if ($huesped) {
                $huespedID = $huesped['HuespedID'];
            } else {
                // Crear nuevo huésped
                $sqlNuevoH = "INSERT INTO huespedes (NombreCompleto, Cedula) VALUES (:nombre, :cedula)";
                $stmtNH = $this->conn->prepare($sqlNuevoH);
                $stmtNH->bindParam(':nombre', $datos['huesped']);
                $stmtNH->bindParam(':cedula', $datos['cedula']);
                $stmtNH->execute();
                $huespedID = $this->conn->lastInsertId();
            }

            // 2. Buscar habitación por número
            $partes = explode(' - ', $datos['habitacion']);
            $numeroHab = trim($partes[0]);

            $sqlHab = "SELECT HabitacionID FROM habitaciones WHERE Numero = :numero";
            $stmtHab = $this->conn->prepare($sqlHab);
            $stmtHab->bindParam(':numero', $numeroHab);
            $stmtHab->execute();
            $habitacion = $stmtHab->fetch();

            if (!$habitacion) {
                throw new Exception('Habitación no encontrada');
            }

            $habitacionID = $habitacion['HabitacionID'];

            // 3. Insertar la reserva
            $sql = "INSERT INTO {$this->table}
                    (HuespedID, HabitacionID, FechaCheckin, FechaCheckout, Estado)
                    VALUES (:huesped_id, :habitacion_id, :checkin, :checkout, :estado)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':huesped_id', $huespedID, PDO::PARAM_INT);
            $stmt->bindParam(':habitacion_id', $habitacionID, PDO::PARAM_INT);
            $stmt->bindParam(':checkin', $datos['checkin']);
            $stmt->bindParam(':checkout', $datos['checkout']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->execute();

            $nuevoID = $this->conn->lastInsertId();
            $this->conn->commit();

            return $nuevoID;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Actualizar reserva
    public function actualizar($id, $datos)
{
    $this->conn->beginTransaction();

    try {
        // ============================================
        // 1. ACTUALIZAR EL HUÉSPED (nombre)
        // ============================================
        // Obtener el HuespedID de esta reserva
        $sqlH = "SELECT HuespedID FROM {$this->table} WHERE ReservaID = :id";
        $stmtH = $this->conn->prepare($sqlH);
        $stmtH->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtH->execute();
        $reserva = $stmtH->fetch();

        if (!$reserva) {
            throw new Exception('Reserva no encontrada');
        }

        $huespedID = $reserva['HuespedID'];

        // Actualizar nombre del huésped
                // Actualizar datos del huésped (nombre + cédula)
        $camposH = [];
        $paramsH = [':huesped_id' => $huespedID];

        if (!empty($datos['huesped'])) {
            $camposH[] = "NombreCompleto = :nombre";
            $paramsH[':nombre'] = $datos['huesped'];
        }
        if (!empty($datos['cedula'])) {
            $camposH[] = "Cedula = :cedula";
            $paramsH[':cedula'] = $datos['cedula'];
        }

        if (!empty($camposH)) {
            $sqlUH = "UPDATE huespedes SET " . implode(', ', $camposH) . " WHERE HuespedID = :huesped_id";
            $stmtUH = $this->conn->prepare($sqlUH);

            foreach ($paramsH as $key => &$value) {
                $tipo = ($key === ':huesped_id') ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmtUH->bindParam($key, $value, $tipo);
            }

            $stmtUH->execute();
        }
        // ============================================
        // 2. ACTUALIZAR LA RESERVA (fechas, estado, habitación)
        // ============================================
        // Si viene habitación, buscar su ID
        $habitacionID = null;
        if (!empty($datos['habitacion'])) {
            $partes = explode(' - ', $datos['habitacion']);
            $numeroHab = trim($partes[0]);

            $sqlHab = "SELECT HabitacionID FROM habitaciones WHERE Numero = :numero";
            $stmtHab = $this->conn->prepare($sqlHab);
            $stmtHab->bindParam(':numero', $numeroHab);
            $stmtHab->execute();
            $hab = $stmtHab->fetch();

            if ($hab) {
                $habitacionID = $hab['HabitacionID'];
            }
        }

        // Construir UPDATE dinámico
        $campos = [];
        $params = [':id' => $id];

        if (!empty($datos['checkin'])) {
            $campos[] = "FechaCheckin = :checkin";
            $params[':checkin'] = $datos['checkin'];
        }
        if (!empty($datos['checkout'])) {
            $campos[] = "FechaCheckout = :checkout";
            $params[':checkout'] = $datos['checkout'];
        }
        if (!empty($datos['estado'])) {
            $campos[] = "Estado = :estado";
            $params[':estado'] = $datos['estado'];
        }
        if ($habitacionID !== null) {
            $campos[] = "HabitacionID = :habitacion_id";
            $params[':habitacion_id'] = $habitacionID;
        }

        if (empty($campos)) {
            throw new Exception('No hay campos para actualizar');
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $campos) . " WHERE ReservaID = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => &$value) {
            $tipo = ($key === ':id' || $key === ':habitacion_id') ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($key, $value, $tipo);
        }

        $stmt->execute();

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error al actualizar reserva: " . $e->getMessage());
        throw $e;
    }
}

    // Eliminar reserva
    public function eliminar($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE ReservaID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}