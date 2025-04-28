-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-04-2025 a las 15:13:15
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
(1, 816767995, '{\"GutoTradeBot\":{\"admin_level\":1,\"time_zone\":\"-4\",\"config_delete_prev_messages\":true,\"metadatas\":{\"wallet\":\"0xFAcD960564531bd336ed94fBBd0911408288FCF2\"}},\"ZentroTraderBot\":{\"exchanges\":{\"bingx\":{\"api_key\":\"F3pw0O44pv5QhGGuKzY9AyHZq60NYf3THq5QlfIaq98sS1KBIROzeLB2aUI7U7bUqUSnjMDfw3licUQDdhcbQ\",\"secret_key\":\"DzLfyysYVDVQYVtEDe4z8AixcI0eDfNbBuYt4gJBm68jdR3D7PoCCxLG4PRnIokZEOyvl11PtuQBg06ORQ\",\"base_order_size\":10},\"apexpromainnet\":{\"account_id\":\"582636658558500973\",\"api_key\":\"7db9ecef-ae49-919c-1846-d940915038f3\",\"api_key_passphrase\":\"LE28zjRYnLxCg6gSwNt6\",\"api_key_secret\":\"vaDMMnqqpaAw2zuM8PFLz7APToQ0u67HmIIaoaCg\",\"stark_key_private\":\"0x075ad444fa7b1192d97a7d70d41e0e84aecb5ab24e94cfd66ff132c6fa080371\",\"stark_key_public\":\"0x02ca7badf2d37abdc8e3b750f4f73a817e0a6baf309fb668d00b96e840cb58ed\",\"stark_key_public_key_y_coordinate\":\"0x04b45a88a2c5f7692ce9f1bb025fb182300925d3111e903d68d72fea660fb668\",\"base_order_size\":1},\"apexprotestnet\":{\"account_id\":\"582635608690655601\",\"api_key\":\"2e705f3e-1b37-3224-9977-9cfa5b17b1c2\",\"api_key_passphrase\":\"MBQBXf9WXwKDB991TGGh\",\"api_key_secret\":\"2AhyVMp8ZZ2SI6NeJlQq7tw65-hj8PxfkR2MNZ_P\",\"stark_key_private\":\"0x07ead10517457b1a57f17bda2a7253962457e39ce22d4b469468f1f6dde1434b\",\"stark_key_public\":\"0x0678a0b412a405b276ff2ce43c5bab96f49a86a7be1e9bde0941e5b3a37070f1\",\"stark_key_public_key_y_coordinate\":\"0x05cf53cfd299bb9452bf0f750de4935e672500fa11474acfb042fce4d1f1f2d3\",\"base_order_size\":5},\"active\":[\"apexprotestnet\",\"apexpromainnet\",\"bingx\"]},\"admin_level\":2,\"suscription_level\":2,\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":816767995,\"first_name\":\"Donel\",\"last_name\":\"Vazquez Zambrano\",\"username\":\"dvzambrano\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"dvzambrano\"],\"bio\":\"Programador, Emprendedor y Entusiasta de las criptomonedas\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoYEdQ8w5SEV3rAk5CLaX3zd_AAKtpzEb--OuMAMFCQeYxcDPAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 Donel Vazquez Zambrano\",\"formated_username\":\"dvzambrano\",\"full_info\":\"\\ud83d\\udc64 Donel Vazquez Zambrano \\n\\u2709\\ufe0f @dvzambrano \\n\\ud83c\\udd94 `816767995`\"},\"ZentroOwnerBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"}}'),
(3, 6277250767, '{\"GutoTradeBot\":{\"admin_level\":4},\"telegram\":{\"id\":6277250767,\"first_name\":\"Dayami\",\"username\":\"dproenzap\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"dproenzap\"],\"photo\":\"AgACAgQAAxUAAWfXo3N_xI1ooXjmlGvSKro98dyrAAJwvTEbSKIZUh_lK1IaSxoLAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 Dayami\",\"formated_username\":\"dproenzap\",\"full_info\":\"\\ud83d\\udc64 Dayami \\n\\u2709\\ufe0f @dproenzap \\n\\ud83c\\udd94 `6277250767`\"}}'),
(4, 347888105, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\",\"metadatas\":{\"wallet\":\"0x435017CAda98F95CcE0402Bc2aE9FcAcFcCe1E46\"}},\"telegram\":{\"id\":347888105,\"first_name\":\"Arqu\\u00ed\\u028d\\u1d07d\\u1d07s\",\"username\":\"N3WBi3\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"N3WBi3\"],\"has_private_forwards\":true,\"has_restricted_voice_and_video_messages\":true,\"business_intro\":{\"sticker\":{\"width\":512,\"height\":512,\"emoji\":\"\\ud83d\\ude0e\",\"set_name\":\"Developer\",\"is_animated\":true,\"is_video\":false,\"type\":\"regular\",\"thumbnail\":{\"file_id\":\"AAMCAgADFQABZ9ejdJOqT_bpWh6HVbLB39Nr2P0AAvoQAAKhxyhIOWV265NYB6MBAAdtAAM2BA\",\"file_unique_id\":\"AQAD-hAAAqHHKEhy\",\"file_size\":6088,\"width\":128,\"height\":128},\"thumb\":{\"file_id\":\"AAMCAgADFQABZ9ejdJOqT_bpWh6HVbLB39Nr2P0AAvoQAAKhxyhIOWV265NYB6MBAAdtAAM2BA\",\"file_unique_id\":\"AQAD-hAAAqHHKEhy\",\"file_size\":6088,\"width\":128,\"height\":128},\"file_id\":\"CAACAgIAAxUAAWfXo3STqk_26Voeh1Wywd_Ta9j9AAL6EAACoccoSDllduuTWAejNgQ\",\"file_unique_id\":\"AgAD-hAAAqHHKEg\",\"file_size\":26353}},\"photo\":\"AgACAgEAAxUAAWfXo3T7bTvnUbRtxAuLJsUrQTiiAAKzpzEb6Vm8FODOyTFllNHtAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5413550549960245358\",\"max_reaction_count\":11,\"accent_color_id\":12,\"background_custom_emoji_id\":\"5420141555233071341\",\"profile_accent_color_id\":12,\"profile_background_custom_emoji_id\":\"5465383954750122781\",\"full_name\":\"\\ud83d\\udc64 Arqu\\u00ed\\u028d\\u1d07d\\u1d07s\",\"formated_username\":\"N3WBi3\",\"full_info\":\"\\ud83d\\udc64 Arqu\\u00ed\\u028d\\u1d07d\\u1d07s \\n\\u2709\\ufe0f @N3WBi3 \\n\\ud83c\\udd94 `347888105`\"}}'),
(5, 1419502564, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\",\"metadatas\":{\"wallet\":\"0x5ca74b45E9Bf7f99016cd6AE099C0c67A5ADd953\"}},\"telegram\":{\"id\":1419502564,\"first_name\":\"Dr Limonta\",\"username\":\"LimontikaEKO\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"LimontikaEKO\"],\"bio\":\"Si alguien pudo hacerlo yo tambi\\u00e9n puedo y si alguien no pudo yo ser\\u00e9 el primero\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoXC-BQoF9TBpYFX37N4HFfkWAAJnrTEbkRDwRlT31AN5cTAbAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5391112412445288650\",\"max_reaction_count\":11,\"accent_color_id\":4,\"profile_accent_color_id\":15,\"full_name\":\"\\ud83d\\udc64 Dr Limonta\",\"formated_username\":\"LimontikaEKO\",\"full_info\":\"\\ud83d\\udc64 Dr Limonta \\n\\u2709\\ufe0f @LimontikaEKO \\n\\ud83c\\udd94 `1419502564`\"}}'),
(6, 1269084609, '{\"GutoTradeBot\":{\"admin_level\":2},\"telegram\":{\"id\":1269084609,\"first_name\":\"Tangiro\",\"last_name\":\"Kamado\",\"username\":\"AZOR79\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"AZOR79\"],\"bio\":\"Andan un clon m\\u00edo pidan videollamada siempre conmigo....\",\"photo\":\"AgACAgEAAxUAAWfYoXBvhH2XEnBDv9jxXkmT63VOAALbrDEbs-AhRHLM4Rdn3DpFAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5368562433981947135\",\"max_reaction_count\":11,\"accent_color_id\":2,\"full_name\":\"\\ud83d\\udc64 Tangiro Kamado\",\"formated_username\":\"AZOR79\",\"full_info\":\"\\ud83d\\udc64 Tangiro Kamado \\n\\u2709\\ufe0f @AZOR79 \\n\\ud83c\\udd94 `1269084609`\"}}'),
(7, 873754229, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0xF409929eB4799aD184bD2F2402aE313f648D6D78\"}},\"telegram\":{\"id\":873754229,\"first_name\":\"German David\",\"username\":\"GermanDavid\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"GermanDavid\"],\"bio\":\"#Emprendedor #Comerciante #Trader #NegociosDigitales \\ud83c\\udd94 873754229\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfX4YUzCAr2TqCt2vtDlmwMVnp9AALApzEbdW4UNDxp-54EZhglAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"4913457176627905267\",\"max_reaction_count\":11,\"accent_color_id\":9,\"profile_accent_color_id\":10,\"full_name\":\"\\ud83d\\udc64 German David\",\"formated_username\":\"GermanDavid\",\"full_info\":\"\\ud83d\\udc64 German David \\n\\u2709\\ufe0f @GermanDavid \\n\\ud83c\\udd94 `873754229`\"}}'),
(8, 613173575, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"getsenderpaymentscreenshot\",\"metadatas\":{\"wallet\":\"0x451a9E32dF65691fCE93EE830CCf5bB5Cfa66FA0\"}},\"telegram\":{\"id\":613173575,\"first_name\":\"Yander.ron\",\"username\":\"yander961122\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"yander961122\"],\"bio\":\"\\ud83d\\udc8e\\ud83d\\udc49 Verifica siempre que sea yo \\ud83d\\udc48\\ud83d\\udc8e\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoXHfB3JJY4N-IsfLqGsju6UJAAK8pzEbR0mMJLZCvoDT6w43AQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Yander.ron\",\"formated_username\":\"yander961122\",\"full_info\":\"\\ud83d\\udc64 Yander.ron \\n\\u2709\\ufe0f @yander961122 \\n\\ud83c\\udd94 `613173575`\"}}'),
(9, 1358852792, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x01a9B0a5240dD2eef39075C8505284D8dE565A0e\"}},\"telegram\":{\"id\":1358852792,\"first_name\":\"Anibal\",\"username\":\"Amesa47\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Amesa47\"],\"bio\":\"id 1358852792\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoXEpSaHlgBPxu2WEcsFczFReAAJaqTEbMTJIRxmfhS8T8T9iAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 Anibal\",\"formated_username\":\"Amesa47\",\"full_info\":\"\\ud83d\\udc64 Anibal \\n\\u2709\\ufe0f @Amesa47 \\n\\ud83c\\udd94 `1358852792`\"}}'),
(11, 6211414111, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x2FAB1226E8171bF05821580929aD9b5b066fBB47\"}},\"telegram\":{\"id\":6211414111,\"first_name\":\"LOCOL\",\"type\":\"private\",\"can_send_gift\":true,\"photo\":\"AgACAgEAAxUAAWfYoXKB4m3YnV84chcJW2y-kBYOAAJ8rjEbcJmpR1lVCsu_Mu7iAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 LOCOL\",\"full_info\":\"\\ud83d\\udc64 LOCOL \\n\\ud83c\\udd94 `6211414111`\"}}'),
(12, 5919527201, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"},\"telegram\":{\"id\":5919527201,\"first_name\":\"Lixandro\",\"last_name\":\"Lopez\",\"username\":\"lixandrousa\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"lixandrousa\"],\"photo\":\"AgACAgEAAxUAAWfYoXJ80ug5XylZKLv9PgrA7xdxAAJkrDEbVZGgRKIT2yJEAAGdngEAAwIAA2MAAzYE\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Lixandro Lopez\",\"formated_username\":\"lixandrousa\",\"full_info\":\"\\ud83d\\udc64 Lixandro Lopez \\n\\u2709\\ufe0f @lixandrousa \\n\\ud83c\\udd94 `5919527201`\"}}'),
(13, 895670352, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x16D02D6b07d9998a0BCc1adAd339F5eE1963a162\"}},\"telegram\":{\"id\":895670352,\"first_name\":\"GerardGames\",\"username\":\"GerardGames\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"GerardGames\"],\"bio\":\"Siempre Verifiqueme \\ud83d\\ude09\",\"photo\":\"AgACAgEAAxUAAWfYoXMjeLfwUKFCqHY0wIsRDr9xAAKvpzEbUNhiNVLZb6BJBbj-AQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 GerardGames\",\"formated_username\":\"GerardGames\",\"full_info\":\"\\ud83d\\udc64 GerardGames \\n\\u2709\\ufe0f @GerardGames \\n\\ud83c\\udd94 `895670352`\"}}'),
(15, 5328142807, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0xe6ae5c2890A82f77293CB1e7aeB7983abac1D100\"}},\"telegram\":{\"id\":5328142807,\"first_name\":\"\\ud835\\udcd4\\ud835\\udcdb \\ud835\\udcd0\\ud835\\udcdb\\ud835\\udcd3\\ud835\\udcd4\\ud835\\udcd0\\ud835\\udcdd\\ud835\\udcde \\ud83c\\udfa7\",\"username\":\"EL_Lobo_DPEPDE\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"EL_Lobo_DPEPDE\"],\"bio\":\"El le\\u00f3n es el rey de la selva pero el lobo no trabaja para el circo \\ud83d\\ude0e\\ud83d\\udcaa\",\"photo\":\"AgACAgEAAxUAAWfYoXP6F5_Utf2iEeLVmDrvuqfpAAI_qjEbsFe4R-BjSjis6eUYAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 \\ud835\\udcd4\\ud835\\udcdb \\ud835\\udcd0\\ud835\\udcdb\\ud835\\udcd3\\ud835\\udcd4\\ud835\\udcd0\\ud835\\udcdd\\ud835\\udcde \\ud83c\\udfa7\",\"formated_username\":\"EL\\\\_Lobo\\\\_DPEPDE\",\"full_info\":\"\\ud83d\\udc64 \\ud835\\udcd4\\ud835\\udcdb \\ud835\\udcd0\\ud835\\udcdb\\ud835\\udcd3\\ud835\\udcd4\\ud835\\udcd0\\ud835\\udcdd\\ud835\\udcde \\ud83c\\udfa7 \\n\\u2709\\ufe0f @EL\\\\_Lobo\\\\_DPEPDE \\n\\ud83c\\udd94 `5328142807`\"}}'),
(16, 5219069448, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x747be3684809dAbD572B7860504007569ccCA7a1\"}},\"telegram\":{\"id\":5219069448,\"first_name\":\"Karim\",\"last_name\":\"Benzema\",\"username\":\"KarimB99\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"KarimB99\"],\"bio\":\"No me dejes referenciaas....No apadrino \\ud83c\\udd97 No pregunte x gusto ah\\u00ed lo dice claro.\",\"has_restricted_voice_and_video_messages\":true,\"photo\":\"AgACAgEAAxUAAWfZohNTTUYAAWGMymgK4vHMlGTjLwACU60xGw6J4Uao-eGoQ7ekewEAAwIAA2MAAzYE\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5463091464416271240\",\"max_reaction_count\":11,\"accent_color_id\":5,\"full_name\":\"\\ud83d\\udc64 Karim Benzema\",\"formated_username\":\"KarimB99\",\"full_info\":\"\\ud83d\\udc64 Karim Benzema \\n\\u2709\\ufe0f @KarimB99 \\n\\ud83c\\udd94 `5219069448`\"}}'),
(30, 6549567189, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0xE490a7e36c3Db10d36b7530ac405E6d09B8a2326\"}},\"telegram\":{\"id\":6549567189,\"first_name\":\"Alejandro\",\"username\":\"ALEJ1961\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"ALEJ1961\"],\"bio\":\"VERIFICAME EN RIC E IDC SIEMPRE\",\"photo\":\"AgACAgEAAxUAAWfYoXRHVyss44m8kDJdygMPcTvEAAKerjEba_RpRE8xGcPKKm4oAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5361939671720926182\",\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 Alejandro\",\"formated_username\":\"ALEJ1961\",\"full_info\":\"\\ud83d\\udc64 Alejandro \\n\\u2709\\ufe0f @ALEJ1961 \\n\\ud83c\\udd94 `6549567189`\"}}'),
(31, 1562139660, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x9b8465c22635b4F45a1Df2f6EF2cc44D1B444244\"}},\"telegram\":{\"id\":1562139660,\"first_name\":\"$\\u00a3du$\",\"username\":\"EdutroLL\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"EdutroLL\"],\"bio\":\"Verifiqueme siempre en RIC. ID:1562139660\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoXRS2BUWkIEsdWTtchn9HDmAAAJCrDEbdUjoR5Akdr578gjfAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 $\\u00a3du$\",\"formated_username\":\"EdutroLL\",\"full_info\":\"\\ud83d\\udc64 $\\u00a3du$ \\n\\u2709\\ufe0f @EdutroLL \\n\\ud83c\\udd94 `1562139660`\"}}'),
(32, 1705333263, '{\"GutoTradeBot\":{\"admin_level\":2,\"config_delete_prev_messages\":true,\"metadatas\":{\"wallet\":\"0x893549727d17172d99a6810d9b32916324f63232\"}},\"last_bot_callback_data\":\"getsenderpaymentscreenshot\",\"telegram\":{\"id\":1705333263,\"first_name\":\"Chichi\",\"username\":\"chichifuentes\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"chichifuentes\"],\"bio\":\"Verificame siempre \\ud83d\\udc51\\ud83d\\udc51. Confiable en RIC 1705333263\",\"photo\":\"AgACAgEAAxUAAWfYFK42yygsc0X1iZgSgsbJ9vPVAAKrqjEbZFwYRYR1sZZTTQLOAQADAgADYwADNgQ\",\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 Chichi\",\"formated_username\":\"chichifuentes\",\"full_info\":\"\\ud83d\\udc64 Chichi \\n\\u2709\\ufe0f @chichifuentes \\n\\ud83c\\udd94 `1705333263`\",\"pinned_message\":false}}'),
(37, 1314081227, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\",\"metadatas\":{\"wallet\":\"0xa872e206b7a909cc61006949d58b828e545390b9\"}},\"telegram\":{\"id\":1314081227,\"first_name\":\"JAlvaro98\",\"username\":\"Jalvaro98\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Jalvaro98\"],\"photo\":\"AgACAgEAAxUAAWfYoXUnKHOgvVEU212sil8gSOo7AALeqTEbnQMYRsJq_OifW70bAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 JAlvaro98\",\"formated_username\":\"Jalvaro98\",\"full_info\":\"\\ud83d\\udc64 JAlvaro98 \\n\\u2709\\ufe0f @Jalvaro98 \\n\\ud83c\\udd94 `1314081227`\"}}'),
(38, 894525123, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"},\"telegram\":{\"id\":894525123,\"first_name\":\"Ernesto\",\"last_name\":\"Marey\",\"username\":\"yeramf\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"yeramf\"],\"has_private_forwards\":true,\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":2,\"full_name\":\"\\ud83d\\udc64 Ernesto Marey\",\"formated_username\":\"yeramf\",\"full_info\":\"\\ud83d\\udc64 Ernesto Marey \\n\\u2709\\ufe0f @yeramf \\n\\ud83c\\udd94 `894525123`\",\"photo\":false}}'),
(39, 800673679, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\",\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":800673679,\"first_name\":\"\\uf8ffAle\",\"username\":\"alexcraw\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"alexcraw\"],\"bio\":\"Downloading, please wait.....\\u26a0\\ufe0f\",\"photo\":\"AgACAgEAAxUAAWfYoXadxTbiQ45zm6os6zBUfkVyAAKspzEbj0-5LwYZ3avNhSCkAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 \\uf8ffAle\",\"formated_username\":\"alexcraw\",\"full_info\":\"\\ud83d\\udc64 \\uf8ffAle \\n\\u2709\\ufe0f @alexcraw \\n\\ud83c\\udd94 `800673679`\"}}'),
(40, 1391442211, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"},\"telegram\":{\"id\":1391442211,\"first_name\":\"David\",\"last_name\":\"Cuesta\",\"username\":\"Aassassin_98\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Aassassin_98\"],\"bio\":\"El mejor maestro es el tiempo\\ud83d\\udd70\",\"photo\":\"AgACAgEAAxUAAWfYoXaWsCx7MrzEK8iDQZLN1hM9AAI2rTEbAa7ZRfZ5568I5gvpAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":5,\"full_name\":\"\\ud83d\\udc64 David Cuesta\",\"formated_username\":\"Aassassin\\\\_98\",\"full_info\":\"\\ud83d\\udc64 David Cuesta \\n\\u2709\\ufe0f @Aassassin\\\\_98 \\n\\ud83c\\udd94 `1391442211`\"}}'),
(41, 1086812420, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"},\"telegram\":{\"id\":1086812420,\"first_name\":\"Kendry_Axel\",\"username\":\"KendryAxel\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"KendryAxel\"],\"photo\":\"AgACAgEAAxUAAWfYoXd9Dtn5AmZdyzBVpy-EqbWNAAJMqjEbMmf4RNrqjgO0eNhKAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 Kendry Axel\",\"formated_username\":\"KendryAxel\",\"full_info\":\"\\ud83d\\udc64 Kendry Axel \\n\\u2709\\ufe0f @KendryAxel \\n\\ud83c\\udd94 `1086812420`\"}}'),
(43, 1246560016, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"1358852792\"},\"telegram\":{\"id\":1246560016,\"first_name\":\"El loco de la carretera\",\"username\":\"LittLebiker\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"LittLebiker\"],\"bio\":\"Amante a las Motos\\ud83d\\ude01\\ud83d\\ude0f\\ud83d\\ude09\\ud83d\\ude0e\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfZoiZmO9w1kLT0vrW0ENiBO58yAAK_rjEbMYHBR-mqx5D-M6A5AQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":2,\"full_name\":\"\\ud83d\\udc64 El loco de la carretera\",\"formated_username\":\"LittLebiker\",\"full_info\":\"\\ud83d\\udc64 El loco de la carretera \\n\\u2709\\ufe0f @LittLebiker \\n\\ud83c\\udd94 `1246560016`\"}}'),
(51, 1256079990, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0xAdb07988dfC4Ca4B8ACAA973EFfd475633b92A22\"}},\"telegram\":{\"id\":1256079990,\"first_name\":\"\\ud83d\\udc51\\ua9c1\\ud835\\udd84\\ud835\\udd9a\\ud835\\udd93\\ud835\\udd8e\\ud835\\udd94\\ud835\\udd97\\ua9c2\\ud83d\\udc51\",\"username\":\"TheSon_ofGod\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"TheSon_ofGod\"],\"bio\":\"Use el bot y mi ID\\u2705 antes de negociar para su seguridad\\ud83e\\uddd0 ID:1256079990\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYoXjvGOCOdqGByZo-PlD90-ARAAIerDEbLLsIRlCExIYKC5XOAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 \\ud83d\\udc51\\ua9c1\\ud835\\udd84\\ud835\\udd9a\\ud835\\udd93\\ud835\\udd8e\\ud835\\udd94\\ud835\\udd97\\ua9c2\\ud83d\\udc51\",\"formated_username\":\"TheSon\\\\_ofGod\",\"full_info\":\"\\ud83d\\udc64 \\ud83d\\udc51\\ua9c1\\ud835\\udd84\\ud835\\udd9a\\ud835\\udd93\\ud835\\udd8e\\ud835\\udd94\\ud835\\udd97\\ua9c2\\ud83d\\udc51 \\n\\u2709\\ufe0f @TheSon\\\\_ofGod \\n\\ud83c\\udd94 `1256079990`\"}}'),
(52, 7252174930, '{\"GutoTradeBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\",\"metadatas\":{\"wallet\":\"0xB5FAA907Fe00E53e7906df6d5d5BF5CD23661b02\"}},\"telegram\":{\"id\":7252174930,\"first_name\":\"Pipon\",\"username\":\"GutoTradeBot\",\"type\":\"private\",\"active_usernames\":[\"GutoTradeBot\"],\"photo\":\"AgACAgEAAxUAAWfZl1xzAiOr-q4SWHzPkA4TPArfAAKirTEb8N25RUDs24QFXImwAQADAgADYwADNgQ\",\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Pipon\",\"formated_username\":\"GutoTradeBot\",\"full_info\":\"\\ud83d\\udc64 Pipon \\n\\u2709\\ufe0f @GutoTradeBot \\n\\ud83c\\udd94 `7252174930`\",\"pinned_message\":false}}'),
(54, 5508220560, '{\"GutoTradeBot\":{\"admin_level\":2,\"time_zone\":\"-4\"},\"telegram\":{\"id\":5508220560,\"first_name\":\"Deivys \\ud83d\\ude07\",\"username\":\"Deivys2000\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Deivys2000\"],\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 Deivys \\ud83d\\ude07\",\"formated_username\":\"Deivys2000\",\"full_info\":\"\\ud83d\\udc64 Deivys \\ud83d\\ude07 \\n\\u2709\\ufe0f @Deivys2000 \\n\\ud83c\\udd94 `5508220560`\",\"photo\":false}}'),
(55, 765842467, '{\"ZentroCriptoBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":765842467,\"first_name\":\"Yovanys\",\"username\":\"YovanysCuba\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"YovanysCuba\"],\"has_private_forwards\":true,\"max_reaction_count\":11,\"accent_color_id\":5,\"full_name\":\"\\ud83d\\udc64 Yovanys\",\"formated_username\":\"YovanysCuba\",\"full_info\":\"\\ud83d\\udc64 Yovanys \\n\\u2709\\ufe0f @YovanysCuba \\n\\ud83c\\udd94 `765842467`\",\"pinned_message\":false,\"photo\":false}}'),
(59, 7758300896, '{\"GutoTradeBot\":{\"admin_level\":4},\"telegram\":{\"id\":7758300896,\"first_name\":\"Roger\",\"username\":\"Rogerjose87\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Rogerjose87\"],\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 Roger\",\"formated_username\":\"Rogerjose87\",\"full_info\":\"\\ud83d\\udc64 Roger \\n\\u2709\\ufe0f @Rogerjose87 \\n\\ud83c\\udd94 `7758300896`\",\"photo\":false}}'),
(62, 1741391257, '{\"GutoTradeBot\":{\"admin_level\":2},\"ZentroTraderBot\":{\"exchanges\":{\"bingx\":{\"api_key\":\"\",\"secret_key\":\"\",\"base_order_size\":10},\"apexpromainnet\":{\"account_id\":\"\",\"api_key\":\"\",\"api_key_passphrase\":\"\",\"api_key_secret\":\"\",\"stark_key_private\":\"\",\"stark_key_public\":\"\",\"stark_key_public_key_y_coordinate\":\"\",\"base_order_size\":1},\"apexprotestnet\":{\"account_id\":\"\",\"api_key\":\"\",\"api_key_passphrase\":\"\",\"api_key_secret\":\"\",\"stark_key_private\":\"\",\"stark_key_public\":\"\",\"stark_key_public_key_y_coordinate\":\"\",\"base_order_size\":1},\"active\":[]},\"admin_level\":0,\"suscription_level\":0,\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":1741391257,\"first_name\":\"Crypto\",\"last_name\":\"Dev\",\"username\":\"criptodev1981\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"criptodev1981\"],\"photo\":\"AgACAgEAAxUAAWfYoXl1YWiVkaavTg5pHq5Ku7z-AAKgrDEbmmPZR6Pt4aMevEGIAQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":4,\"full_name\":\"\\ud83d\\udc64 Crypto Dev\",\"formated_username\":\"criptodev1981\",\"full_info\":\"\\ud83d\\udc64 Crypto Dev \\n\\u2709\\ufe0f @criptodev1981 \\n\\ud83c\\udd94 `1741391257`\"},\"ZentroOwnerBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"}}'),
(63, 5482646491, '{\"GutoTradeBot\":{\"admin_level\":4},\"telegram\":{\"id\":5482646491,\"first_name\":\"Roger\",\"username\":\"rogerjoser87\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"rogerjoser87\"],\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Roger\",\"formated_username\":\"rogerjoser87\",\"full_info\":\"\\ud83d\\udc64 Roger \\n\\u2709\\ufe0f @rogerjoser87 \\n\\ud83c\\udd94 `5482646491`\",\"photo\":false}}'),
(66, 902278699, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x02303a5CfC2AdDcc45Aec7Ac5554A178aa90C964\"}},\"telegram\":{\"id\":902278699,\"first_name\":\"Gabriel\",\"username\":\"GabrieL_92\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"GabrieL_92\"],\"bio\":\"Adorador del Dios Alt\\u00edsimo y padre de familia. Canal de negocios: https:\\/\\/t.me\\/fastshopcuba\",\"has_private_forwards\":true,\"has_restricted_voice_and_video_messages\":true,\"business_intro\":{\"title\":\"Dios te bendiga!!!!\",\"message\":\"Escribe siempre lo que quieres y no solo saludes para poderte ayudar!!\",\"sticker\":{\"width\":512,\"height\":512,\"emoji\":\"\\u2728\",\"set_name\":\"AlabanzayAdoracion\",\"is_animated\":false,\"is_video\":false,\"type\":\"regular\",\"thumbnail\":{\"file_id\":\"AAMCAQADFQABZ9iheXiAQaINgt8pPUAJg687V8cAAj4BAAIVc1BHzR9lD6NOGIoBAAdtAAM2BA\",\"file_unique_id\":\"AQADPgEAAhVzUEdy\",\"file_size\":10006,\"width\":320,\"height\":320},\"thumb\":{\"file_id\":\"AAMCAQADFQABZ9iheXiAQaINgt8pPUAJg687V8cAAj4BAAIVc1BHzR9lD6NOGIoBAAdtAAM2BA\",\"file_unique_id\":\"AQADPgEAAhVzUEdy\",\"file_size\":10006,\"width\":320,\"height\":320},\"file_id\":\"CAACAgEAAxUAAWfYoXl4gEGiDYLfKT1ACYOvO1fHAAI-AQACFXNQR80fZQ-jThiKNgQ\",\"file_unique_id\":\"AgADPgEAAhVzUEc\",\"file_size\":16288}},\"business_opening_hours\":{\"opening_hours\":[{\"opening_minute\":480,\"closing_minute\":1380},{\"opening_minute\":1920,\"closing_minute\":2820},{\"opening_minute\":3360,\"closing_minute\":4260},{\"opening_minute\":4800,\"closing_minute\":5700},{\"opening_minute\":6240,\"closing_minute\":7140},{\"opening_minute\":7680,\"closing_minute\":8580}],\"time_zone_name\":\"America\\/Havana\"},\"personal_chat\":{\"id\":-1001522529838,\"title\":\"Canal FastShop Post\",\"username\":\"fastshopcuba\",\"type\":\"channel\"},\"photo\":\"AgACAgEAAxUAAWfYoXnIRWH4nw0McEiyjSf8PFoxAAK8pzEbK67HNeXI-sI6608hAQADAgADYwADNgQ\",\"pinned_message\":false,\"emoji_status_custom_emoji_id\":\"5933550168996581523\",\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Gabriel\",\"formated_username\":\"GabrieL\\\\_92\",\"full_info\":\"\\ud83d\\udc64 Gabriel \\n\\u2709\\ufe0f @GabrieL\\\\_92 \\n\\ud83c\\udd94 `902278699`\"}}'),
(67, 1242891579, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\",\"metadatas\":{\"wallet\":\"0x799519C94fBF15f358E62B17C393DE26C401298c\"}},\"telegram\":{\"id\":1242891579,\"first_name\":\"Mr Alucard\",\"username\":\"LionSVG\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"LionSVG\"],\"bio\":\"\\ud83e\\udd11\\ud83e\\udd11 Get rich or die trying \\ud83e\\udd11\\ud83e\\udd11\",\"has_private_forwards\":true,\"photo\":\"AgACAgEAAxUAAWfYURvPe0HMar-Ha7dMuR8-TbESAAKbrDEb1mY4RYkpIeDcKzB3AQADAgADYwADNgQ\",\"pinned_message\":false,\"max_reaction_count\":11,\"accent_color_id\":6,\"full_name\":\"\\ud83d\\udc64 Mr Alucard\",\"formated_username\":\"LionSVG\",\"full_info\":\"\\ud83d\\udc64 Mr Alucard \\n\\u2709\\ufe0f @LionSVG \\n\\ud83c\\udd94 `1242891579`\"}}'),
(78, 1668276100, '{\"GutoTradeBot\":{\"admin_level\":2},\"telegram\":{\"id\":1668276100,\"first_name\":\"\\u2763\\ufe0fNena\\u2763\\ufe0f\",\"username\":\"Nena9808\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Nena9808\"],\"bio\":\"Who has magic \\ud83d\\udd2d\\ud83c\\udf0c doesn\'t need tricks \\ud83d\\ude0f\\ud83e\\ude90\",\"has_private_forwards\":true,\"birthdate\":{\"day\":3,\"month\":8,\"year\":1998},\"photo\":\"AgACAgEAAxUAAWfYoXrY8kRELH6F9itNwCKUDGbSAAJJqzEblPwIROIDSqoBdkz6AQADAgADYwADNgQ\",\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 \\u2763\\ufe0fNena\\u2763\\ufe0f\",\"formated_username\":\"Nena9808\",\"full_info\":\"\\ud83d\\udc64 \\u2763\\ufe0fNena\\u2763\\ufe0f \\n\\u2709\\ufe0f @Nena9808 \\n\\ud83c\\udd94 `1668276100`\",\"pinned_message\":false}}'),
(79, 1006074613, '{\"GutoTradeBot\":{\"admin_level\":2},\"telegram\":{\"id\":1006074613,\"first_name\":\"Miguel\",\"last_name\":\"Soria Mart\\u00ednez\",\"username\":\"msmmystore\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"msmmystore\"],\"bio\":\"\\u201cGuiando almas hacia su despertar: En armon\\u00eda con el divino dise\\u00f1o del Universo\\u201d \\u267e\\ufe0f msmmystore.com\",\"has_private_forwards\":true,\"has_restricted_voice_and_video_messages\":true,\"business_intro\":{\"sticker\":{\"width\":512,\"height\":512,\"emoji\":\"\\ud83c\\udfc3\\u200d\\u2642\\ufe0f\",\"set_name\":\"diggy_anim\",\"is_animated\":true,\"is_video\":false,\"type\":\"regular\",\"thumbnail\":{\"file_id\":\"AAMCAgADFQABZ9ihejDxOJcwOYeLDMtlXW0niUYAAj1dAAKezgsAAdpz8nGZVMrMAQAHbQADNgQ\",\"file_unique_id\":\"AQADPV0AAp7OCwABcg\",\"file_size\":4686,\"width\":128,\"height\":128},\"thumb\":{\"file_id\":\"AAMCAgADFQABZ9ihejDxOJcwOYeLDMtlXW0niUYAAj1dAAKezgsAAdpz8nGZVMrMAQAHbQADNgQ\",\"file_unique_id\":\"AQADPV0AAp7OCwABcg\",\"file_size\":4686,\"width\":128,\"height\":128},\"file_id\":\"CAACAgIAAxUAAWfYoXow8TiXMDmHiwzLZV1tJ4lGAAI9XQACns4LAAHac_JxmVTKzDYE\",\"file_unique_id\":\"AgADPV0AAp7OCwAB\",\"file_size\":12843}},\"business_opening_hours\":{\"opening_hours\":[{\"opening_minute\":480,\"closing_minute\":1320},{\"opening_minute\":1920,\"closing_minute\":2760},{\"opening_minute\":3360,\"closing_minute\":4200},{\"opening_minute\":4800,\"closing_minute\":5640},{\"opening_minute\":6240,\"closing_minute\":7080},{\"opening_minute\":7680,\"closing_minute\":8520},{\"opening_minute\":9120,\"closing_minute\":9960}],\"time_zone_name\":\"America\\/New_York\"},\"personal_chat\":{\"id\":-1001729081530,\"title\":\"MSM my store noticias\",\"username\":\"msmmystor\",\"type\":\"channel\"},\"photo\":\"AgACAgEAAxUAAWfYoXqXy3sEnqDT4JZ9DxRS2HAzAALnpzEb9Xr3O_iBLKP1x2XjAQADAgADYwADNgQ\",\"emoji_status_custom_emoji_id\":\"5206594230792756692\",\"max_reaction_count\":11,\"accent_color_id\":12,\"background_custom_emoji_id\":\"5301253879772494275\",\"profile_accent_color_id\":13,\"profile_background_custom_emoji_id\":\"5348503265967355284\",\"full_name\":\"\\ud83d\\udc64 Miguel Soria Mart\\u00ednez\",\"formated_username\":\"msmmystore\",\"full_info\":\"\\ud83d\\udc64 Miguel Soria Mart\\u00ednez \\n\\u2709\\ufe0f @msmmystore \\n\\ud83c\\udd94 `1006074613`\",\"pinned_message\":false}}'),
(82, 7025289286, '{\"ZentroCriptoBot\":{\"admin_level\":0},\"telegram\":{\"id\":7025289286,\"first_name\":\"Alusma\",\"last_name\":\"Alexandra\",\"type\":\"private\",\"can_send_gift\":true,\"bio\":\"Je vais avoir mes 16 ans en moi de mai\",\"max_reaction_count\":11,\"accent_color_id\":1,\"full_name\":\"\\ud83d\\udc64 Alusma Alexandra\",\"full_info\":\"\\ud83d\\udc64 Alusma Alexandra \\n\\ud83c\\udd94 `7025289286`\",\"pinned_message\":false,\"photo\":false}}'),
(86, 1290493382, '{\"GutoTradeBot\":{\"admin_level\":2,\"last_bot_callback_data\":\"\",\"metadatas\":{\"wallet\":\"0x10ec8aa860eAba4acCB3d59680B98d6c3aD217Bd\"}},\"telegram\":{\"id\":1290493382,\"first_name\":\"Luis Daniel\\ud83d\\udc51\",\"username\":\"LuisDanieLpototo\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"LuisDanieLpototo\"],\"bio\":\"Dios es bueno\\ud83d\\ude4c\",\"has_private_forwards\":true,\"has_restricted_voice_and_video_messages\":true,\"photo\":\"AgACAgEAAxUAAWf1KRklLXJ3ikKmOo3548YcZWnxAAKpqzEbytdxRq8P1EA4oUscAQADAgADYwADNgQ\",\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 Luis Daniel\\ud83d\\udc51\",\"formated_username\":\"LuisDanieLpototo\",\"full_info\":\"\\ud83d\\udc64 Luis Daniel\\ud83d\\udc51 \\n\\u2709\\ufe0f @LuisDanieLpototo \\n\\ud83c\\udd94 `1290493382`\",\"pinned_message\":false}}'),
(87, 1846894749, '{\"ZentroCriptoBot\":{\"admin_level\":0,\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":1846894749,\"first_name\":\"Leomar Mota\",\"username\":\"LeomarMota\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"LeomarMota\"],\"bio\":\"\\ud83d\\udc68\\ud83c\\udffb\\u200d\\ud83c\\udfeb\\ud83e\\uddee\",\"photo\":\"AgACAgEAAxUAAWf1sjfIx3FSHV_Cv_kyJbizbXm2AAJDqzEbZyQwR6utfwbuxhoBAQADAgADYwADNgQ\",\"max_reaction_count\":11,\"accent_color_id\":0,\"full_name\":\"\\ud83d\\udc64 Leomar Mota\",\"formated_username\":\"LeomarMota\",\"full_info\":\"\\ud83d\\udc64 Leomar Mota \\n\\u2709\\ufe0f @LeomarMota \\n\\ud83c\\udd94 `1846894749`\",\"pinned_message\":false}}'),
(89, 1120249986, '{\"GutoTradeBot\":{\"admin_level\":2,\"parent_id\":\"347888105\",\"last_bot_callback_data\":\"\"},\"telegram\":{\"id\":1120249986,\"first_name\":\"Foreverlove0505\",\"username\":\"Recilencia05\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"Recilencia05\"],\"has_private_forwards\":true,\"accepted_gift_types\":{\"unlimited_gifts\":true,\"limited_gifts\":true,\"unique_gifts\":true,\"premium_subscription\":true},\"max_reaction_count\":11,\"accent_color_id\":2,\"full_name\":\"\\ud83d\\udc64 Foreverlove0505\",\"formated_username\":\"Recilencia05\",\"full_info\":\"\\ud83d\\udc64 Foreverlove0505 \\n\\u2709\\ufe0f @Recilencia05 \\n\\ud83c\\udd94 `1120249986`\",\"pinned_message\":false,\"photo\":false}}'),
(90, 1135355056, '{\"GutoTradeBot\":{\"admin_level\":2,\"metadatas\":{\"wallet\":\"0x9f0870638d97c23708EDCAb76D255913C3eE8662\"}},\"telegram\":{\"id\":1135355056,\"first_name\":\"Roily\",\"username\":\"rf58469004\",\"type\":\"private\",\"can_send_gift\":true,\"active_usernames\":[\"rf58469004\"],\"bio\":\"Verificame en RiC \\/ IDC \\/ MY BAMBU\",\"has_private_forwards\":true,\"accepted_gift_types\":{\"unlimited_gifts\":true,\"limited_gifts\":true,\"unique_gifts\":true,\"premium_subscription\":true},\"personal_chat\":{\"id\":-1001614953676,\"title\":\"\\ud83d\\udc51ROILY VIP OFFERS\\ud83d\\udc51\",\"username\":\"roilyvipoffers\",\"type\":\"channel\"},\"emoji_status_custom_emoji_id\":\"5112098258721702554\",\"max_reaction_count\":11,\"accent_color_id\":3,\"full_name\":\"\\ud83d\\udc64 Roily\",\"formated_username\":\"rf58469004\",\"full_info\":\"\\ud83d\\udc64 Roily \\n\\u2709\\ufe0f @rf58469004 \\n\\ud83c\\udd94 `1135355056`\",\"pinned_message\":false,\"photo\":false}}');

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
(6, '@ZentroLicensorBot', '1450849635:AAHpvMRi6EMdCajw6yZ9G6uma0WV1FF2JCY', '[]'),
(7, '@ZentroOwnerBot', '7948651884:AAGI3FjcxYyaRkmuqrLsAZP34vQxz5B2LwA', '[]');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `bots`
--
ALTER TABLE `bots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
