-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3307
-- Tiempo de generación: 25-04-2025 a las 08:53:38
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
-- Base de datos: `ej_licencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesorio`
--

CREATE TABLE `accesorio` (
  `Id_accesorio` int(11) NOT NULL,
  `codigo_barras` varchar(13) NOT NULL,
  `caracteristicas` varchar(500) NOT NULL,
  `peso` decimal(10,0) NOT NULL,
  `precio` bigint(20) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_tipo_accesorio` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accesorio`
--

INSERT INTO `accesorio` (`Id_accesorio`, `codigo_barras`, `caracteristicas`, `peso`, `precio`, `id_material`, `id_tipo_accesorio`, `id_empresa`) VALUES
(2, '', 'Pulsera de 18 kilates con esmeraldas de 3mm', 2, 864000, 1, 1, 213456789),
(5, 'JYA5788864763', 'pulsera oro blanco', 2, 1234556, 1, 1, 213456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `nombre_empresa`) VALUES
(213456789, 'Aleja Marin'),
(321456789, 'Onix'),
(432156789, 'Montealegre'),
(987654321, 'Kevins');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia`
--

CREATE TABLE `licencia` (
  `id_licencia` varchar(10) NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_tipo_licencia` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `licencia`
--

INSERT INTO `licencia` (`id_licencia`, `fecha_ini`, `fecha_fin`, `id_tipo_licencia`, `id_empresa`) VALUES
('15a0d48dd2', '2025-04-24', '2027-04-24', 6, 213456789),
('43c52d2e60', '0000-00-00', '0000-00-00', 4, 987654321),
('ad70085a28', '2025-04-25', '2025-04-28', 4, 987654321),
('e8e4badb9e', '2025-04-20', '2025-04-23', 4, 321456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `nombre_material` varchar(50) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `material`
--

INSERT INTO `material` (`id_material`, `nombre_material`, `id_empresa`) VALUES
(1, 'Oro', 213456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `Id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`Id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `superadmin`
--

CREATE TABLE `superadmin` (
  `documento` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `superadmin`
--

INSERT INTO `superadmin` (`documento`, `nombre`, `email`, `password`) VALUES
(1106227432, 'Gabriela Devia', 'gabrieladeviamarin@gmail.com', '$2y$10$3sbafkqLuoNc9L6gN31gIeG0govx7fcUBaWFKvNU..npNRYZpzPZa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_accesorio`
--

CREATE TABLE `tipo_accesorio` (
  `id_tipo_accesorio` int(11) NOT NULL,
  `nombre_tipo` varchar(50) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_accesorio`
--

INSERT INTO `tipo_accesorio` (`id_tipo_accesorio`, `nombre_tipo`, `id_empresa`) VALUES
(1, 'Pulsera', 213456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_licencia`
--

CREATE TABLE `tipo_licencia` (
  `id_tipo_licencia` int(11) NOT NULL,
  `nombre_tipo_licencia` varchar(50) NOT NULL,
  `tipo_duracion` varchar(10) NOT NULL,
  `duracion` int(10) NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `precio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_licencia`
--

INSERT INTO `tipo_licencia` (`id_tipo_licencia`, `nombre_tipo_licencia`, `tipo_duracion`, `duracion`, `descripcion`, `precio`) VALUES
(4, 'Licencia Demo', 'dias', 3, 'Duracion de 3 dias donde el usuario tendra todos los permisos', 0),
(5, 'Licencia 1 año', 'años', 1, 'Duracion de 1 año donde el usuario tendra todos los permisos', 70000),
(6, 'Licencia 2 años', 'años', 2, 'Duracion de 2 años donde el usuario tendra todos los permisos', 120000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `documento` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `Id_rol` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`documento`, `nombre`, `email`, `password`, `Id_rol`, `id_empresa`) VALUES
(12345678, 'Aleja Marin', 'aleja@gmail.com', '$2y$10$4sHIqobEgWtXvR4bVmOG/O2mwjwl9xUvcnDHQiYpzH2qzg/RSsUzC', 1, 213456789),
(123456789, 'Ricardo montealegre', 'monte@gmail.com', '$2y$10$Xhl0Ustv0Ox/Nm5zbuSS6.Yx44wO0/ediwY6wtUkHCSIH6XuAOB9W', 1, 432156789),
(321654987, 'Jhon Freddy', 'fred@gmail.com', '$2y$10$f3.7px4odCrSJUbwYxZXSeJuHMtRhiFriGe5524Iuyp0fucfMea2G', 1, 321456789),
(1106227432, 'gaby', 'gaby@gmail.com', '$2y$10$5XPkPi/2PCq1Lw2uAQreROD3xIwB4cDFNIsMoXicQxz12P0Bvqnra', 2, 213456789);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesorio`
--
ALTER TABLE `accesorio`
  ADD PRIMARY KEY (`Id_accesorio`),
  ADD KEY `id_tipo_accesorio` (`id_tipo_accesorio`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD PRIMARY KEY (`id_licencia`),
  ADD KEY `id_tipo_licencia` (`id_tipo_licencia`),
  ADD KEY `id_empresa` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`Id_rol`);

--
-- Indices de la tabla `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`documento`);

--
-- Indices de la tabla `tipo_accesorio`
--
ALTER TABLE `tipo_accesorio`
  ADD PRIMARY KEY (`id_tipo_accesorio`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `tipo_licencia`
--
ALTER TABLE `tipo_licencia`
  ADD PRIMARY KEY (`id_tipo_licencia`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`documento`),
  ADD KEY `Id_rol` (`Id_rol`),
  ADD KEY `id_empresa` (`id_empresa`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesorio`
--
ALTER TABLE `accesorio`
  MODIFY `Id_accesorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `Id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_accesorio`
--
ALTER TABLE `tipo_accesorio`
  MODIFY `id_tipo_accesorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_licencia`
--
ALTER TABLE `tipo_licencia`
  MODIFY `id_tipo_licencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesorio`
--
ALTER TABLE `accesorio`
  ADD CONSTRAINT `accesorio_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_tipo_accesorio` FOREIGN KEY (`id_tipo_accesorio`) REFERENCES `tipo_accesorio` (`id_tipo_accesorio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD CONSTRAINT `fk_licencia_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_tipo_licencia` FOREIGN KEY (`id_tipo_licencia`) REFERENCES `tipo_licencia` (`id_tipo_licencia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tipo_accesorio`
--
ALTER TABLE `tipo_accesorio`
  ADD CONSTRAINT `tipo_accesorio_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `Id_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Id_rol` FOREIGN KEY (`Id_rol`) REFERENCES `rol` (`Id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
