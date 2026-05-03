-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 05:35:47
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
  `dni` varchar(9) NOT NULL,
  `sexo` enum('M','H','Otro') NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL
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
(10, 'Marc', 'Vila', 'Gómez', '34561890C', 'H', 'marc.vila@correo.es', '600555666'),
(11, 'Elena', 'Sanz', 'Castro', '45678901D', 'M', 'elena.sanz@correo.es', '600777888'),
(12, 'Javier', 'López', 'Ruiz', '56789012E', 'H', 'javier.lopez@correo.es', '600999000'),
(13, 'Marta', 'Ibáñez', 'Torres', '67890123F', 'M', 'marta.ibanez@correo.es', '2'),
(14, 'David', 'Jiménez', 'Méndez', '78901234G', 'H', 'david.jimenez@correo.es', '611444555'),
(57, 'MARCOS Fabian', 'García', 'Gonzalez', '36719012E', 'H', 'marcosf@gmail.com', '671478574'),
(58, 'JOSUE', 'García', 'Sánchez', '2345678WD', 'H', 'josue@gmail.com', '685412154'),
(59, 'Abraham', 'Velasquez', 'Granados', '21565678A', 'H', 'abraham@gmail.com', '658873964'),
(60, 'Mariana', 'Gonzalez', 'Díaz', '76545678A', 'M', 'mariana@gmail.com', '611002001'),
(61, 'maria', 'López', 'Pérez', '23456111X', 'M', 'marialo@gmail.com', '122123243');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `num_total_horas` int(4) DEFAULT NULL,
  `dias_semana` varchar(50) DEFAULT NULL,
  `horas_dia` decimal(4,2) DEFAULT NULL,
  `enviado` tinyint(1) NOT NULL DEFAULT 0,
  `nombre_tutor_empresa` varchar(150) DEFAULT NULL,
  `correo_tutor_empresa` varchar(150) DEFAULT NULL,
  `tel_tutor_empresa` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`, `enviado`, `nombre_tutor_empresa`, `correo_tutor_empresa`, `tel_tutor_empresa`) VALUES
(1, 1, 7, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, '', '', ''),
(2, 2, 4, '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0, NULL, NULL, NULL),
(3, 3, 3, '2026-03-01', '2026-06-15', '08:30-14:30', 370, 'L-V', 6.00, 0, NULL, NULL, NULL),
(4, 4, 4, '2026-05-10', '2026-06-10', '08:00-15:00', 100, 'L-V', 7.00, 0, NULL, NULL, NULL),
(5, 5, 5, '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 0, NULL, NULL, NULL),
(6, 6, 6, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0, NULL, NULL, NULL),
(7, 7, 7, '2026-03-01', '2026-06-15', '10:00-14:00', 370, 'L-V', 4.00, 0, NULL, NULL, NULL),
(13, 8, 5, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, 'Javier', '', ''),
(14, 9, 5, '2026-03-01', '2026-06-15', NULL, 0, 'L-V', 0.00, 0, NULL, NULL, NULL),
(15, 10, 4, '2026-04-25', '2026-04-30', '08:30-14:30', 370, 'L-V', 7.00, 1, NULL, NULL, NULL),
(17, 12, 19, '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 1, NULL, NULL, NULL),
(27, 59, 22, '2026-04-25', '2026-04-30', '09:00-17:00', NULL, NULL, 8.00, 1, 'Javier Antonio', 'javier@gmail.com', '12312312'),
(28, 13, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(29, 58, 1, '2026-04-18', '2026-04-30', '08:00-16:00', NULL, NULL, 7.00, 1, 'Leornardo', 'leornardo.guti@techcloud.com', '1231231231');

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
(22, 1, 1, 1111),
(24, 17, 1, NULL),
(26, 13, 1, NULL),
(27, 29, 1, 111),
(29, 27, 1, 21),
(30, 15, 0, 121);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL,
  `grado` enum('Básica','Media','Superior') NOT NULL,
  `linea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`, `grado`, `linea`) VALUES
(1, 2, 'DAW', 'Superior', NULL),
(2, 2, 'SMR', 'Media', NULL),
(3, 1, 'DAM', 'Superior', NULL),
(4, 2, 'ASIR', 'Superior', NULL),
(5, 1, 'DAW', 'Superior', NULL),
(6, 1, 'SMR', 'Media', NULL),
(7, 2, 'DAM', 'Superior', NULL),
(8, 1, 'ASIR', 'Superior', NULL),
(9, 2, 'UCQ', 'Básica', NULL),
(10, 1, 'UCQ', 'Básica', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios`
--

