-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-05-2026 a las 20:55:49
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
-- Base de datos: `citye2`
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
(57, 'MARCOS Fabian', 'García', 'Gonzalez', '36719012E', 'H', 'marcosf@gmail.com', '671478574'),
(58, 'JOSUE', 'García', 'Sánchez', '2345678WD', 'H', 'josue@gmail.com', '685412154'),
(59, 'Abraham', 'Velasquez', 'Granados', '21565678A', 'H', 'abraham@gmail.com', '658873964'),
(60, 'Mariana', 'Gonzalez', 'Díaz', '76545678A', 'M', 'mariana@gmail.com', '611002001'),
(61, 'maria', 'López', 'Pérez', '23456111X', 'M', 'marialo@gmail.com', '685093482'),
(77, 'Alejandro', 'López', 'Ruiz', NULL, 'H', '', ''),
(78, 'ABRAHAM JOSUé', 'VELáSQUEZ', 'GRANADOS', '56787012M', 'H', 'ajvg80@educa.madrid.org', ''),
(79, 'ABRAHAM JOSUé', 'VELáSQUEZ', 'GRANADOS', NULL, NULL, 'ajvg80@educa.madrid.org', NULL),
(80, 'aaaaaeeeeeeeee', 'aaaaaaaaa', 'wwwwwwww', '12CA5678A', 'H', 'aaaaaaaaa@educa.madrid.org', '611000001');

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
(1, 1, '7', '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, 'Kevin Gonzalez', NULL, NULL, NULL),
(2, 2, '4', '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0, NULL, NULL, NULL, NULL),
(3, 3, '3', '2026-03-01', '2026-06-15', '08:30-14:30', 370, 'L-V', 6.00, 0, NULL, NULL, NULL, NULL),
(4, 4, '4', '2026-05-10', '2026-06-10', '08:00-15:00', 370, 'L-V', 7.00, 1, NULL, NULL, NULL, NULL),
(5, 5, '5', '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 0, NULL, NULL, NULL, NULL),
(6, 6, '6', '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0, NULL, NULL, NULL, NULL),
(7, 7, '7', '2026-03-01', '2026-06-15', '10:00-14:00', 370, 'L-V', 4.00, 0, NULL, NULL, NULL, NULL),
(13, 8, '5', '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, 'Javier De la Cruz', NULL, NULL, NULL),
(14, 9, '4', '2026-03-01', '2026-06-15', '08:00-15:00', 380, 'L-V', 8.00, 1, NULL, NULL, NULL, NULL),
(15, 10, '4', '2026-04-25', '2026-04-30', '09:30-16:00', 350, NULL, 7.00, 1, 'Fernando Martins', 'fer@gmail.es', '1234321999', NULL),
(17, 12, '19', '2026-03-01', '2026-06-15', '09:00-17:00', 400, NULL, 8.00, 1, 'Fernando Ríos', 'fer@gmail.com', '1234321567', NULL),
(27, 59, '22', '2026-04-25', '2026-04-30', '09:00-17:00', 380, NULL, 8.00, 1, 'Javier Andre', 'javier@gmail.com', '12312312', NULL),
(29, 58, '1', '2026-04-18', '2026-04-30', '08:00-16:00', 380, NULL, 7.00, 1, 'Leornardo Fernandez', 'leornardo.guti@techcloud.com', '1231231231', NULL);

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
(39, 14, 0, 3333);

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
('1', 'Tech Cloud', 'B11111111', 'Calle Nube 1', 'Madrid', '28001', '910000001', '910000002', 'Antonio Recio', 1, NULL, NULL, NULL),
('19', 'Dev Ops', 'B11101110', 'Calle Script 11', 'Móstoles', '28932', '910000101', '910000102', 'Maite Figueroa', 1, NULL, NULL, NULL),
('22', 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', '910000071', '910000072', 'Vicente Figueroa', 3, NULL, NULL, NULL),
('25', 'CLOUD SECURE S.A', 'B10101010', 'AVENIDA DEL LINK 10', 'LEGANéS', '28912', '910000091', '910000092', 'JAVIER MAROTO', 1, NULL, NULL, NULL),
('29', 'Indra Sistemas S.A.', 'A-28123456', 'Calle del Futuro, 12', 'Alcobendas', '28108', '916 000 111', '917 330 122', 'Alicia Gómez Valles', 7, NULL, NULL, NULL),
('3', 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', '910000021', '910000022', 'Amador Rivas', 1, NULL, NULL, NULL),
('30', 'FARMACIA LITERATOS ', '434443', 'AV. DE LOS LABRADOR', 'TRES CANT', '444444', '2222222', '11111', 'francisco ', 1, '2026-05-18', '2026-06-06', 'impresionante es esto'),
('32', 'PAscual Variantes S.L', '255555', 'AV. DE LOS LABRADOR', 'TRES CANT', '2876', '92818222', '382811', 'ANA PASC', 1, '2026-05-17', '2026-05-31', 'Algo importante en verdad'),
('4', 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', '910000031', '910000032', 'Enrique Pastor', 4, NULL, NULL, NULL),
('5', 'Data Flow', 'A55555555', 'Paseo Nodo 5', 'Alcorcón', '28921', '910000041', '910000042', 'Judith Becker', 5, NULL, NULL, NULL),
('6', 'Net Solutions', 'B66666666', 'Calle Router 6', 'Madrid', '28010', '910000051', '910000052', 'Coque Calatrava', 6, NULL, NULL, NULL),
('7', 'Global IT', 'B77777777', 'Av. Mundo 7', 'Móstoles', '28931', '910000061', '910000062', 'Fermín Trujillo', 7, NULL, NULL, NULL),
('A001', 'IES ISAAC NEWTON', 'Q2868567E', 'Calle Joaquín Lorenzo, 2º', 'Madrid', '28035', '913732052', NULL, 'Isabel Gallego Gallego', 1, '2025-02-11', '2029-02-11', NULL),
('A002', 'IES Albert Einstein', 'Q2868567D', 'Calle Joaquín Lorenzo, 2º', 'Madrid', '28035', '913732052', '', 'Andrea Gallego Sevilla', 8, '2025-02-20', '2029-03-20', 'otro mundo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios_aprobados`
--

CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` date NOT NULL,
  `validado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios_aprobados`
--

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`, `fecha_aprobacion`, `validado`) VALUES
(8, 4, '2026-05-15', 1),
(14, 10, '2026-05-15', 1),
(20, 31, '2026-05-15', 1),
(21, 39, '2026-05-15', 1),
(22, 28, '0000-00-00', 1),
(25, 25, '0000-00-00', 1),
(26, 23, '0000-00-00', 1),
(27, 47, '0000-00-00', 0),
(28, 46, '0000-00-00', 0),
(29, 45, '0000-00-00', 0),
(30, 44, '2026-05-17', 0);

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
(4, 1, 'ZAPIM', 'FIC', 'DIRECCION', 'MUNICIPIO', 'CP', 'TELEFONO', 'FAX', 'nombre', NULL, NULL),
(10, 1, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', '910000071', '910000072', 'Vicente Figueroa', NULL, NULL),
(11, 1, 'Byte Masters', 'A99999999', 'Paseo del Código 9', 'Getafe', '28902', '910000081', '910000082', 'Lola Trujillo', NULL, NULL),
(12, 1, 'CLOUD SECURE S.A', 'B10101010', 'AVENIDA DEL LINK 10', 'LEGANéS', '28912', '910000091', '910000092', 'JAVIER MAROTO', NULL, NULL),
(13, 1, 'Dev Ops', 'B11101110', 'Calle Script 11', 'Móstoles', '28932', '910000101', '910000102', 'Maite Figueroa', NULL, NULL),
(17, 1, 'Consultoría Energética', 'B77889900', 'Calle Recogidas 14', 'Granada', '18002', '958998877', '', 'Isabel Ramos Luna', NULL, NULL),
(18, 1, 'SISTEMAS DE DATOS CANTáBRICO', 'A44332211', 'CALLE REAL 88', 'SANTANDER', '39001', '942556677', '942556678', 'FERNANDO RUIZ DEHESA', NULL, NULL),
(23, 1, 'FARMATODO ', '123123', 'TRES CA 16', 'MARACAY', '22119', '611000000', 'FAX', 'FARMACIAS SA', '2026-05-21', 'Muy buena farmacia'),
(25, 1, 'PAscual Variantes S.L', '255555', 'AV. DE LOS LABRADOR', 'TRES CANT', '2876', '92818222', '382811', 'ANA PASC', '2026-05-31', 'Algo importante en verdad'),
(28, 1, 'FARMACIA LITERATOS ', '434443', 'AV. DE LOS LABRADOR', 'TRES CANT', '444444', '2222222', '11111', 'francisco ', NULL, 'impresionante'),
(31, 1, 'FARMATODO ', '123123', 'TRES CA 16', 'MARACAY', '22119', '611000000', 'FAX', 'FARMACIAS SA', NULL, NULL),
(39, 1, 'COGNODATA', 'CIF', 'DIRECCION', 'cojedes', 'CP', '726182743', 'FAX', 'NOMBRE', NULL, NULL),
(43, 1, 'Alpha Digital S.L.', 'B99911122', 'Av. de la Industria 4', 'Tres Cantos', '28760', '918002233', '918002234', 'Carlos Mendoza', '2026-06-15', 'Interesados en perfiles de desarrollo web.'),
(44, 1, 'NOVA CODE S.A.', 'A88822233', 'CALLE DEL SOFTWARE 12', 'MADRID', '28001', '600123456', '', 'LUCíA GóMEZ', '2026-05-28', NULL),
(45, 1, 'Sistemas Infinity', 'B77733344', 'Paseo de la Innovación 8', 'Getafe', '28902', '916554433', 'FAX', 'Marcos López', '2026-07-01', 'Pendiente de asignar tutor académico.'),
(46, 1, 'Byte Force', 'B11144455', 'Calle Binaria 3', 'Leganés', '28911', '914005566', '11111', 'Elena Rivas', NULL, 'Empresa muy recomendada.'),
(47, 5, 'Apex Solutions', 'B22255566', 'Av. Principal 45', 'Alcobendas', '28108', '722334455', '', 'David Castro', '2026-05-25', 'rgdfgdfgdfg');

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
(42, 78, 4, 2026, 2027),
(43, 79, 1, 2026, 2027),
(44, 80, 1, 2026, 2027);

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
(3, '3'),
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
  MODIFY `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  MODIFY `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  MODIFY `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  MODIFY `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  MODIFY `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  MODIFY `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
