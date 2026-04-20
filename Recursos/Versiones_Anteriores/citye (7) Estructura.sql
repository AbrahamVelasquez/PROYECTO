SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

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
(2, 'Marta', 'García', 'Mesa', '23456789X', 'M', 'marta@alumno.es', '611000002');

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

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`, `enviado`) VALUES
(1, 1, 7, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00, 0),
(2, 2, 2, '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00, 0);

CREATE TABLE `asignaciones_firmadas` (
  `id_firmada` int(6) UNSIGNED NOT NULL,
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `exportado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `asignaciones_firmadas` (`id_firmada`, `id_asignacion`, `exportado`) VALUES
(16, 13, 1),
(18, 17, 1);

CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`) VALUES
(1, 2, 'DAW'),
(2, 2, 'SMR');

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
(2, 'Sistemas S.A.', 'A22222222', 'Av. Binaria 2', 'Getafe', '28901', 'España', '910000011', '910000012', 'rrhh@sistemas.es', 'Berta Escobar', '22233344L', 'Directora');

CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `agregado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`, `fecha_aprobacion`, `agregado`) VALUES
(8, 4, '2026-04-16 13:12:07', 1),
(9, 13, '2026-04-16 15:46:39', 1);

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
(10, 1, 'Logic Gate S.L.', 'B88888888', 'Calle Transistor 8', 'Madrid', '28015', 'España', '910000071', '910000072', 'admin@logicgate.es', 'Vicente Figueroa', '88899900R', 'Jefe de Sistemas');

CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `cursos` (`id_curso`, `nombre_curso`) VALUES
(1, 'Primero'),
(2, 'Segundo');

CREATE TABLE `curso_academico` (
  `id_curso_academico` int(6) UNSIGNED NOT NULL,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `anio_inicio` int(4) NOT NULL,
  `anio_fin` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `curso_academico` (`id_curso_academico`, `id_alumno`, `id_ciclo`, `anio_inicio`, `anio_fin`) VALUES
(1, 1, 1, 2025, 2026),
(2, 8, 1, 2025, 2026);

CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `mi_listado` (`id_tutor`, `id_convenio`) VALUES
(1, 1),
(1, 2);

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
(2, 3, 2, '22222222B', 'Laura', 'Sanz', 'laura@centrofct.es', '600222222');

CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin789', 'admin'),
(2, 'carlos_tutor', 'car123', 'tutor');

ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `dni` (`dni`);

ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `fk_asig_alumno` (`id_alumno`),
  ADD KEY `fk_asig_convenio` (`id_convenio`);

ALTER TABLE `asignaciones_firmadas`
  ADD PRIMARY KEY (`id_firmada`),
  ADD KEY `fk_firmada_asig` (`id_asignacion`);

ALTER TABLE `ciclos`
  ADD PRIMARY KEY (`id_ciclo`),
  ADD KEY `fk_ciclo_curso` (`id_curso`);

ALTER TABLE `convenios`
  ADD PRIMARY KEY (`id_convenio`),
  ADD UNIQUE KEY `cif` (`cif`);

ALTER TABLE `convenios_aprobados`
  ADD PRIMARY KEY (`id_convenio_aprobado`),
  ADD KEY `fk_aprobado_nuevo` (`id_convenio_nuevo`);

ALTER TABLE `convenios_nuevos`
  ADD PRIMARY KEY (`id_convenio_nuevo`),
  ADD KEY `fk_conv_nuevo_ciclo` (`id_ciclo`);

ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`);

ALTER TABLE `curso_academico`
  ADD PRIMARY KEY (`id_curso_academico`),
  ADD KEY `fk_hist_alumno` (`id_alumno`),
  ADD KEY `fk_hist_ciclo` (`id_ciclo`);

ALTER TABLE `mi_listado`
  ADD PRIMARY KEY (`id_tutor`,`id_convenio`),
  ADD KEY `fk_fav_convenio` (`id_convenio`);

ALTER TABLE `tutores`
  ADD PRIMARY KEY (`id_tutor`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `fk_tutor_usuario` (`id_usuario`),
  ADD KEY `fk_tutor_ciclo` (`id_ciclo`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

ALTER TABLE `asignaciones`
  MODIFY `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `asignaciones_firmadas`
  MODIFY `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `ciclos`
  MODIFY `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `convenios`
  MODIFY `id_convenio` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

ALTER TABLE `convenios_aprobados`
  MODIFY `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `convenios_nuevos`
  MODIFY `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `cursos`
  MODIFY `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `curso_academico`
  MODIFY `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

ALTER TABLE `tutores`
  MODIFY `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `asignaciones`
  ADD CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_asig_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`);

ALTER TABLE `asignaciones_firmadas`
  ADD CONSTRAINT `fk_firmada_asig` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`) ON DELETE CASCADE;

ALTER TABLE `ciclos`
  ADD CONSTRAINT `fk_ciclo_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;

ALTER TABLE `convenios_aprobados`
  ADD CONSTRAINT `fk_aprobado_nuevo` FOREIGN KEY (`id_convenio_nuevo`) REFERENCES `convenios_nuevos` (`id_convenio_nuevo`) ON DELETE CASCADE;

ALTER TABLE `convenios_nuevos`
  ADD CONSTRAINT `fk_conv_nuevo_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

ALTER TABLE `curso_academico`
  ADD CONSTRAINT `fk_hist_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hist_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

ALTER TABLE `mi_listado`
  ADD CONSTRAINT `fk_fav_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE;

ALTER TABLE `tutores`
  ADD CONSTRAINT `fk_tutor_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
