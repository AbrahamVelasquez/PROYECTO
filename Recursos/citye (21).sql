-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-05-2026 a las 01:18:46
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
-- Base de datos: `citye`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) NOT NULL,
  `dni` varchar(9) DEFAULT NULL,
  `sexo` enum('M','H','Otro') DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `apellido1`, `apellido2`, `dni`, `sexo`, `correo`, `telefono`) VALUES
(1, 'Juan', 'Pérez', 'Sánchez', '12345678Z', 'H', 'juan@alumno.es', '611000001'),
(2, 'Marta', 'García', 'Mesa', '23456789X', 'M', 'marta@alumno.es', '611000002'),
(3, 'Luis', 'Rodríguez', 'Oca', '34567890C', 'H', 'luis@alumno.es', '611000003'),
(4, 'Sofía', 'Alba', 'Rico', '45678901V', 'M', 'sofia@alumno.es', '611000004'),
(5, 'Diego', 'Torres', 'Luna', '56789012B', 'H', 'diego@alumno.es', '611000005'),
(6, 'Lucía', 'Blanco', 'Polo', '67890123N', 'M', 'lucia@alumno.es', '611000006'),
(7, 'Andrés', 'Marín', 'Soler', '78901234M', 'H', 'andres@alumno.es', '611000007'),
(8, 'Alejandro', 'García', 'Pérez', '12345678A', 'H', 'alejandro.garcia@correo.es', '611000001'),
(9, 'Lucía', 'Martín', 'Sánchez', '23456789B', 'M', 'lucia.martin@correo.es', '600333444'),
(10, 'Marc', 'Vila', 'Gómez', '34561890C', 'H', 'marc.vila@correo.co', '645321212'),
(11, 'Elena', 'Sanz', 'Castro', '45678901D', 'M', 'elena.sanz@correo.es', '600777888'),
(12, 'Javier', 'López', 'Ruiz', '56789012E', 'H', 'javier.lopez@correo.es', '600999000'),
(13, 'Marta', 'Ibáñez', 'Torres', '67890123F', 'M', 'marta.ibanez@correo.es', '633241975'),
(14, 'David', 'Jiménez', 'Méndez', '78901234G', 'H', 'david.jimenez@correo.es', '611444555'),
(59, 'Abraham', 'Velásquez', 'Granados', '21565678A', 'H', 'abraham.velasquez@alumno.es', '658873964'),
(60, 'Mariana', 'González', 'Díaz', '76545678A', 'M', 'mariana.gonzalez@alumno.es', '611002001'),
(61, 'María', 'López', 'Pérez', '23456111X', 'M', 'maria.lopez@alumno.es', '611111111'),
(81, 'Carlos', 'Fernández', 'López', '12345678F', 'H', 'carlos.fernandez@alumno.es', '611111111'),
(82, 'Laura', 'Martínez', 'García', '23456789G', 'M', 'laura.martinez@alumno.es', '622222222'),
(83, 'Javier', 'Rodríguez', 'Sánchez', '34567890H', 'H', 'javier.rodriguez@alumno.es', '633333333'),
(84, 'Elena', 'Gómez', 'Pérez', '45678901I', 'M', 'elena.gomez@alumno.es', '644444444'),
(85, 'David', 'López', 'Martín', '56789012J', 'H', 'david.lopez@alumno.es', '655555555'),
(86, 'Marta', 'Sánchez', 'Ruiz', '67890123K', 'M', 'marta.sanchez@alumno.es', '666666666'),
(87, 'Alejandro', 'Ramírez', 'Jiménez', '78901234L', 'H', 'alejandro.ramirez@alumno.es', '677777777'),
(88, 'Sara', 'Torres', 'Molina', '89012345M', 'M', 'sara.torres@alumno.es', '688888888'),
(89, 'Pablo', 'Díaz', 'Ortega', '90123456N', 'H', 'pablo.diaz@alumno.es', '699999999'),
(90, 'Lucía', 'Serrano', 'Navarro', '01234567O', 'M', 'lucia.serrano@alumno.es', '600000000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `num_convenio` varchar(20) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `num_total_horas` int(4) DEFAULT NULL,
  `dias_semana` varchar(50) DEFAULT NULL,
  `horas_dia` decimal(4,2) DEFAULT NULL,
  `enviado` tinyint(1) NOT NULL DEFAULT 0,
  `nombre_tutor_empresa` varchar(150) DEFAULT NULL,
  `correo_tutor_empresa` varchar(150) DEFAULT NULL,
  `tel_tutor_empresa` varchar(20) DEFAULT NULL,
  `horario_excepciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `num_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`, `enviado`, `nombre_tutor_empresa`, `correo_tutor_empresa`, `tel_tutor_empresa`, `horario_excepciones`) VALUES
(1, 1, '7', '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, 'Kevin Gonzalez', NULL, NULL, '[{\"dias\":[\"L\",\"M\",\"X\",\"J\"],\"inicio\":\"08:00\",\"fin\":\"17:00\"},{\"dias\":[\"V\"],\"inicio\":\"08:00\",\"fin\":\"15:00\"}]'),
(2, 2, '4', '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0, NULL, NULL, NULL, NULL),
(4, 4, '4', '2026-05-10', '2026-06-10', '08:00-15:00', 370, 'L-V', 7.00, 1, NULL, NULL, NULL, NULL),
(5, 5, '5', '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 0, NULL, NULL, NULL, NULL),
(6, 6, '6', '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0, NULL, NULL, NULL, NULL),
(7, 7, '7', '2026-03-01', '2026-06-15', '10:00-14:00', 370, 'L-V', 4.00, 0, NULL, NULL, NULL, NULL),
(13, 8, '5', '2026-03-01', '2026-06-15', '08:00-15:00', 370, NULL, 7.00, 1, 'Javier De la Cruz', '', '', NULL),
(14, 9, '4', '2026-03-01', '2026-06-15', '08:00-15:00', 380, 'L-V', 8.00, 1, NULL, NULL, NULL, NULL),
(15, 10, '4', '2026-04-25', '2026-04-30', '09:30-16:00', 350, NULL, 7.00, 1, 'Fernando Martins', 'fer@gmail.es', '1234321999', '[{\"dias\":[\"S\",\"D\"],\"inicio\":\"10:00\",\"fin\":\"16:00\"},{\"dias\":[\"S\",\"D\"],\"inicio\":\"19:00\",\"fin\":\"23:00\"}]'),
(17, 12, '19', '2026-03-01', '2026-06-15', '09:00-17:00', 400, NULL, 8.00, 1, 'Fernando Ríos', 'fer@gmail.com', '1234321567', NULL),
(27, 59, '22', '2026-04-25', '2026-04-30', '09:00-17:00', 380, NULL, 8.00, 1, 'Javier Andre', 'javier@gmail.com', '12312312', NULL),
(30, 83, '37', '2026-03-01', NULL, NULL, 380, 'L-V', 8.00, 0, 'Rosa Fernández', 'rosa.fernandez@amazon.com', '611000003', NULL),
(31, 84, '38', '2026-03-15', '2026-06-20', NULL, 350, 'L-V', NULL, 0, 'Marc Vila', 'marc.vila@glovo.com', '611000004', NULL),
(32, 87, '40', '2026-04-01', '2026-06-30', '09:00-15:00', 370, 'L-V', NULL, 0, 'Laura Martínez', 'laura@mercadona.es', '611000007', NULL),
(33, 88, '41', NULL, '2026-06-25', '08:00-14:00', 360, 'L-V', 6.00, 0, 'Álvaro Gómez', 'alvaro@telefonica.es', '611000008', NULL),
(34, 81, '35', '2026-03-01', '2026-06-15', '08:00-15:00', 380, 'L-V', 7.00, 1, 'Ana Belén García', 'ana.garcia@google.es', '611000001', NULL),
(35, 82, '36', '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-V', 8.00, 1, 'Javier Merino', 'javier.merino@microsoft.com', '611000002', '[{\"dias\":[\"L\",\"M\",\"X\",\"J\"],\"inicio\":\"09:00\",\"fin\":\"18:00\"},{\"dias\":[\"V\"],\"inicio\":\"09:00\",\"fin\":\"15:00\"}]'),
(36, 90, '39', '2026-03-01', '2026-06-15', '08:30-16:30', 380, 'L-V', 8.00, 1, 'José Ignacio Sánchez', 'jose.ignacio@iberdrola.es', '611000009', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_firmadas`
--

CREATE TABLE `asignaciones_firmadas` (
  `id_firmada` int(6) UNSIGNED NOT NULL,
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `exportado` tinyint(1) NOT NULL DEFAULT 0,
  `anexo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaciones_firmadas`
--

INSERT INTO `asignaciones_firmadas` (`id_firmada`, `id_asignacion`, `exportado`, `anexo`) VALUES
(31, 27, 1, 1111),
(35, 4, 0, 7678),
(36, 13, 1, 7487),
(38, 17, 1, 7788),
(42, 34, 0, NULL),
(43, 35, 1, 1),
(44, 36, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL,
  `grado` enum('básico','medio','superior') NOT NULL,
  `linea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`, `grado`, `linea`) VALUES
(1, 2, 'DAW', 'superior', NULL),
(2, 2, 'SMR', 'medio', NULL),
(3, 1, 'DAM', 'superior', NULL),
(4, 2, 'ASIR', 'superior', NULL),
(5, 1, 'DAW', 'superior', NULL),
(6, 1, 'SMR', 'medio', NULL),
(7, 2, 'DAM', 'superior', NULL),
(8, 1, 'ASIR', 'superior', NULL),
(9, 2, 'UCQ', 'básico', NULL),
(10, 1, 'UCQ', 'básico', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios`
--

CREATE TABLE `convenios` (
  `num_convenio` varchar(20) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `representante` varchar(200) DEFAULT NULL,
  `especialidad` int(6) UNSIGNED NOT NULL,
  `fecha_alta_renovacion` date DEFAULT NULL,
  `fecha_nueva_renovacion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios`
--

INSERT INTO `convenios` (`num_convenio`, `nombre_empresa`, `cif`, `direccion`, `localidad`, `cp`, `telefono`, `fax`, `representante`, `especialidad`, `fecha_alta_renovacion`, `fecha_nueva_renovacion`, `observaciones`) VALUES
('1', 'Tech Cloud Solutions', 'B11111111', 'Calle Nube, 1', 'Madrid', '28001', '910000001', '910000002', 'Antonio Recio Pérez', 1, NULL, NULL, NULL),
('19', 'Dev Ops Solutions', 'B11101110', 'Calle Script 11', 'Móstoles', '28932', '910000101', '910000102', 'Maite Figueroa López', 1, NULL, NULL, NULL),
('22', 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', '910000071', '910000072', 'Vicente Figueroa Ruiz', 3, NULL, NULL, NULL),
('29', 'Indra Sistemas S.A.', 'A-28123456', 'Calle del Futuro, 12', 'Alcobendas', '28108', '916 000 111', '917 330 122', 'Alicia Gómez Valles', 7, NULL, NULL, NULL),
('35', 'Google Spain', 'B-87654321', 'Calle de la Pirámide, 1', 'Madrid', '28043', '913753700', '913753701', 'Javier Rodríguez Zapatero', 1, '2026-05-22', '2028-05-22', 'Empresa líder en tecnología y servicios digitales'),
('36', 'Microsoft Ibérica', 'B-12345678', 'Paseo de la Castellana, 259', 'Madrid', '28046', '913750000', '913750001', 'María González López', 1, '2026-05-22', '2028-05-22', 'Desarrollo de software y soluciones cloud'),
('37', 'Amazon Web Services', 'B-98765432', 'Avda. de Europa, 19', 'Pozuelo de Alarcón', '28224', '915674500', '915674501', 'Carlos Méndez Ruiz', 3, '2026-05-22', '2028-05-22', 'Servicios de computación en la nube'),
('38', 'Glovo', 'B-55667788', 'Carrer de Pallars, 85', 'Barcelona', '08018', '931234500', '931234501', 'Anna Ferrer Costa', 1, '2026-05-22', '2027-05-22', 'Plataforma de delivery y logística'),
('39', 'Iberdrola', 'A-12345678', 'Plaza Euskadi, 5', 'Bilbao', '48009', '944801000', '944801001', 'José Ignacio Sánchez', 4, '2026-05-22', '2029-05-22', 'Energía y sostenibilidad'),
('4', 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', '910000031', '910000032', 'Enrique Pastor', 4, NULL, NULL, NULL),
('40', 'Mercadona', 'B-99887766', 'Calle Valencia, 5', 'Valencia', '46001', '963500000', '963500001', 'Laura Martínez Pérez', 2, '2026-05-22', '2028-05-22', 'Distribución alimentaria'),
('41', 'Telefónica', 'A-12345679', 'Gran Vía, 28', 'Madrid', '28013', '913699000', '913699001', 'Álvaro Gómez de la Cruz', 1, '2026-05-22', '2028-05-22', 'Telecomunicaciones y tecnología'),
('42', 'Inditex', 'A-12345680', 'Avda. de la Diputación, s/n', 'Arteixo', '15142', '981185400', '981185401', 'Pablo Isla Álvarez', 2, '2026-05-22', '2028-05-22', 'Moda y retail internacional'),
('43', 'Repsol', 'A-12345681', 'Calle Méndez Álvaro, 44', 'Madrid', '28045', '913488000', '913488001', 'Josu Jon Imaz', 4, '2026-05-22', '2029-05-22', 'Energía y petróleo'),
('44', 'Banco Santander', 'A-12345682', 'Avda. de Cantabria, s/n', 'Santander', '39004', '942201100', '942201101', 'Ana Botín Sanz', 4, '2026-05-22', '2028-05-22', 'Banca y servicios financieros'),
('5', 'Data Flow', 'A55555555', 'Paseo Nodo 5', 'Alcorcón', '28921', '910000041', '910000042', 'Judith Becker', 5, NULL, NULL, NULL),
('6', 'Net Solutions', 'B66666666', 'Calle Router 6', 'Madrid', '28010', '910000051', '910000052', 'Coque Calatrava', 6, NULL, NULL, NULL),
('7', 'Global IT', 'B77777777', 'Av. Mundo 7', 'Móstoles', '28931', '910000061', '910000062', 'Fermín Trujillo', 7, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios_aprobados`
--

CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` date DEFAULT NULL,
  `validado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios_aprobados`
--

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`, `fecha_aprobacion`, `validado`) VALUES
(8, 4, '2026-05-15', 1),
(14, 10, '2026-05-15', 1),
(21, 39, '2026-05-15', 1),
(28, 46, '2026-05-15', 0),
(29, 45, '2026-05-15', 0),
(30, 44, '2026-05-17', 0),
(31, 11, '2026-05-22', 0),
(32, 13, '2026-05-22', 0),
(33, 17, '2026-05-22', 0),
(34, 43, '2026-05-22', 0),
(35, 48, '2026-05-22', 0),
(36, 50, '2026-05-22', 0),
(37, 53, '2026-05-22', 0),
(38, 55, '2026-05-22', 0),
(39, 52, '2026-05-22', 0),
(40, 56, '2026-05-22', 0),
(41, 49, '2026-05-22', 0),
(42, 54, '2026-05-22', 0),
(43, 51, '2026-05-22', 0),
(44, 57, '2026-05-22', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios_nuevos`
--

CREATE TABLE `convenios_nuevos` (
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `especialidad` int(6) UNSIGNED NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `representante` varchar(200) DEFAULT NULL,
  `fecha_nueva_renovacion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios_nuevos`
--

INSERT INTO `convenios_nuevos` (`id_convenio_nuevo`, `especialidad`, `nombre_empresa`, `cif`, `direccion`, `localidad`, `cp`, `telefono`, `fax`, `representante`, `fecha_nueva_renovacion`, `observaciones`) VALUES
(4, 1, 'Zapim Technologies', 'B-12345678', 'Calle de la Tecnología, 45', 'Madrid', '28015', '915556677', '915556678', 'Laura Gómez Sánchez', '2027-05-30', 'Desarrollo de aplicaciones móviles y software a medida'),
(10, 1, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor, 8', 'Madrid', '28015', '910000071', '910000072', 'Vicente Figueroa Ruiz', NULL, 'Desarrollo de sistemas electrónicos y domótica'),
(11, 1, 'Byte Masters Tech', 'B-99911122', 'Paseo del Código, 9', 'Getafe', '28902', '910000081', '910000082', 'Lola Trujillo Martín', NULL, 'Consultoría tecnológica y desarrollo de software'),
(13, 1, 'Dev Ops Solutions', 'B-11101110', 'Calle Script, 11', 'Móstoles', '28932', '910000101', '910000102', 'Maite Figueroa López', NULL, 'Automatización, integración continua y DevOps'),
(17, 1, 'Consultoría Energética del Sur', 'B-77889900', 'Calle Recogidas, 14', 'Granada', '18002', '958998877', '', 'Isabel Ramos Luna', '2027-02-28', 'Eficiencia energética y energías renovables'),
(39, 1, 'Cognodata Group', 'B-11223344', 'Paseo de la Castellana, 140', 'Madrid', '28046', '917263747', '917263748', 'Carlos Mendoza Pérez', '2027-07-15', 'Big Data, inteligencia artificial y analítica avanzada'),
(43, 1, 'Alpha Digital Solutions', 'B-99911122', 'Avenida de la Industria, 4', 'Tres Cantos', '28760', '918002233', '918002234', 'Carlos Mendoza Álvarez', '2027-06-15', 'Desarrollo web, marketing digital y SEO'),
(44, 1, 'Nova Code Systems', 'A-88822233', 'Calle del Software, 12', 'Madrid', '28001', '600123456', '', 'Lucía Gómez Sanz', '2027-05-28', 'Plataformas cloud, soluciones empresariales y ERP'),
(45, 1, 'Sistemas Infinity Tech', 'B-77733344', 'Paseo de la Innovación, 8', 'Getafe', '28902', '916554433', 'FAX', 'Marcos López Ruiz', '2027-07-01', 'Ciberseguridad, auditoría informática y sistemas'),
(46, 1, 'Byte Force Technologies', 'B-11144455', 'Calle Binaria, 3', 'Leganés', '28911', '914005566', '11111', 'Elena Rivas Moreno', NULL, 'Desarrollo de software, apps móviles y videojuegos'),
(48, 1, 'NTT Data Spain', 'B-12345678', 'Calle de la Basílica, 19', 'Madrid', '28020', '915678900', '915678901', 'Juan Carlos López', '2027-06-01', 'Empresa de consultoría tecnológica'),
(49, 3, 'Accenture', 'B-87654321', 'Paseo de la Castellana, 216', 'Madrid', '28046', '913756000', '913756001', 'María Fernández', '2027-05-15', 'Servicios profesionales y consultoría'),
(50, 1, 'HP España', 'B-11122233', 'Calle de la Venta, 31', 'Madrid', '28045', '915876543', '915876544', 'Carlos Rodríguez', '2026-12-31', 'Tecnología y soluciones informáticas'),
(51, 4, 'Deloitte', 'B-44455566', 'Paseo de la Castellana, 89', 'Madrid', '28046', '913750000', '913750001', 'Elena Sánchez', '2027-03-20', 'Consultoría y auditoría'),
(52, 2, 'Leroy Merlin', 'B-77788899', 'Calle Joaquín Costa, 22', 'Madrid', '28002', '913456789', '913456788', 'Ana García', '2027-01-10', 'Materiales de construcción y bricolaje'),
(53, 1, 'Oracle Ibérica', 'B-99887766', 'Paseo de la Castellana, 231', 'Madrid', '28046', '914975000', '914975001', 'José Manuel Ruiz', '2027-08-01', 'Software y bases de datos'),
(54, 3, 'Cisco Systems', 'B-55667788', 'Calle de la Princesa, 1', 'Madrid', '28008', '915123456', '915123457', 'Alberto Díaz', '2027-04-15', 'Redes y telecomunicaciones'),
(55, 1, 'Salesforce Spain', 'B-44332211', 'Paseo de la Castellana, 95', 'Madrid', '28046', '914999000', '914999001', 'Laura Torres', '2027-07-01', 'CRM y soluciones cloud'),
(56, 2, 'Decathlon', 'B-66554433', 'Calle de la Alcalá, 500', 'Madrid', '28027', '916543210', '916543211', 'Marcos López', '2026-11-30', 'Artículos deportivos'),
(57, 4, 'PwC', 'B-33221144', 'Paseo de la Castellana, 259B', 'Madrid', '28046', '913756900', '913756901', 'Isabel Martínez', '2027-09-01', 'Auditoría y consultoría');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`) VALUES
(1, 'Primero'),
(2, 'Segundo'),
(3, 'Tercero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_academico`
--

CREATE TABLE `curso_academico` (
  `id_curso_academico` int(6) UNSIGNED NOT NULL,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `anio_inicio` int(4) NOT NULL,
  `anio_fin` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `curso_academico`
--

INSERT INTO `curso_academico` (`id_curso_academico`, `id_alumno`, `id_ciclo`, `anio_inicio`, `anio_fin`) VALUES
(1, 1, 1, 2025, 2026),
(2, 8, 1, 2025, 2026),
(3, 9, 1, 2025, 2026),
(4, 10, 1, 2025, 2026),
(5, 11, 1, 2025, 2026),
(6, 12, 1, 2025, 2026),
(7, 13, 1, 2025, 2026),
(8, 14, 1, 2025, 2026),
(11, 2, 2, 2025, 2026),
(12, 3, 3, 2025, 2026),
(13, 4, 4, 2025, 2026),
(45, 81, 1, 2025, 2026),
(46, 82, 1, 2025, 2026),
(47, 83, 1, 2025, 2026),
(48, 84, 1, 2025, 2026),
(49, 85, 1, 2025, 2026),
(50, 86, 1, 2025, 2026),
(51, 87, 3, 2025, 2026),
(52, 88, 3, 2025, 2026),
(53, 89, 3, 2025, 2026),
(54, 90, 3, 2025, 2026);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mi_listado`
--

CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `num_convenio` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `mi_listado`
--

INSERT INTO `mi_listado` (`id_tutor`, `num_convenio`) VALUES
(1, '1'),
(1, '4'),
(1, '5'),
(1, '7'),
(2, '6'),
(4, '5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(6) UNSIGNED NOT NULL,
  `nombre_modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nombre_modulo`) VALUES
(156, 'Inglés profesional (GM)'),
(179, 'Inglés profesional (GS)'),
(221, 'Montaje y mantenimiento de equipos'),
(222, 'Sistemas operativos monopuesto'),
(223, 'Aplicaciones ofimáticas'),
(224, 'Sistemas operativos en red'),
(225, 'Redes locales'),
(226, 'Seguridad informática'),
(227, 'Servicios en red'),
(228, 'Aplicaciones web'),
(369, 'Implantación de sistemas operativos'),
(370, 'Planificación y administración de redes'),
(371, 'Fundamentos de hardware'),
(372, 'Gestión de bases de datos'),
(373, 'Lenguajes de marcas y sistemas de gestión de información'),
(374, 'Administración de sistemas operativos'),
(375, 'Servicios de red e internet'),
(376, 'Implantación de aplicaciones web'),
(377, 'Administración de sistemas gestores de bases de datos'),
(378, 'Seguridad y alta disponibilidad'),
(379, 'Proyecto intermodular ASIR'),
(483, 'Sistemas informáticos'),
(484, 'Bases de datos'),
(485, 'Programación'),
(486, 'Acceso a datos'),
(487, 'Entornos de desarrollo'),
(488, 'Desarrollo de interfaces'),
(489, 'Programación multimedia'),
(490, 'Programación de servicios'),
(491, 'Sistemas de gestión empresarial'),
(492, 'Proyecto intermodular DAM'),
(612, 'Desarrollo web en entorno cliente'),
(613, 'Desarrollo web en entorno servidor'),
(614, 'Despliegue de aplicaciones web'),
(615, 'Diseño de interfaces web'),
(616, 'Proyecto intermodular DAW'),
(1664, 'Digitalización (GM)'),
(1665, 'Digitalización (GS)'),
(1708, 'Sostenibilidad'),
(1709, 'IPE I'),
(1710, 'IPE II'),
(1713, 'Proyecto intermodular (SMR)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_estudios`
--

CREATE TABLE `plan_estudios` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_modulo` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `plan_estudios`
--

INSERT INTO `plan_estudios` (`id_ciclo`, `id_modulo`) VALUES
(1, 179),
(1, 612),
(1, 613),
(1, 614),
(2, 156),
(3, 483),
(4, 374),
(6, 221);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados_aprendizaje`
--

CREATE TABLE `resultados_aprendizaje` (
  `id_ra` int(6) UNSIGNED NOT NULL,
  `id_modulo` int(6) UNSIGNED NOT NULL,
  `numero_ra` int(3) NOT NULL,
  `impartido_empresa` tinyint(1) NOT NULL DEFAULT 0,
  `periodo` enum('1','2','3') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `resultados_aprendizaje`
--

INSERT INTO `resultados_aprendizaje` (`id_ra`, `id_modulo`, `numero_ra`, `impartido_empresa`, `periodo`) VALUES
(7, 613, 9, 0, '2'),
(8, 612, 1, 0, '2'),
(9, 614, 10, 0, '2'),
(11, 378, 1, 0, '1'),
(12, 179, 3, 1, '2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

CREATE TABLE `tutores` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `tutores`
--

INSERT INTO `tutores` (`id_tutor`, `id_usuario`, `id_ciclo`, `dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
(1, 2, 1, '11111111A', 'Carlos', 'Gómez', 'carlos@centrofct.es', '600111111'),
(2, 3, 2, '22222222B', 'Laura', 'Sanz', 'laura@centrofct.es', '600222222'),
(3, 4, 3, '33333333C', 'Pablo', 'López', 'pablo@centrofct.es', '600333333'),
(4, 5, 4, '44444444D', 'Elena', 'Martín', 'elena@centrofct.es', '600444444'),
(5, 6, 5, '55555555E', 'Mario', 'García', 'mario@centrofct.es', '600555555'),
(6, 7, 6, '66666666F', 'Ana', 'Ruiz', 'ana@centrofct.es', '600666666'),
(7, 1, 7, '77777777G', 'Roberto', 'Vidal', 'roberto@centrofct.es', '600777777'),
(15, 18, 8, '11221122e', 'Antonio', 'Gómez', 'antonio@centrofct.es', '671527391');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin789', 'admin'),
(2, 'carlos_tutor', 'car123', 'tutor'),
(3, 'laura_tutor', 'lau123', 'tutor'),
(4, 'pablo_tutor', 'pab123', 'tutor'),
(5, 'elena_tutor', 'ele123', 'tutor'),
(6, 'mario_tutor', 'mar123', 'tutor'),
(7, 'ana_tutor', 'ana123', 'tutor'),
(18, 'antonio_tutor', 'ant123', 'tutor');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `fk_asig_alumno` (`id_alumno`),
  ADD KEY `fk_asig_convenio` (`num_convenio`);

--
-- Indices de la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  ADD PRIMARY KEY (`id_firmada`),
  ADD KEY `fk_firmada_asig` (`id_asignacion`);

--
-- Indices de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  ADD PRIMARY KEY (`id_ciclo`),
  ADD KEY `fk_ciclo_curso` (`id_curso`);

--
-- Indices de la tabla `convenios`
--
ALTER TABLE `convenios`
  ADD PRIMARY KEY (`num_convenio`),
  ADD UNIQUE KEY `cif` (`cif`),
  ADD KEY `fk_convenio_ciclo` (`especialidad`);

--
-- Indices de la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  ADD PRIMARY KEY (`id_convenio_aprobado`),
  ADD KEY `fk_aprobado_nuevo` (`id_convenio_nuevo`);

--
-- Indices de la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  ADD PRIMARY KEY (`id_convenio_nuevo`),
  ADD KEY `fk_conv_nuevo_ciclo` (`especialidad`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  ADD PRIMARY KEY (`id_curso_academico`),
  ADD KEY `fk_hist_alumno` (`id_alumno`),
  ADD KEY `fk_hist_ciclo` (`id_ciclo`);

--
-- Indices de la tabla `mi_listado`
--
ALTER TABLE `mi_listado`
  ADD PRIMARY KEY (`id_tutor`,`num_convenio`),
  ADD KEY `fk_fav_convenio` (`num_convenio`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `plan_estudios`
--
ALTER TABLE `plan_estudios`
  ADD PRIMARY KEY (`id_ciclo`,`id_modulo`),
  ADD KEY `fk_plan_modulo` (`id_modulo`);

--
-- Indices de la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  ADD PRIMARY KEY (`id_ra`),
  ADD KEY `fk_ra_modulo` (`id_modulo`);

--
-- Indices de la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD PRIMARY KEY (`id_tutor`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `fk_tutor_ciclo` (`id_ciclo`),
  ADD KEY `fk_tutor_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  MODIFY `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  MODIFY `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  MODIFY `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  MODIFY `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  MODIFY `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1714;

--
-- AUTO_INCREMENT de la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  MODIFY `id_ra` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tutores`
--
ALTER TABLE `tutores`
  MODIFY `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asig_convenio` FOREIGN KEY (`num_convenio`) REFERENCES `convenios` (`num_convenio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  ADD CONSTRAINT `fk_firmada_asig` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ciclos`
--
ALTER TABLE `ciclos`
  ADD CONSTRAINT `fk_ciclo_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `convenios`
--
ALTER TABLE `convenios`
  ADD CONSTRAINT `fk_convenio_ciclo` FOREIGN KEY (`especialidad`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  ADD CONSTRAINT `fk_aprobado_nuevo` FOREIGN KEY (`id_convenio_nuevo`) REFERENCES `convenios_nuevos` (`id_convenio_nuevo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  ADD CONSTRAINT `fk_conv_nuevo_ciclo` FOREIGN KEY (`especialidad`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  ADD CONSTRAINT `fk_hist_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hist_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mi_listado`
--
ALTER TABLE `mi_listado`
  ADD CONSTRAINT `fk_fav_convenio` FOREIGN KEY (`num_convenio`) REFERENCES `convenios` (`num_convenio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `plan_estudios`
--
ALTER TABLE `plan_estudios`
  ADD CONSTRAINT `fk_plan_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plan_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  ADD CONSTRAINT `fk_ra_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD CONSTRAINT `fk_tutor_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
