-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-10-2026 a las 01:16:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biotel_suites`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `HabitacionID` int(11) NOT NULL,
  `Numero` varchar(10) NOT NULL,
  `Tipo` enum('Suite Deluxe','Suite Ejecutiva','Doble Estándar','Suite Presidencial','Suite Junior','Familiar') NOT NULL,
  `Capacidad` int(11) NOT NULL DEFAULT 2,
  `Camas` int(11) NOT NULL DEFAULT 1,
  `Piso` int(11) DEFAULT 1,
  `Descripcion` text DEFAULT NULL,
  `Amenidades` text DEFAULT NULL,
  `PrecioNoche` decimal(10,2) NOT NULL,
  `Estado` enum('Disponible','Ocupada','Mantenimiento') DEFAULT 'Disponible',
  `Activo` tinyint(1) DEFAULT 1,
  `FechaActualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`HabitacionID`, `Numero`, `Tipo`, `Capacidad`, `Camas`, `Piso`, `Descripcion`, `Amenidades`, `PrecioNoche`, `Estado`, `Activo`, `FechaActualizacion`) VALUES
(1, '101', 'Suite Deluxe', 2, 1, 1, NULL, NULL, 120.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(2, '102', 'Suite Ejecutiva', 2, 1, 1, NULL, NULL, 95.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(3, '103', 'Doble Estándar', 2, 1, 1, NULL, NULL, 65.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(4, '104', 'Suite Presidencial', 2, 1, 1, NULL, NULL, 200.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(5, '205', 'Suite Ejecutiva', 2, 1, 1, NULL, NULL, 95.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(6, '312', 'Doble Estándar', 2, 1, 1, NULL, NULL, 65.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(7, '105', 'Suite Junior', 2, 1, 1, 'Suite compacta ideal para parejas.', 'WiFi,A/C,TV 42\",Minibar', 85.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(8, '201', 'Familiar', 4, 2, 2, 'Amplia habitación para familias.', 'WiFi,A/C,TV 55\",Cocina,Nevera', 140.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(9, '202', 'Suite Deluxe', 3, 2, 2, 'Vista al jardín, balcón privado.', 'WiFi,A/C,TV 50\",Jacuzzi,Balcón', 125.00, 'Disponible', 1, '2026-10-09 17:23:40'),
(10, '310', 'Suite Presidencial', 4, 2, 3, 'Penthouse con vista panorámica.', 'WiFi,A/C,TV 65\",Jacuzzi,Sala,Amenities VIP', 250.00, 'Disponible', 1, '2026-10-09 17:23:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `huespedes`
--

CREATE TABLE `huespedes` (
  `HuespedID` int(11) NOT NULL,
  `NombreCompleto` varchar(150) NOT NULL,
  `Cedula` varchar(20) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `Nacionalidad` varchar(50) DEFAULT 'Venezolana',
  `FechaRegistro` datetime DEFAULT current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1,
  `FechaActualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `huespedes`
--

INSERT INTO `huespedes` (`HuespedID`, `NombreCompleto`, `Cedula`, `Password`, `Email`, `Telefono`, `Direccion`, `FechaNacimiento`, `Nacionalidad`, `FechaRegistro`, `Activo`, `FechaActualizacion`) VALUES
(1, 'María Delgado', 'V-12345678', NULL, 'maria.delgado@email.com', '0412-1234567', NULL, NULL, 'Venezolana', '2026-10-09 17:23:40', 1, '2026-10-09 17:23:40'),
(2, 'José Marcano', 'V-28990112', NULL, 'jose.marcano@email.com', '0414-9876543', NULL, NULL, 'Venezolana', '2026-10-09 17:23:40', 1, '2026-10-09 17:23:40'),
(3, 'Laura Rivas', 'V-18765432', NULL, 'laura.rivas@email.com', '0416-5551234', NULL, NULL, 'Venezolana', '2026-10-09 17:23:40', 1, '2026-10-09 17:23:40'),
(4, 'Carlos Bravo', 'V-14555888', NULL, 'carlos.bravo@email.com', '0424-7778888', NULL, NULL, 'Venezolana', '2026-10-09 17:23:40', 1, '2026-10-09 17:23:40'),
(5, 'Andrea Peña', 'V-20333444', NULL, 'andrea.pena@email.com', '0412-9990011', NULL, NULL, 'Venezolana', '2026-10-09 17:23:40', 1, '2026-10-09 17:23:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `ReservaID` int(11) NOT NULL,
  `HuespedID` int(11) NOT NULL,
  `HabitacionID` int(11) NOT NULL,
  `FechaCheckin` date NOT NULL,
  `FechaCheckout` date NOT NULL,
  `Estado` enum('Pendiente','Confirmada','Por verificar','Cancelada') DEFAULT 'Pendiente',
  `FechaCreacion` datetime DEFAULT current_timestamp(),
  `FechaActualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`ReservaID`, `HuespedID`, `HabitacionID`, `FechaCheckin`, `FechaCheckout`, `Estado`, `FechaCreacion`, `FechaActualizacion`) VALUES
(1, 1, 1, '2026-09-26', '2026-09-28', 'Confirmada', '2026-10-09 17:23:40', '2026-10-09 17:23:40'),
(2, 2, 5, '2026-09-26', '2026-09-30', 'Pendiente', '2026-10-09 17:23:40', '2026-10-09 17:23:40'),
(3, 3, 6, '2026-09-26', '2026-09-29', 'Confirmada', '2026-10-09 17:23:40', '2026-10-09 17:23:40'),
(4, 4, 4, '2026-09-26', '2026-10-01', 'Por verificar', '2026-10-09 17:23:40', '2026-10-09 17:23:40'),
(5, 5, 3, '2026-09-27', '2026-09-29', 'Confirmada', '2026-10-09 17:23:40', '2026-10-09 17:23:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_sistema`
--

CREATE TABLE `usuarios_sistema` (
  `UsuarioID` int(11) NOT NULL,
  `NombreCompleto` varchar(150) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rol` enum('Recepcionista','Gerente','Admin') DEFAULT 'Recepcionista',
  `Activo` tinyint(1) DEFAULT 1,
  `FechaRegistro` datetime DEFAULT current_timestamp(),
  `FechaActualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_sistema`
--

INSERT INTO `usuarios_sistema` (`UsuarioID`, `NombreCompleto`, `Email`, `Password`, `Rol`, `Activo`, `FechaRegistro`, `FechaActualizacion`) VALUES
(1, 'Ana Castillo', 'recepcion@biotelsuites.com', '123456', 'Recepcionista', 1, '2026-10-09 18:16:23', '2026-10-09 18:16:48'),
(2, 'Carlos Méndez', 'gerente@biotelsuites.com', '123456', 'Gerente', 1, '2026-10-09 18:16:23', '2026-10-09 18:16:57'),
(3, 'Santiago Salazar', 'santiago@gmail.com', '$2y$10$eCIxpkpYO7ju1U5ZDbPxpO5xDgZ0399QH1/gud7anDNiN/I1emHry', 'Recepcionista', 1, '2026-10-09 18:48:48', '2026-10-09 18:48:48'),
(4, 'Alvaro Diaz', 'Alvaro@gmail.com', '$2y$10$F6uDI0Hz0d981aOpAnAzYOJxabmtz7T/YNhryWm27OUfWqrePDQ6e', 'Gerente', 1, '2026-10-09 19:03:20', '2026-10-09 19:03:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`HabitacionID`),
  ADD UNIQUE KEY `Numero` (`Numero`),
  ADD KEY `idx_habitaciones_activo` (`Activo`),
  ADD KEY `idx_habitaciones_tipo` (`Tipo`);

--
-- Indices de la tabla `huespedes`
--
ALTER TABLE `huespedes`
  ADD PRIMARY KEY (`HuespedID`),
  ADD UNIQUE KEY `Cedula` (`Cedula`),
  ADD KEY `idx_huespedes_activo` (`Activo`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`ReservaID`),
  ADD KEY `fk_reserva_huesped` (`HuespedID`),
  ADD KEY `fk_reserva_habitacion` (`HabitacionID`);

--
-- Indices de la tabla `usuarios_sistema`
--
ALTER TABLE `usuarios_sistema`
  ADD PRIMARY KEY (`UsuarioID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `HabitacionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `huespedes`
--
ALTER TABLE `huespedes`
  MODIFY `HuespedID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `ReservaID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios_sistema`
--
ALTER TABLE `usuarios_sistema`
  MODIFY `UsuarioID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reserva_habitacion` FOREIGN KEY (`HabitacionID`) REFERENCES `habitaciones` (`HabitacionID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reserva_huesped` FOREIGN KEY (`HuespedID`) REFERENCES `huespedes` (`HuespedID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
