<?php
/**
 * Conexión a la base de datos MySQL
 * Biotel Suites - API REST
 */

// Configuración (en producción, usar variables de entorno)
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'biotel_suites');

class Database
{
    private $conn;

    public function conectar()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            // Zona horaria
            $this->conn->exec("SET time_zone = '-04:00'");

            return $this->conn;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error de conexión a la base de datos',
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}