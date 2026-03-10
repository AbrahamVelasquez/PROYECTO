SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `gestion_fct`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `rol`) VALUES
(1, 'admin', 'admin123', 'admin'),
(2, 'carlos', 'carlos123', 'tutor'),
(3, 'laura', 'laura123', 'tutor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

DROP TABLE IF EXISTS `ciclos`;
CREATE TABLE `ciclos` (
  `id_ciclo` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_ciclo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ciclo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`id_ciclo`, `nombre_ciclo`) VALUES
(1, 'Desarrollo de Aplicaciones Web'),
(2, 'Administración de Sistemas Informáticos en Red'),
(3, 'Desarrollo de Aplicaciones Multiplataforma');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE `cursos` (
  `id_curso` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_ciclo` int(6) UNSIGNED DEFAULT NULL,
  `nombre_curso` varchar(50) NOT NULL,
  PRIMARY KEY (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `id_ciclo`, `nombre_curso`) VALUES
(1, 1, '2º DAW'),
(2, 2, '2º ASIR'),
(3, 1, '1º DAW');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

DROP TABLE IF EXISTS `tutores`;
CREATE TABLE `tutores` (
  `id_tutor` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` int(6) UNSIGNED DEFAULT NULL,
  `id_curso` int(6) UNSIGNED DEFAULT NULL,
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_tutor`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tutores`
--

INSERT INTO `tutores` (`id_tutor`, `id_usuario`, `id_curso`, `dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
(1, 2, 1, '12345678A', 'Carlos', 'García Pérez', 'carlos.garcia@educa.madrid.org', '600111222'),
(2, 3, 2, '87654321B', 'Laura', 'Martínez Soler', 'laura.martinez@educa.madrid.org', '600333444'),
(3, NULL, NULL, '11223344C', 'Javier', 'Rodríguez', 'javier.rodriguez@educa.madrid.org', '600555666');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE `alumnos` (
  `id_alumno` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_curso` int(6) UNSIGNED DEFAULT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(30) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_alumno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `id_curso`, `nombre`, `apellidos`, `telefono`, `correo`) VALUES
(1, 1, 'Marta', 'Blanco Meléndez', '634315599', 'marta.blanco@educa.madrid.org'),
(2, 1, 'Margarita', 'Morón Sánchez', '687177286', 'margarita.moron@educa.madrid.org'),
(3, 2, 'Miguel Ángel', 'Caro Cruz', '647143792', 'miguel.caro@educa.madrid.org');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `id_empresa` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `nombre_empresa`, `email`, `telefono`, `direccion`, `responsable`) VALUES
(1, 'Tech Solutions S.L.', 'rrhh@techsolutions.es', '912344556', 'Calle Falsa 123, Madrid', 'Marta Sánchez'),
(2, 'Sistemas Globales', 'info@sisglobal.com', '933445566', 'Av. Diagonal 45, Barcelona', 'Jorge Ramos'),
(3, 'Web Design Studio', 'contacto@webdesign.es', '955667788', 'Plaza Mayor 5, Sevilla', 'Lucía Fernández');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

DROP TABLE IF EXISTS `asignaciones`;
CREATE TABLE `asignaciones` (
  `id_asignacion` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` int(6) UNSIGNED NOT NULL,
  `id_empresa` int(6) UNSIGNED NOT NULL,
  `id_tutor` int(6) UNSIGNED NOT NULL,
  `annio_academico` varchar(9) NOT NULL,
  PRIMARY KEY (`id_asignacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id_asignacion`, `id_alumno`, `id_empresa`, `id_tutor`, `annio_academico`) VALUES
(1, 1, 1, 1, '2025-2026'),
(2, 2, 3, 1, '2025-2026'),
(3, 3, 2, 2, '2025-2026');

-- --------------------------------------------------------

--
-- Restricciones para tablas volcadas (Relaciones)
--

ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_curso_ciclo` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclos` (`id_ciclo`) ON DELETE CASCADE;

ALTER TABLE `tutores`
  ADD CONSTRAINT `fk_tutor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tutor_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE SET NULL;

ALTER TABLE `alumnos`
  ADD CONSTRAINT `fk_alumno_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE SET NULL;

ALTER TABLE `asignaciones`
  ADD CONSTRAINT `fk_asig_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_asig_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`),
  ADD CONSTRAINT `fk_asig_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`);

COMMIT;