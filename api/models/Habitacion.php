<?php
/**
 * Modelo Habitacion
 * Gestiona el acceso a datos de la tabla habitaciones
 */

class Habitacion
{
    private $conn;
    private $table = 'habitaciones';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $sql = "SELECT
                    HabitacionID AS id,
                    Numero AS numero,
                    Tipo AS tipo,
                    Capacidad AS capacidad,
                    Camas AS camas,
                    Piso AS piso,
                    PrecioNoche AS precio_noche,
                    Estado AS estado,
                    Descripcion AS descripcion,
                    Amenidades AS amenidades,
                    Activo AS activo
                FROM {$this->table}
                WHERE Activo = TRUE
                ORDER BY Piso ASC, Numero ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    HabitacionID AS id,
                    Numero AS numero,
                    Tipo AS tipo,
                    Capacidad AS capacidad,
                    Camas AS camas,
                    Piso AS piso,
                    PrecioNoche AS precio_noche,
                    Estado AS estado,
                    Descripcion AS descripcion,
                    Amenidades AS amenidades,
                    Activo AS activo
                FROM {$this->table}
                WHERE HabitacionID = :id AND Activo = TRUE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function existeNumero($numero, $excluirId = null)
    {
        $sql = "SELECT HabitacionID FROM {$this->table} WHERE Numero = :numero";
        if ($excluirId !== null) {
            $sql .= " AND HabitacionID != :id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':numero', $numero);
        if ($excluirId !== null) {
            $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    public function crear($datos)
    {
        if ($this->existeNumero($datos['numero'])) {
            throw new Exception('Ya existe una habitación con ese número');
        }

        $sql = "INSERT INTO {$this->table}
                (Numero, Tipo, Capacidad, Camas, Piso, PrecioNoche,
                 Estado, Descripcion, Amenidades, Activo)
                VALUES
                (:numero, :tipo, :capacidad, :camas, :piso, :precio,
                 :estado, :descripcion, :amenidades, TRUE)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':numero', $datos['numero']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
        $stmt->bindParam(':camas', $datos['camas'], PDO::PARAM_INT);
        $stmt->bindParam(':piso', $datos['piso'], PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio_noche']);
        $stmt->bindParam(':estado', $datos['estado']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':amenidades', $datos['amenidades']);

        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        if (!empty($datos['numero']) && $this->existeNumero($datos['numero'], $id)) {
            throw new Exception('Ya existe otra habitación con ese número');
        }

        $campos = [];
        $params = [':id' => $id];

        $mapa = [
            'numero' => 'Numero',
            'tipo' => 'Tipo',
            'capacidad' => 'Capacidad',
            'camas' => 'Camas',
            'piso' => 'Piso',
            'precio_noche' => 'PrecioNoche',
            'estado' => 'Estado',
            'descripcion' => 'Descripcion',
            'amenidades' => 'Amenidades',
        ];

        foreach ($mapa as $key => $columna) {
            if (isset($datos[$key]) && $datos[$key] !== '') {
                $campos[] = "$columna = :$key";
                $params[":$key"] = $datos[$key];
            }
        }

        if (empty($campos)) {
            throw new Exception('No hay campos para actualizar');
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $campos) . " WHERE HabitacionID = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => &$value) {
            $tipo = in_array($key, [':id', ':capacidad', ':camas', ':piso'])
                ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($key, $value, $tipo);
        }

        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado)
    {
        $permitidos = ['Disponible', 'Ocupada', 'Mantenimiento'];
        if (!in_array($estado, $permitidos)) {
            throw new Exception('Estado no válido');
        }

        $sql = "UPDATE {$this->table} SET Estado = :estado WHERE HabitacionID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Verificar si tiene reservas activas
        $sqlCheck = "SELECT COUNT(*) AS total FROM reservas
                     WHERE HabitacionID = :id
                     AND Estado IN ('Pendiente', 'Confirmada', 'Por verificar')";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $resultado = $stmtCheck->fetch();

        if ($resultado['total'] > 0) {
            throw new Exception('No se puede eliminar: la habitación tiene reservas activas');
        }

        // Soft delete
        $sql = "UPDATE {$this->table} SET Activo = FALSE WHERE HabitacionID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}