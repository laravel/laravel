-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-01-2025 a las 23:12:47
-- Versión del servidor: 10.11.10-MariaDB
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u650901517_telegrambot`
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
(1, 816767995, '{\"GutoTradeBot\":{\"admin_level\":1,\"time_zone\":\"-4\"}}'),
(2, 5482646491, '{\"GutoTradeBot\":{\"admin_level\":4}}'),
(3, 6277250767, '{\"GutoTradeBot\":{\"admin_level\":4}}'),
(4, 347888105, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(5, 1419502564, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}'),
(6, 1269084609, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(7, 873754229, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(8, 613173575, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(9, 1358852792, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(11, 6211414111, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(12, 5919527201, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}'),
(13, 895670352, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(15, 5328142807, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(16, 5219069448, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(30, 6549567189, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(31, 1562139660, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(32, 1705333263, '{\"GutoTradeBot\":{\"admin_level\":2,\"config_delete_prev_messages\":true,\"last_bot_callback_data\":\"\"},\"last_bot_callback_data\":\"getsenderpaymentscreenshot\"}'),
(37, 1314081227, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\"}}'),
(38, 894525123, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(39, 800673679, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\",\"last_bot_callback_data\":\"\"}}'),
(40, 1391442211, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(41, 1086812420, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(43, 1246560016, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"}}'),
(47, 1741391257, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"816767995\",\"time_zone\":\"-4\",\"last_bot_callback_data\":\"\"},\"last_bot_callback_data\":\"getsenderpaymentscreenshot\"}'),
(51, 1256079990, '{\"GutoTradeBot\":{\"admin_level\":2}}'),
(52, 7252174930, '{\"GutoTradeBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"}}'),
(54, 5508220560, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"}}'),
(55, 765842467, '{\"ZentroCriptoBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"}}'),
(56, 1971165627, '{\"GutoTradeBot\":{\"admin_level\":0}}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bots`
--

CREATE TABLE `bots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `token` longtext NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `bots`
--

INSERT INTO `bots` (`id`, `name`, `token`, `data`) VALUES
(1, '@ZentroNotificationBot', '8198488135:AAHjSshi4P3jTy_bPDDNZF2aIzSL0DkGxBg', '[]'),
(2, '@GutoTradeBot', '7252174930:AAFJwAZaLrWiP-ONZHQZ7D0ps77HDoMkixQ', '[]'),
(3, '@ZentroTraderBot', '6989103595:AAH-qQww_v01UnAt9Ex0ZfmVp3qAIR9KXrE', '[]'),
(4, '@ZentroBaseTelegramBot', '6055381762:AAEGjtR7MHpG7GmDIMVlKzxYzBFCBkobots', '[]'),
(5, '@ZentroCriptoBot', '5797151131:AAF0o1P3C9wK8zx3OczGej9QmkILZmekJKc', '[]'),
(6, '@ZentroLicensorBot', '1450849635:AAHpvMRi6EMdCajw6yZ9G6uma0WV1FF2JCY', '[]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_03_20_00_create_telegram_nested_notifications_table', 1),
(2, '2024_08_11_01_create_actors_table', 1),
(3, '2025_01_10_00_create_telegram_bots_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nested_notifications`
--

CREATE TABLE `nested_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actors`
--
ALTER TABLE `actors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bots`
--
ALTER TABLE `bots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bots_name_unique` (`name`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nested_notifications`
--
ALTER TABLE `nested_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nested_notifications_name_unique` (`name`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actors`
--
ALTER TABLE `actors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `bots`
--
ALTER TABLE `bots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `nested_notifications`
--
ALTER TABLE `nested_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
