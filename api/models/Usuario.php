<?php
/**
 * Modelo Usuario
 * Gestiona autenticación de trabajadores del sistema
 */

class Usuario
{
    private $conn;
    private $table = 'usuarios_sistema';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Buscar usuario por email
    public function buscarPorEmail($email)
    {
        $sql = "SELECT UsuarioID, NombreCompleto, Email, Password, Rol, Activo
                FROM {$this->table}
                WHERE Email = :email AND Activo = TRUE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Verificar credenciales
    public function login($email, $password)
    {
        $usuario = $this->buscarPorEmail($email);

        if (!$usuario) {
            return false;
        }

        // Verificar contraseña (bcrypt)
        if (!password_verify($password, $usuario['Password'])) {
            return false;
        }

        // Quitar el hash antes de devolver
        unset($usuario['Password']);
        return $usuario;
    }

    // Registrar nuevo trabajador
    public function registrar($datos)
    {
        // Verificar si el email ya existe
        if ($this->buscarPorEmail($datos['email'])) {
            throw new Exception('El correo ya está registrado');
        }

        // Validar rol
        $rolesPermitidos = ['Recepcionista', 'Gerente'];
        $rol = $datos['rol'] ?? 'Recepcionista';
        if (!in_array($rol, $rolesPermitidos)) {
            $rol = 'Recepcionista';
        }

        // Hash de contraseña
        $passwordHash = password_hash($datos['password'], PASSWORD_BCRYPT);

        // Insertar nuevo usuario
        $sql = "INSERT INTO {$this->table}
                (NombreCompleto, Email, Password, Rol, Activo)
                VALUES
                (:nombre, :email, :password, :rol, TRUE)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':rol', $rol);

        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}