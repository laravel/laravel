-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-01-2025 a las 15:50:57
-- Versión del servidor: 10.11.7-MariaDB
-- Versión de PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `telegrambot`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actors`
--

CREATE TABLE `actors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `actors`
--

INSERT INTO `actors` (`id`, `user_id`, `data`) VALUES
(1, 816767995, '{\"GutoTradeBot\":{\"admin_level\":1,\"last_bot_callback_data\":\"\",\"time_zone\":\"-4\",\"config_delete_prev_messages\":true}}'),
(2, 5482646491, '{\"GutoTradeBot\":{\"admin_level\":4}}'),
(3, 6277250767, '{\"GutoTradeBot\":{\"admin_level\":4}}'),
(4, 347888105, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(5, 1419502564, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}'),
(6, 1269084609, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(7, 873754229, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(8, 613173575, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(9, 1358852792, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(11, 6211414111, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(12, 5919527201, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}'),
(13, 895670352, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(15, 5328142807, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(16, 5219069448, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(30, 6549567189, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(31, 1562139660, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(32, 1705333263, '{\"GutoTradeBot\":{\"admin_level\":2,\"config_delete_prev_messages\":true,\"last_bot_callback_data\":\"\"}}'),
(37, 1314081227, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(38, 894525123, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(39, 800673679, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\",\"last_bot_callback_data\":\"\"}}'),
(40, 1391442211, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(41, 1086812420, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(43, 1246560016, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(47, 1741391257, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"816767995\",\"time_zone\":\"-4\"}}'),
(51, 1256079990, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(52, 7252174930, '{\"GutoTradeBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"}}'),
(54, 5508220560, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actors`
--
ALTER TABLE `actors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actors`
--
ALTER TABLE `actors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
