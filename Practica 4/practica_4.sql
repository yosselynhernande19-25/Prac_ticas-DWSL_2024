-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-09-2024 a las 02:08:09
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
-- Base de datos: `practica_4`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas_docentes`
--

CREATE TABLE `asignaturas_docentes` (
  `id` int(11) NOT NULL,
  `id_asignatura` int(11) DEFAULT NULL,
  `id_user_docentes` int(11) DEFAULT NULL,
  `ciclo` varchar(1) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `computos`
--

CREATE TABLE `computos` (
  `id` int(11) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `id_asignaturas` int(11) DEFAULT NULL,
  `id_user_estudiante` int(11) DEFAULT NULL,
  `ciclo` varchar(1) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups_permissions`
--

CREATE TABLE `groups_permissions` (
  `id` int(11) NOT NULL,
  `id_user_group` int(11) DEFAULT NULL,
  `id_permission` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created` datetime NOT NULL,
  `update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `id_asignaturas` int(11) DEFAULT NULL,
  `id_user_estudiante` int(11) DEFAULT NULL,
  `ciclo` int(1) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `id_computo` int(11) DEFAULT NULL,
  `id_user_estudiante` int(11) DEFAULT NULL,
  `laboratorio` decimal(4,2) NOT NULL,
  `parcial` decimal(4,2) NOT NULL,
  `promedio` decimal(4,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombres` varchar(255) DEFAULT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `carnet` varchar(10) DEFAULT NULL,
  `id_user_group` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asignaturas_docentes`
--
ALTER TABLE `asignaturas_docentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_asignatura` (`id_asignatura`),
  ADD KEY `fk_id_user_docente` (`id_user_docentes`);

--
-- Indices de la tabla `computos`
--
ALTER TABLE `computos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_estudiante` (`id_user_estudiante`);

--
-- Indices de la tabla `groups_permissions`
--
ALTER TABLE `groups_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_group` (`id_user_group`),
  ADD KEY `fk_id_permission` (`id_permission`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_asignaturas` (`id_asignaturas`),
  ADD KEY `fk_id_user_asignatura` (`id_user_estudiante`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_computo` (`id_computo`),
  ADD KEY `fk_id_user_estudiante` (`id_user_estudiante`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignaturas_docentes`
--
ALTER TABLE `asignaturas_docentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `computos`
--
ALTER TABLE `computos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `groups_permissions`
--
ALTER TABLE `groups_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaturas_docentes`
--
ALTER TABLE `asignaturas_docentes`
  ADD CONSTRAINT `fk_id_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `fk_id_user_docente` FOREIGN KEY (`id_user_docentes`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `computos`
--
ALTER TABLE `computos`
  ADD CONSTRAINT `fk_user_estudiante` FOREIGN KEY (`id_user_estudiante`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `groups_permissions`
--
ALTER TABLE `groups_permissions`
  ADD CONSTRAINT `fk_id_permission` FOREIGN KEY (`id_permission`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`id_user_group`) REFERENCES `users_groups` (`id`);

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_id_asignaturas` FOREIGN KEY (`id_asignaturas`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `fk_id_user_asignatura` FOREIGN KEY (`id_user_estudiante`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_id_computo` FOREIGN KEY (`id_computo`) REFERENCES `computos` (`id`),
  ADD CONSTRAINT `fk_id_user_estudiante` FOREIGN KEY (`id_user_estudiante`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