CREATE TABLE `convenios` (
  `id_convenio` int(6) UNSIGNED NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fax` varchar(15) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `nombre_representante` varchar(150) NOT NULL,
  `dni_representante` varchar(9) NOT NULL,
  `cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios`
--

INSERT INTO `convenios` (`id_convenio`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `fax`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(1, 'Tech Cloud', 'B11111111', 'Calle Nube 1', 'Madrid', '28001', 'España', '910000001', '910000002', 'info@techcloud.es', 'Antonio Recio', '11122233K', 'CEO'),
(3, 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', 'España', '910000021', '910000022', 'hola@webdesign.es', 'Amador Rivas', '33344455M', 'Gerente'),
(4, 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', 'España', '910000031', '910000032', 'admin@cyberguard.es', 'Enrique Pastor', '44455566N', 'Concejal IT'),
(5, 'Data Flow', 'A55555555', 'Paseo Nodo 5', 'Alcorcón', '28921', 'España', '910000041', '910000042', 'jobs@dataflow.es', 'Judith Becker', '55566677O', 'RRHH'),
(6, 'Net Solutions', 'B66666666', 'Calle Router 6', 'Madrid', '28010', 'España', '910000051', '910000052', 'pyme@netsolutions.es', 'Coque Calatrava', '66677788P', 'Técnico Jefe'),
(7, 'Global IT', 'B77777777', 'Av. Mundo 7', 'Móstoles', '28931', 'España', '910000061', '910000062', 'ventas@globalit.es', 'Fermín Trujillo', '77788899Q', 'Comercial'),
(19, 'Dev Ops', 'B11101110', 'Calle Script 11', 'Móstoles', '28932', 'España', '910000101', '910000102', 'dev@devops.es', 'Maite Figueroa', '11122233U', 'Gestora de Calidad'),
(22, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', 'España', '910000071', '910000072', 'admin@logicgate.es', 'Vicente Figueroa', '88899900R', 'Jefe de Sistemas'),
(25, 'CLOUD SECURE S.A', 'B10101010', 'AVENIDA DEL LINK 10', 'LEGANéS', '28912', 'ESPAñA', '910000091', '910000092', 'soporte@cloudsecure.es', 'JAVIER MAROTO', '10101010T', 'PROJECT MANAGER');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios_aprobados`
--

CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `agregado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios_aprobados`
--

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`, `fecha_aprobacion`, `agregado`) VALUES
(8, 4, '2026-04-16 13:12:07', 1),
(9, 13, '2026-04-16 15:46:39', 1),
(11, 18, '2026-04-16 16:26:43', 1),
(12, 17, '2026-04-16 16:26:45', 1),
(14, 10, '2026-04-16 18:02:42', 1),
(17, 23, '2026-04-17 22:47:02', 1),
(18, 12, '2026-04-18 13:11:13', 1),
(19, 36, '2026-04-18 23:23:56', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convenios_nuevos`
--

CREATE TABLE `convenios_nuevos` (
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fax` varchar(15) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `nombre_representante` varchar(150) NOT NULL,
  `dni_representante` varchar(9) NOT NULL,
  `cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `convenios_nuevos`
--

INSERT INTO `convenios_nuevos` (`id_convenio_nuevo`, `id_ciclo`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `fax`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(4, 1, 'ZAPIM', 'FIC', 'DIRECCION', 'MUNICIPIO', 'CP', 'ESPAÑA', 'TELEFONO', 'FAX', 'EMAIL@email.com', 'nombre', 'dni', 'cargo'),
(10, 1, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', 'España', '910000071', '910000072', 'admin@logicgate.es', 'Vicente Figueroa', '88899900R', 'Jefe de Sistemas'),
(11, 1, 'Byte Masters', 'A99999999', 'Paseo del Código 9', 'Getafe', '28902', 'España', '910000081', '910000082', 'hola@bytemasters.es', 'Lola Trujillo', '99900011S', 'Directora de Arte'),
(12, 1, 'CLOUD SECURE S.A', 'B10101010', 'AVENIDA DEL LINK 10', 'LEGANéS', '28912', 'ESPAñA', '910000091', '910000092', 'soporte@cloudsecure.es', 'JAVIER MAROTO', '10101010T', 'PROJECT MANAGER'),
(13, 1, 'Dev Ops', 'B11101110', 'Calle Script 11', 'Móstoles', '28932', 'España', '910000101', '910000102', 'dev@devops.es', 'Maite Figueroa', '11122233U', 'Gestora de Calidad'),
(17, 1, 'Consultoría Energética', 'B77889900', 'Calle Recogidas 14', 'Granada', '18002', 'España', '958998877', '', 'proyectos@conenergia.es', 'Isabel Ramos Luna', '44556677K', 'Gerente'),
(18, 1, 'SISTEMAS DE DATOS CANTáBRICO', 'A44332211', 'CALLE REAL 88', 'SANTANDER', '39001', 'ESPAñA', '942556677', '942556678', 'admin@sdcantabrico.net', 'FERNANDO RUIZ DEHESA', '55667788L', 'DIRECTOR IT'),
(23, 1, 'FARMATODO', '123123', 'AV DE LAS DELICIAS', 'MARACAY', '22119', 'ESPAÑA', '212312321', 'FAX', 'mail@mail.com', 'NOMBRE APELLIDOS', 'DNI', 'CARGO'),
(25, 1, 'PASCUAL VARIANTES', '234234', 'AV. DE LOS LABRADORES', 'TRES CANTOS', '28760', 'ESPAÑA', '92818910', '382823', 'pascualvariantes@gmail.com', 'ANA PASCUAL', '3190238', 'MANAGER'),
(28, 1, 'FARMACIA LITERATOS S.A', '3234', 'AV. DE LOS LABRADORES', 'TRES CANTOS', '28760', 'ESPAÑA', '2409239', '21312', 'farmaliteratos@gmail.com', 'ANGELA', '4238490', 'MANAGER'),
(31, 1, 'FARMATODO S.A', '123123', 'TRES CA 16', 'MARACAY', '22119', 'ESPAÑA', '611000001', 'FAX', 'asdasdsdad@gmail.com', 'ASDASDASD ASDASD ASD ASD', 'DNI', 'ASD'),
(36, 1, 'gggggggggggggg', 'QQQQQQQQ', 'EEEEEEEEEEE', 'DDDDDDDDDD', 'rrrrrrrrrr', 'ESPAÑA', 'vvvvvv', 'xxxxxxxxxx', 'bbbbbbbbb@bb.com', 'TTTTTTTT', 'TTYYY', 'UUUUUUUU');

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
(2, 'Segundo');

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
(9, 57, 1, 2025, 2026),
(10, 58, 1, 2025, 2026),
(11, 2, 2, 2025, 2026),
(12, 3, 3, 2025, 2026),
(13, 4, 4, 2025, 2026),
(14, 5, 5, 2025, 2026),
(15, 6, 6, 2025, 2026),
(16, 7, 7, 2025, 2026),
(32, 59, 1, 2025, 2026),
(33, 60, 1, 2026, 2027),
(34, 61, 1, 2026, 2027);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mi_listado`
--

CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `mi_listado`
--

INSERT INTO `mi_listado` (`id_tutor`, `id_convenio`) VALUES
(1, 1),
(1, 4),
(1, 5),
(1, 7),
(1, 19),
(1, 22),
(1, 25),
(2, 6),
(3, 3),
(4, 5),
(5, 7);

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
(379, 'Proyecto intermodular de administración de sistemas informáticos en red'),
(483, 'Sistemas informáticos'),
(484, 'Bases de datos'),
(485, 'Programación'),
(486, 'Acceso a datos'),
(487, 'Entornos de desarrollo'),
(488, 'Desarrollo de interfaces'),
(489, 'Programación multimedia y dispositivos móviles'),
(490, 'Programación de servicios y procesos'),
(491, 'Sistemas de gestión empresarial'),
(492, 'Proyecto intermodular de desarrollo de aplicaciones multiplataforma'),
(612, 'Desarrollo web en entorno cliente'),
(613, 'Desarrollo web en entorno servidor'),
(614, 'Despliegue de aplicaciones web'),
(615, 'Diseño de interfaces web'),
(616, 'Proyecto intermodular de desarrollo de aplicaciones web'),
(1664, 'Digitalización aplicada a los sectores productivos (GM)'),
(1665, 'Digitalización aplicada a los sectores productivos (GS)'),
(1708, 'Sostenibilidad aplicada al sistema productivo'),
(1709, 'Itinerario personal para la empleabilidad I'),
(1710, 'Itinerario personal para la empleabilidad II'),
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
(1, 615),
(1, 616),
(1, 1665),
(1, 1708),
(1, 1710),
(2, 156),
(2, 224),
(2, 226),
(2, 227),
(2, 228),
(2, 1664),
(2, 1708),
(2, 1710),
(2, 1713),
(3, 373),
(3, 483),
(3, 484),
(3, 485),
(3, 487),
(3, 1709),
(4, 179),
(4, 374),
(4, 375),
(4, 376),
(4, 377),
(4, 378),
(4, 379),
(4, 1665),
(4, 1708),
(4, 1710),
(5, 373),
(5, 483),
(5, 484),
(5, 485),
(5, 487),
(5, 1709),
(6, 221),
(6, 222),
(6, 223),
(6, 225),
(6, 1709),
(7, 179),
(7, 486),
(7, 488),
(7, 489),
(7, 490),
(7, 491),
(7, 492),
(7, 1665),
(7, 1708),
(7, 1710),
(8, 369),
(8, 370),
(8, 371),
(8, 372),
(8, 373),
(8, 1709);

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
(7, 613, 9, 0, '1');

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
  ADD KEY `fk_asig_convenio` (`id_convenio`);

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
  ADD PRIMARY KEY (`id_convenio`),
  ADD UNIQUE KEY `cif` (`cif`);

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
  ADD KEY `fk_conv_nuevo_ciclo` (`id_ciclo`);

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
  ADD PRIMARY KEY (`id_tutor`,`id_convenio`),
  ADD KEY `fk_fav_convenio` (`id_convenio`);

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
  ADD KEY `fk_tutor_usuario` (`id_usuario`),
  ADD KEY `fk_tutor_ciclo` (`id_ciclo`);

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
  MODIFY `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  MODIFY `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  MODIFY `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `convenios`
--
ALTER TABLE `convenios`
  MODIFY `id_convenio` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  MODIFY `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  MODIFY `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  MODIFY `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1714;

--
-- AUTO_INCREMENT de la tabla `resultados_aprendizaje`
--
ALTER TABLE `resultados_aprendizaje`
  MODIFY `id_ra` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tutores`
--
ALTER TABLE `tutores`
  MODIFY `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_asig_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`);

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
-- Filtros para la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  ADD CONSTRAINT `fk_aprobado_nuevo` FOREIGN KEY (`id_convenio_nuevo`) REFERENCES `convenios_nuevos` (`id_convenio_nuevo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  ADD CONSTRAINT `fk_conv_nuevo_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_fav_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`) ON DELETE CASCADE,
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
