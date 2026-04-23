SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- TABLA: alumnos
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

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `apellido1`, `apellido2`, `dni`, `sexo`, `correo`, `telefono`) VALUES
(1, 'Juan', 'Pérez', 'Sánchez', '12345678Z', 'H', 'juan@alumno.es', '611000001'),
(2, 'Marta', 'García', 'Mesa', '23456789X', 'M', 'marta@alumno.es', '611000002'),
(3, 'Luis', 'Rodríguez', 'Oca', '34567890C', 'H', 'luis@alumno.es', '611000003');

-- TABLA: asignaciones
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

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`, `enviado`, `nombre_tutor_empresa`, `correo_tutor_empresa`, `tel_tutor_empresa`) VALUES
(1, 1, 7, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1, '', '', ''),
(2, 2, 4, '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0, NULL, NULL, NULL),
(3, 3, 3, '2026-03-01', '2026-06-15', '08:30-14:30', 370, 'L-V', 6.00, 0, NULL, NULL, NULL);

-- TABLA: asignaciones_firmadas
CREATE TABLE `asignaciones_firmadas` (
  `id_firmada` int(6) UNSIGNED NOT NULL,
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `exportado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `asignaciones_firmadas` (`id_firmada`, `id_asignacion`, `exportado`) VALUES
(22, 1, 1),
(24, 17, 1),
(25, 29, 1);

-- TABLA: ciclos
CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL,
  `grado` enum('Básica','Media','Superior') NOT NULL,
  `linea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`, `grado`, `linea`) VALUES
(1, 2, 'DAW', 'Superior', NULL),
(2, 2, 'SMR', 'Media', NULL),
(3, 1, 'DAM', 'Superior', NULL);

-- TABLA: convenios
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

INSERT INTO `convenios` (`id_convenio`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `fax`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(1, 'Tech Cloud', 'B11111111', 'Calle Nube 1', 'Madrid', '28001', 'España', '910000001', '910000002', 'info@techcloud.es', 'Antonio Recio', '11122233K', 'CEO'),
(3, 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', 'España', '910000021', '910000022', 'hola@webdesign.es', 'Amador Rivas', '33344455M', 'Gerente'),
(4, 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', 'España', '910000031', '910000032', 'admin@cyberguard.es', 'Enrique Pastor', '44455566N', 'Concejal IT');

-- TABLA: convenios_aprobados
CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `agregado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`, `fecha_aprobacion`, `agregado`) VALUES
(8, 4, '2026-04-16 13:12:07', 1),
(9, 13, '2026-04-16 15:46:39', 1),
(11, 18, '2026-04-16 16:26:43', 1);

-- TABLA: convenios_nuevos
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

INSERT INTO `convenios_nuevos` (`id_convenio_nuevo`, `id_ciclo`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `fax`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(4, 1, 'ZAPIM', 'FIC', 'DIRECCION', 'MUNICIPIO', 'CP', 'ESPAÑA', 'TELEFONO', 'FAX', 'EMAIL@email.com', 'nombre', 'dni', 'cargo'),
(10, 1, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', 'España', '910000071', '910000072', 'admin@logicgate.es', 'Vicente Figueroa', '88899900R', 'Jefe de Sistemas'),
(11, 1, 'Byte Masters', 'A99999999', 'Paseo del Código 9', 'Getafe', '28902', 'España', '910000081', '910000082', 'hola@bytemasters.es', 'Lola Trujillo', '99900011S', 'Directora de Arte');

-- TABLA: cursos
CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `cursos` (`id_curso`, `nombre_curso`) VALUES
(1, 'Primero'),
(2, 'Segundo');

-- TABLA: curso_academico
CREATE TABLE `curso_academico` (
  `id_curso_academico` int(6) UNSIGNED NOT NULL,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `anio_inicio` int(4) NOT NULL,
  `anio_fin` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `curso_academico` (`id_curso_academico`, `id_alumno`, `id_ciclo`, `anio_inicio`, `anio_fin`) VALUES
(1, 1, 1, 2025, 2026),
(2, 8, 1, 2025, 2026),
(3, 9, 1, 2025, 2026);

-- TABLA: mi_listado
CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `mi_listado` (`id_tutor`, `id_convenio`) VALUES
(1, 1),
(1, 4),
(1, 5);

-- TABLA: modulos
CREATE TABLE `modulos` (
  `id_modulo` int(6) UNSIGNED NOT NULL,
  `nombre_modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `modulos` (`id_modulo`, `nombre_modulo`) VALUES
(156, 'Inglés profesional (GM)'),
(179, 'Inglés profesional (GS)'),
(221, 'Montaje y mantenimiento de equipos');

-- TABLA: plan_estudios
CREATE TABLE `plan_estudios` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_modulo` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `plan_estudios` (`id_ciclo`, `id_modulo`) VALUES
(1, 179),
(1, 612),
(1, 613);

-- TABLA: tutores
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

INSERT INTO `tutores` (`id_tutor`, `id_usuario`, `id_ciclo`, `dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
(1, 2, 1, '11111111A', 'Carlos', 'Gómez', 'carlos@centrofct.es', '600111111'),
(2, 3, 2, '22222222B', 'Laura', 'Sanz', 'laura@centrofct.es', '600222222'),
(3, 4, 3, '33333333C', 'Pablo', 'López', 'pablo@centrofct.es', '600333333');

-- TABLA: usuarios
CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin789', 'admin'),
(2, 'carlos_tutor', 'car123', 'tutor'),
(3, 'laura_tutor', 'lau123', 'tutor');

COMMIT;