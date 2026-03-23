SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ==========================================================
-- 1. ESTRUCTURA DE LA BASE DE DATOS (DDL)
-- ==========================================================

DROP TABLE IF EXISTS `mi_listado`;
DROP TABLE IF EXISTS `asignaciones`;
DROP TABLE IF EXISTS `alumnos`;
DROP TABLE IF EXISTS `tutores`;
DROP TABLE IF EXISTS `ciclos`;
DROP TABLE IF EXISTS `cursos`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `convenios`;

-- Tabla de Usuarios
CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Cursos
CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(50) NOT NULL,
  PRIMARY KEY (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Ciclos
CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_curso` int(6) UNSIGNED NOT NULL,
  `nombre_ciclo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ciclo`),
  CONSTRAINT `fk_ciclo_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Tutores
CREATE TABLE `tutores` (
  `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` int(6) UNSIGNED NOT NULL,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  PRIMARY KEY (`id_tutor`),
  UNIQUE KEY `dni` (`dni`),
  CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutor_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Alumnos
CREATE TABLE `alumnos` (
  `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_ciclo` int(6) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `sexo` enum('M', 'H', 'Otro') NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  PRIMARY KEY (`id_alumno`),
  UNIQUE KEY `dni` (`dni`),
  CONSTRAINT `fk_alumno_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Convenios
CREATE TABLE `convenios` (
  `id_convenio` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `cargo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_convenio`),
  UNIQUE KEY `cif` (`cif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de Asignaciones
CREATE TABLE `asignaciones` (
  `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_final` DATE NOT NULL,
  `horario` varchar(100) NOT NULL,
  `num_total_horas` int(4) NOT NULL,
  `dias_semana` varchar(50) NOT NULL,
  `horas_dia` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id_asignacion`),
  CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_asig_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla mi_listado (Favoritos)
CREATE TABLE `mi_listado` (
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `id_convenio` int(6) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_tutor`, `id_convenio`),
  CONSTRAINT `fk_fav_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenios` (`id_convenio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================================
-- 2. INSERCIÓN DE DATOS (DML) - 7 REGISTROS POR TABLA
-- ==========================================================

-- Usuarios (7 registros)
INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin789', 'admin'),
(2, 'carlos_tutor', 'car123', 'tutor'),
(3, 'laura_tutor', 'lau123', 'tutor'),
(4, 'pablo_tutor', 'pab123', 'tutor'),
(5, 'elena_tutor', 'ele123', 'tutor'),
(6, 'mario_tutor', 'mar123', 'tutor'),
(7, 'ana_tutor', 'ana123', 'tutor');

-- Cursos (2 registros base usados por los 7 ciclos)
INSERT INTO `cursos` (`id_curso`, `nombre_curso`) VALUES 
(1, 'Primero'), (2, 'Segundo');

-- Ciclos (7 registros)
INSERT INTO `ciclos` (`id_ciclo`, `id_curso`, `nombre_ciclo`) VALUES
(1, 2, 'DAW'), (2, 2, 'SMR'), (3, 1, 'DAM'), (4, 2, 'ASIR'), 
(5, 1, 'DAW'), (6, 1, 'SMR'), (7, 2, 'DAM');

-- Tutores (7 registros, vinculados a usuario y ciclo)
INSERT INTO `tutores` (`id_tutor`, `id_usuario`, `id_ciclo`, `dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
(1, 2, 1, '11111111A', 'Carlos', 'Gómez', 'carlos@centrofct.es', '600111111'),
(2, 3, 2, '22222222B', 'Laura', 'Sanz', 'laura@centrofct.es', '600222222'),
(3, 4, 3, '33333333C', 'Pablo', 'López', 'pablo@centrofct.es', '600333333'),
(4, 5, 4, '44444444D', 'Elena', 'Martín', 'elena@centrofct.es', '600444444'),
(5, 6, 5, '55555555E', 'Mario', 'García', 'mario@centrofct.es', '600555555'),
(6, 7, 6, '66666666F', 'Ana', 'Ruiz', 'ana@centrofct.es', '600666666'),
(7, 1, 7, '77777777G', 'Roberto', 'Vidal', 'roberto@centrofct.es', '600777777');

-- Alumnos (7 registros)
INSERT INTO `alumnos` (`id_alumno`, `id_ciclo`, `nombre`, `apellido1`, `apellido2`, `dni`, `sexo`, `correo`, `telefono`) VALUES
(1, 1, 'Juan', 'Pérez', 'Sánchez', '12345678Z', 'H', 'juan@alumno.es', '611000001'),
(2, 2, 'Marta', 'García', 'Mesa', '23456789X', 'M', 'marta@alumno.es', '611000002'),
(3, 3, 'Luis', 'Rodríguez', 'Oca', '34567890C', 'H', 'luis@alumno.es', '611000003'),
(4, 4, 'Sofía', 'Alba', 'Rico', '45678901V', 'M', 'sofia@alumno.es', '611000004'),
(5, 5, 'Diego', 'Torres', 'Luna', '56789012B', 'H', 'diego@alumno.es', '611000005'),
(6, 6, 'Lucía', 'Blanco', 'Polo', '67890123N', 'M', 'lucia@alumno.es', '611000006'),
(7, 7, 'Andrés', 'Marín', 'Soler', '78901234M', 'H', 'andres@alumno.es', '611000007');

-- Convenios (7 registros)
INSERT INTO `convenios` (`id_convenio`, `nombre_empresa`, `cif`, `direccion`, `municipio`, `cp`, `pais`, `telefono`, `fax`, `mail`, `nombre_representante`, `dni_representante`, `cargo`) VALUES
(1, 'Tech Cloud', 'B11111111', 'Calle Nube 1', 'Madrid', '28001', 'España', '910000001', '910000002', 'info@techcloud.es', 'Antonio Recio', '11122233K', 'CEO'),
(2, 'Sistemas S.A.', 'A22222222', 'Av. Binaria 2', 'Getafe', '28901', 'España', '910000011', '910000012', 'rrhh@sistemas.es', 'Berta Escobar', '22233344L', 'Directora'),
(3, 'Web Design', 'B33333333', 'Plaza Pixel 3', 'Leganés', '28911', 'España', '910000021', '910000022', 'hola@webdesign.es', 'Amador Rivas', '33344455M', 'Gerente'),
(4, 'Cyber Guard', 'B44444444', 'Calle Firewall 4', 'Madrid', '28004', 'España', '910000031', '910000032', 'admin@cyberguard.es', 'Enrique Pastor', '44455566N', 'Concejal IT'),
(5, 'Data Flow', 'A55555555', 'Paseo Nodo 5', 'Alcorcón', '28921', 'España', '910000041', '910000042', 'jobs@dataflow.es', 'Judith Becker', '55566677O', 'RRHH'),
(6, 'Net Solutions', 'B66666666', 'Calle Router 6', 'Madrid', '28010', 'España', '910000051', '910000052', 'pyme@netsolutions.es', 'Coque Calatrava', '66677788P', 'Técnico Jefe'),
(7, 'Global IT', 'B77777777', 'Av. Mundo 7', 'Móstoles', '28931', 'España', '910000061', '910000062', 'ventas@globalit.es', 'Fermín Trujillo', '77788899Q', 'Comercial');

-- Asignaciones (7 registros)
INSERT INTO `asignaciones` (`id_alumno`, `id_convenio`, `fecha_inicio`, `fecha_final`, `horario`, `num_total_horas`, `dias_semana`, `horas_dia`) VALUES
(1, 1, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00),
(2, 2, '2026-03-01', '2026-06-15', '09:00-14:00', 370, 'L-V', 5.00),
(3, 3, '2026-03-01', '2026-06-15', '08:30-14:30', 370, 'L-V', 6.00),
(4, 4, '2026-05-10', '2026-06-10', '08:00-15:00', 100, 'L-V', 7.00),
(5, 5, '2026-03-01', '2026-06-15', '09:00-17:00', 400, 'L-J', 8.00),
(6, 6, '2026-03-01', '2026-06-15', '08:00-15:00', 370, 'L-V', 7.00),
(7, 7, '2026-03-01', '2026-06-15', '10:00-14:00', 370, 'L-V', 4.00);

-- Favoritos (7 registros en mi_listado)
INSERT INTO `mi_listado` (`id_tutor`, `id_convenio`) VALUES
(1, 1), (1, 4), (2, 2), (2, 6), (3, 3), (4, 5), (5, 7);

COMMIT;