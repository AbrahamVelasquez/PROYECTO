-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-04-2026 a las 01:21:30
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
(8, 'Alejandro', 'García', 'Pérez', '12345678A', 'H', 'alejandro.garcia@correo.es', '600111333'),
(9, 'Lucía', 'Martín', 'Sánchez', '23456789B', 'M', 'lucia.martin@correo.es', '600333444'),
(10, 'Marc', 'Vila', 'Gómez', '34561890C', 'H', 'marc.vila@correo.es', '600555666'),
(11, 'Elena', 'Sanz', 'Castro', '45678901D', 'M', 'elena.sanz@correo.es', '600777888'),
(12, 'Javier', 'López', 'Ruiz', '56789012E', 'H', 'javier.lopez@correo.es', '600999000'),
(13, 'Marta', 'Ibáñez', 'Torres', '67890123F', 'M', 'marta.ibanez@correo.es', '2'),
(14, 'David', 'Jiménez', 'Méndez', '78901234G', 'H', 'david.jimenez@correo.es', '611444555'),
(57, 'MARCOS Fabian', 'García', 'Gonzalez', '36719012E', 'H', 'marcosf@gmail.com', '671478574'),
(58, 'JOSUE', 'García', 'Sánchez', '2345678WD', 'H', 'josue@gmail.com', '685412154'),
(59, 'Abraham', 'Velasquez', 'Granados', '21565678A', 'H', 'abraham@gmail.com', '658873964');

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
  `enviado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`, `enviado`) VALUES
(1, 1, 7, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0),
(2, 2, 2, '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0),
(3, 3, 3, '2026-03-01', '2026-06-15', '08:30-14:30', 370, 'L-V', 6.00, 0),
(4, 4, 4, '2026-05-10', '2026-06-10', '08:00-15:00', 100, 'L-V', 7.00, 0),
(5, 5, 5, '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 0),
(6, 6, 6, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0),
(7, 7, 7, '2026-03-01', '2026-06-15', '10:00-14:00', 370, 'L-V', 4.00, 0),
(13, 8, 2, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 1),
(14, 9, 2, '2026-03-01', '2026-06-15', '', 0, 'L-V', 0.00, 0),
(15, 10, 4, '0000-00-00', '0000-00-00', '', 370, 'L-V', 6.00, 0),
(17, 12, 1, '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00, 1),
(25, 57, 1, NULL, NULL, '', NULL, NULL, NULL, 0),
(27, 59, 2, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_firmadas`
--

CREATE TABLE `asignaciones_firmadas` (
  `id_firmada` int(6) UNSIGNED NOT NULL,
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `exportado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `asignaciones_firmadas`
--

INSERT INTO `asignaciones_firmadas` (`id_firmada`, `id_asignacion`, `exportado`) VALUES
(15, 17, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`) VALUES
(1, 2, 'DAW'),
(2, 2, 'SMR'),
(3, 1, 'DAM'),
(4, 2, 'ASIR'),
(5, 1, 'DAW'),
(6, 1, 'SMR'),
(7, 2, 'DAM');

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
(2, 'Sistemas S.A.', 'A22222222', 'Av. Binaria 2', 'Getafe', '28901', 'España', '910000011', '910000012', 'rrhh@sistemas.es', 'Berta Escobar', '22233344L', 'Directora'),
(3, 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', 'España', '910000021', '910000022', 'hola@webdesign.es', 'Amador Rivas', '33344455M', 'Gerente'),
(4, 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', 'España', '910000031', '910000032', 'admin@cyberguard.es', 'Enrique Pastor', '44455566N', 'Concejal IT'),
(5, 'Data Flow', 'A55555555', 'Paseo Nodo 5', 'Alcorcón', '28921', 'España', '910000041', '910000042', 'jobs@dataflow.es', 'Judith Becker', '55566677O', 'RRHH'),
(6, 'Net Solutions', 'B66666666', 'Calle Router 6', 'Madrid', '28010', 'España', '910000051', '910000052', 'pyme@netsolutions.es', 'Coque Calatrava', '66677788P', 'Técnico Jefe'),
(7, 'Global IT', 'B77777777', 'Av. Mundo 7', 'Móstoles', '28931', 'España', '910000061', '910000062', 'ventas@globalit.es', 'Fermín Trujillo', '77788899Q', 'Comercial');

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
(1, 2, '2026-04-13 22:15:09', 0),
(2, 3, '2026-04-13 22:33:18', 0),
(3, 4, '2026-04-13 22:36:09', 0),
(4, 5, '2026-04-13 23:21:01', 0);

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
(2, 1, 'SDFGDFG', 'DFGDSFG', 'FGSDFGDSFG', 'DFGSDG', 'dfgdfgdg', 'ESPAÑA', 'dfgdfg', 'sdfgsdf', 'gsdfgsdg@kdkdkd.com', 'ASDA', 'DASDASD', 'ADASD'),
(3, 1, 'QWEQWE', 'QWEQWE', 'QWEQWE', 'QWEQWE', 'ggdfh', 'ESPAÑA', 'gfhfgh', 'fghf', 'hfgh@gmail.com', 'FGH', 'FGH', 'FGH'),
(4, 1, 'SDFGDFG', 'ASD', 'SAD', 'VBN', 'nvbn', 'ESPAÑA', 'vbn', 'vbn', 'vbnvbn@hotmail.com', 'VBN', 'VBN', 'VBN'),
(5, 1, 'DASD', 'KKK', 'RTH', 'UJY', 'kyu', 'ESPAÑA', 'yu', 'yu', 'yuyu@gial.com', 'DASD', 'SDASD', 'DDQWD');

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
(32, 59, 1, 2025, 2026);

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
(1, 2),
(1, 4),
(1, 7),
(2, 2),
(2, 6),
(3, 3),
(4, 5),
(5, 7);

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
(7, 1, 7, '77777777G', 'Roberto', 'Vidal', 'roberto@centrofct.es', '600777777');

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
(7, 'ana_tutor', 'ana123', 'tutor');

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
  MODIFY `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `asignaciones_firmadas`
--
ALTER TABLE `asignaciones_firmadas`
  MODIFY `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  MODIFY `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `convenios`
--
ALTER TABLE `convenios`
  MODIFY `id_convenio` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `convenios_aprobados`
--
ALTER TABLE `convenios_aprobados`
  MODIFY `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `convenios_nuevos`
--
ALTER TABLE `convenios_nuevos`
  MODIFY `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso_academico`
--
ALTER TABLE `curso_academico`
  MODIFY `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tutores`
--
ALTER TABLE `tutores`
  MODIFY `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Filtros para la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD CONSTRAINT `fk_tutor_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
