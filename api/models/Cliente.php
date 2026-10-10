<?php
/**
 * Modelo Cliente
 * Gestiona el acceso a datos de la tabla huespedes
 */

class Cliente
{
    private $conn;
    private $table = 'huespedes';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Listar todos los clientes activos
    public function listar()
    {
        $sql = "SELECT
                    HuespedID AS id,
                    NombreCompleto AS nombre,
                    Cedula AS cedula,
                    Email AS email,
                    Telefono AS telefono,
                    Direccion AS direccion,
                    FechaNacimiento AS fecha_nacimiento,
                    Nacionalidad AS nacionalidad,
                    Activo AS activo,
                    FechaRegistro AS fecha_registro
                FROM {$this->table}
                WHERE Activo = TRUE
                ORDER BY HuespedID DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un cliente por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    HuespedID AS id,
                    NombreCompleto AS nombre,
                    Cedula AS cedula,
                    Email AS email,
                    Telefono AS telefono,
                    Direccion AS direccion,
                    FechaNacimiento AS fecha_nacimiento,
                    Nacionalidad AS nacionalidad,
                    Activo AS activo,
                    FechaRegistro AS fecha_registro
                FROM {$this->table}
                WHERE HuespedID = :id AND Activo = TRUE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Verificar si existe la cédula
    public function existeCedula($cedula, $excluirId = null)
    {
        $sql = "SELECT HuespedID FROM {$this->table} WHERE Cedula = :cedula";
        if ($excluirId !== null) {
            $sql .= " AND HuespedID != :id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        if ($excluirId !== null) {
            $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    // Crear cliente
    public function crear($datos)
    {
        if ($this->existeCedula($datos['cedula'])) {
            throw new Exception('Ya existe un cliente con esa cédula');
        }

        $sql = "INSERT INTO {$this->table}
                (NombreCompleto, Cedula, Password, Email, Telefono,
                 Direccion, FechaNacimiento, Nacionalidad, Activo)
                VALUES
                (:nombre, :cedula, :password, :email, :telefono,
                 :direccion, :fecha_nac, :nacionalidad, TRUE)";

        $stmt = $this->conn->prepare($sql);

        $password = !empty($datos['password'])
            ? password_hash($datos['password'], PASSWORD_BCRYPT)
            : null;

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':cedula', $datos['cedula']);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':fecha_nac', $datos['fecha_nacimiento']);
        $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);

        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Actualizar cliente (campos dinámicos)
    public function actualizar($id, $datos)
    {
        if (!empty($datos['cedula']) && $this->existeCedula($datos['cedula'], $id)) {
            throw new Exception('Ya existe otro cliente con esa cédula');
        }

        $campos = [];
        $params = [':id' => $id];

        $mapa = [
            'nombre' => 'NombreCompleto',
            'cedula' => 'Cedula',
            'email' => 'Email',
            'telefono' => 'Telefono',
            'direccion' => 'Direccion',
            'fecha_nacimiento' => 'FechaNacimiento',
            'nacionalidad' => 'Nacionalidad',
        ];

        foreach ($mapa as $key => $columna) {
            if (isset($datos[$key]) && $datos[$key] !== '') {
                $campos[] = "$columna = :$key";
                $params[":$key"] = $datos[$key];
            }
        }

        // Password opcional
        if (!empty($datos['password'])) {
            $campos[] = "Password = :password";
            $params[':password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        if (empty($campos)) {
            throw new Exception('No hay campos para actualizar');
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $campos) . " WHERE HuespedID = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => &$value) {
            $tipo = ($key === ':id') ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($key, $value, $tipo);
        }

        return $stmt->execute();
    }

    // Eliminación lógica (Soft Delete)
    public function eliminar($id)
    {
        $sql = "UPDATE {$this->table} SET Activo = FALSE WHERE HuespedID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}