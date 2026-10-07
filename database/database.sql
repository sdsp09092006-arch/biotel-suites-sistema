-- ============================================
-- BIOTEL SUITES - Base de Datos
-- ============================================

CREATE DATABASE IF NOT EXISTS biotel_suites
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE biotel_suites;

-- ============================================
-- TABLA: habitaciones
-- ============================================
CREATE TABLE IF NOT EXISTS habitaciones (
    HabitacionID INT PRIMARY KEY AUTO_INCREMENT,
    Numero VARCHAR(10) NOT NULL UNIQUE,
    Tipo ENUM('Suite Deluxe', 'Suite Ejecutiva', 'Doble Estándar', 'Suite Presidencial') NOT NULL,
    PrecioNoche DECIMAL(10,2) NOT NULL,
    Estado ENUM('Disponible', 'Ocupada', 'Mantenimiento') DEFAULT 'Disponible',
    Activo BOOLEAN DEFAULT TRUE
);

-- ============================================
-- TABLA: huespedes
-- ============================================
CREATE TABLE IF NOT EXISTS huespedes (
    HuespedID INT PRIMARY KEY AUTO_INCREMENT,
    NombreCompleto VARCHAR(150) NOT NULL,
    Cedula VARCHAR(20) NOT NULL UNIQUE,
    Email VARCHAR(100),
    Telefono VARCHAR(20),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: reservas (tabla principal del CRUD)
-- ============================================
CREATE TABLE IF NOT EXISTS reservas (
    ReservaID INT PRIMARY KEY AUTO_INCREMENT,
    HuespedID INT NOT NULL,
    HabitacionID INT NOT NULL,
    FechaCheckin DATE NOT NULL,
    FechaCheckout DATE NOT NULL,
    Estado ENUM('Pendiente', 'Confirmada', 'Por verificar', 'Cancelada') DEFAULT 'Pendiente',
    FechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FechaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reserva_huesped
        FOREIGN KEY (HuespedID) REFERENCES huespedes(HuespedID)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_reserva_habitacion
        FOREIGN KEY (HabitacionID) REFERENCES habitaciones(HabitacionID)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT chk_fechas CHECK (FechaCheckout > FechaCheckin)
);

-- ============================================
-- DATOS DE PRUEBA
-- ============================================
INSERT INTO habitaciones (Numero, Tipo, PrecioNoche) VALUES
('101', 'Suite Deluxe', 120.00),
('102', 'Suite Ejecutiva', 95.00),
('103', 'Doble Estándar', 65.00),
('104', 'Suite Presidencial', 200.00),
('205', 'Suite Ejecutiva', 95.00),
('312', 'Doble Estándar', 65.00);

INSERT INTO huespedes (NombreCompleto, Cedula, Email, Telefono) VALUES
('María Delgado', 'V-12345678', 'maria.delgado@email.com', '0412-1234567'),
('José Marcano', 'V-28990112', 'jose.marcano@email.com', '0414-9876543'),
('Laura Rivas', 'V-18765432', 'laura.rivas@email.com', '0416-5551234'),
('Carlos Bravo', 'V-14555888', 'carlos.bravo@email.com', '0424-7778888'),
('Andrea Peña', 'V-20333444', 'andrea.pena@email.com', '0412-9990011');

INSERT INTO reservas (HuespedID, HabitacionID, FechaCheckin, FechaCheckout, Estado) VALUES
(1, 1, '2026-09-26', '2026-09-28', 'Confirmada'),
(2, 5, '2026-09-26', '2026-09-30', 'Pendiente'),
(3, 6, '2026-09-26', '2026-09-29', 'Confirmada'),
(4, 4, '2026-09-26', '2026-10-01', 'Por verificar'),
(5, 3, '2026-09-27', '2026-09-29', 'Confirmada');