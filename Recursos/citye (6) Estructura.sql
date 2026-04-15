CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre_curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `cursos` (`id_curso`, `nombre_curso`) VALUES
(1, 'Primero'),
(2, 'Segundo');

CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL,
  CONSTRAINT `fk_ciclo_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`) VALUES
(1, 2, 'DAW'),
(2, 2, 'SMR'),
(3, 1, 'DAM');

CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin789', 'admin'),
(2, 'carlos_tutor', 'car123', 'tutor'),
(3, 'laura_tutor', 'lau123', 'tutor');

CREATE TABLE `tutores` (
  `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `dni` varchar(9) NOT NULL UNIQUE,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  CONSTRAINT `fk_tutor_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `tutores` (`id_tutor`, `id_usuario`, `id_ciclo`, `dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
(1, 2, 1, '11111111A', 'Carlos', 'Gómez', 'carlos@centrofct.es', '600111111'),
(2, 3, 2, '22222222B', 'Laura', 'Sanz', 'laura@centrofct.es', '600222222'),
(3, 4, 3, '33333333C', 'Pablo', 'López', 'pablo@centrofct.es', '600333333');

CREATE TABLE `alumnos` (
  `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) NOT NULL,
  `dni` varchar(9) NOT NULL UNIQUE,
  `sexo` enum('M','H','Otro') NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `apellido1`, `apellido2`, `dni`, `sexo`, `correo`, `telefono`) VALUES
(1, 'Juan', 'Pérez', 'Sánchez', '12345678Z', 'H', 'juan@alumno.es', '611000001'),
(2, 'Marta', 'García', 'Mesa', '23456789X', 'M', 'marta@alumno.es', '611000002'),
(3, 'Luis', 'Rodríguez', 'Oca', '34567890C', 'H', 'luis@alumno.es', '611000003');

CREATE TABLE `curso_academico` (
  `id_curso_academico` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `anio_inicio` int(4) NOT NULL,
  `anio_fin` int(4) NOT NULL,
  CONSTRAINT `fk_hist_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `fk_hist_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `curso_academico` (`id_curso_academico`, `id_alumno`, `id_ciclo`, `anio_inicio`, `anio_fin`) VALUES
(1, 1, 1, 2025, 2026),
(2, 2, 2, 2025, 2026),
(3, 3, 3, 2025, 2026);

CREATE TABLE `convenios` (
  `id_convenio` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL UNIQUE,
  `direccion` varchar(255) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `nombre_representante` varchar(150) NOT NULL,
  `dni_representante` varchar(9) NOT NULL,
  `cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `convenios` (`id_convenio`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(1, 'Tech Cloud', 'B11111111', 'Calle Nube 1', 'Madrid', '28001', 'España', '910000001', 'info@techcloud.es', 'Antonio Recio', '11122233K', 'CEO'),
(2, 'Sistemas S.A.', 'A22222222', 'Av. Binaria 2', 'Getafe', '28901', 'España', '910000011', 'rrhh@sistemas.es', 'Berta Escobar', '22233344L', 'Directora'),
(3, 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', 'España', '910000021', 'hola@webdesign.es', 'Amador Rivas', '33344455M', 'Gerente');

CREATE TABLE `asignaciones` (
  `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `num_total_horas` int(4) DEFAULT NULL,
  `enviado` tinyint(1) NOT NULL DEFAULT 0,
  CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_asig_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `num_total_horas`, `enviado`) VALUES
(1, 1, 1, '2026-03-01', '2026-06-15', 370, 0),
(2, 2, 2, '2026-03-01', '2026-06-15', 370, 0),
(3, 3, 3, '2026-03-01', '2026-06-15', 370, 0);

CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_tutor`,`id_convenio`),
  CONSTRAINT `fk_fav_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `mi_listado` (`id_tutor`, `id_convenio`) VALUES
(1, 1),
(1, 2),
(2, 2);

CREATE TABLE `convenios_nuevos` (
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `cif` varchar(15) NOT NULL,
  CONSTRAINT `fk_conv_nuevo_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `convenios_nuevos` (`id_convenio_nuevo`, `id_ciclo`, `nombre_empresa`, `cif`) VALUES
(2, 1, 'SDFGDFG', 'DFGDSFG'),
(3, 1, 'QWEQWE', 'QWEQWE'),
(4, 1, 'SDFGDFG', 'ASD');

CREATE TABLE `convenios_aprobados` (
  `id_convenio_aprobado` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_convenio_nuevo` int(6) UNSIGNED NOT NULL,
  `fecha_aprobacion` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_aprobado_nuevo` FOREIGN KEY (`id_convenio_nuevo`) REFERENCES `convenios_nuevos` (`id_convenio_nuevo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `convenios_aprobados` (`id_convenio_aprobado`, `id_convenio_nuevo`) VALUES
(1, 2),
(2, 3),
(3, 4);

CREATE TABLE `asignaciones_firmadas` (
  `id_firmada` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_asignacion` int(6) UNSIGNED NOT NULL,
  `exportado` tinyint(1) NOT NULL DEFAULT 0,
  CONSTRAINT `fk_firmada_asig` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `asignaciones_firmadas` (`id_firmada`, `id_asignacion`, `exportado`) VALUES
(1, 1, 1);