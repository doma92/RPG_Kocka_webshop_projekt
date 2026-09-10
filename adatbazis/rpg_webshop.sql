-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3306
-- Létrehozás ideje: 2026. Sze 06. 21:29
-- Kiszolgáló verziója: 8.4.7
-- PHP verzió: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `rpg_webshop`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `vezeteknev` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keresztnev` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepules` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iranyitoszam` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cim` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `megjegyzes` text COLLATE utf8mb4_unicode_ci,
  `vegosszeg` int NOT NULL,
  `statusz` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Új',
  `rendeles_ideje` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `vezeteknev`, `keresztnev`, `email`, `telefon`, `telepules`, `iranyitoszam`, `cim`, `megjegyzes`, `vegosszeg`, `statusz`, `rendeles_ideje`) VALUES
(1, NULL, 'Kis ', 'Pista', 'kispista@gmail.com', '+36301234567', 'Lajosmizse', '2341', 'Etel köz 3.', '', 11660, 'Új', '2026-08-20 23:19:23'),
(2, 2, 'Kal', 'Andor', 'kalandor@gmail.com', '+36201234567', 'Ózd', '1666', 'kis utca 1', 'fuss ha kedves az életed!', 2590, 'Postázva', '2026-08-20 23:52:41'),
(3, NULL, 'Kis ', 'Pista', 'kispista@gmail.com', '+36301234567', 'Basaharc', '3245', 'kis utca 2', '', 5180, 'Új', '2026-08-21 19:11:38'),
(4, NULL, 'Teszt', 'Elek', 'tesztelek@gmail.com', '+36709996666', 'Tápiószecső', '5312', 'Eper utca 10', 'Kérem csengessen', 13680, 'Teljesítve', '2026-08-21 22:01:31'),
(5, 2, 'Kal', 'Andor', 'kalandor@gmail.com', '+234422442421', 'Esztergom', '5432', 'kis utca 2', '', 13960, 'Feldolgozás alatt', '2026-08-22 19:34:29'),
(6, NULL, 'Vendég', 'Zsolti', 'vendegzsolti@gmail.com', '+36301264897', 'Kazincbarcelona', '7453', 'Sunshine utca 7', '', 19989, 'Új', '2026-08-23 22:59:40'),
(7, NULL, 'Gipsz', 'Jakab', 'gipszjakab@gmail.com', '+1234254536', 'Dorog', '2501', 'kis utca 2', 'A futár kopogjon', 4620, 'Feldolgozás alatt', '2026-08-28 19:16:22');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `termek_nev` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egysegar` int NOT NULL,
  `darab` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `termek_nev`, `egysegar`, `darab`) VALUES
(1, 1, 'Citromsárga áttetsző akril kockaszett', 2490, 1),
(2, 1, 'Világoskék gyöngyház akril kockaszett', 3990, 1),
(3, 1, 'Sötétkék gyöngyház akril kockaszett', 2590, 2),
(4, 2, 'Sötétkék gyöngyház akril kockaszett', 2590, 1),
(5, 3, 'Zöld gyöngyház akril kockaszett', 2590, 2),
(6, 4, '10 darabos zöld, átlátszó D6 kocka', 1800, 4),
(7, 4, 'Citromsárga áttetsző akril kockaszett', 2490, 1),
(8, 4, 'Piros gyöngyház akril kockaszett', 3990, 1),
(9, 5, '10 darabos zöld, átlátszó D6 kocka', 1800, 2),
(10, 5, 'Zöld gyöngyház akril kockaszett', 2590, 4),
(11, 6, 'Áttetsző Borostyán Hatoldalas Dobókocka Készlet', 1890, 1),
(12, 6, 'Ancient Gold Bone Collector fém dobókocka készlet', 15600, 1),
(13, 6, 'Lila áttetsző akril kockaszett', 2499, 1),
(14, 7, 'Piros gyöngyház akril kockaszett', 1260, 2),
(15, 7, 'Fekete áttetsző akril kockaszett', 2100, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nev` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leiras` text COLLATE utf8mb4_unicode_ci,
  `tipus` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `szin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ar` int NOT NULL,
  `keszlet` int NOT NULL DEFAULT '10',
  `keszlet_db` int DEFAULT '0',
  `kep_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `products`
--

INSERT INTO `products` (`id`, `nev`, `leiras`, `tipus`, `szin`, `ar`, `keszlet`, `keszlet_db`, `kep_url`) VALUES
(18, 'Piros gyöngyház akril kockaszett', 'piros', 'Akril', 'Piros', 1260, 8, 0, 'assets/img/1787519097_main_20.jpg'),
(2, 'Világoskék gyöngyház akril kockaszett', 'Világoskék gyöngyház akril kockaszett. ', 'Akril', 'Kék', 3990, 10, 8, 'assets/img/2.jpg'),
(3, 'Citromsárga áttetsző akril kockaszett', 'Citromsárga áttetsző akril kockaszett', 'Akril', 'Citromsárga', 2490, 9, 15, 'assets/img/3.jpg'),
(4, 'Sötétkék gyöngyház akril kockaszett', 'Sötétkék gyöngyház akril kockaszett. 7db-os, feket...', 'Akril', 'Kék', 2590, 10, 13, 'assets/img/12.jpg'),
(5, 'Fehér, fekete kopásnyomos retro akril kockaszett', 'Fehér, fekete kopásnyomos retro akril kockaszett', 'Akril', 'Fehér', 2690, 10, 20, 'assets/img/19.jpg'),
(6, 'Zöld gyöngyház akril kockaszett', 'Zöld gyöngyház akril kockaszett', 'Akril', 'Zöld', 2590, 4, 25, 'assets/img/17.jpg'),
(9, 'kocka', 'Ez a termék leírása, nagyon jól össze szedett leírás...', 'Akril', 'Piros', 3144, 10, 0, 'assets/img/1787420192_15.jpg'),
(8, '10 darabos zöld, átlátszó D6 kocka', NULL, 'Akril', 'Zöld', 1800, 2, 0, 'assets/img/1787338261_D6zold10db.jpg'),
(10, 'Lila áttetsző akril kockaszett', 'Lila termék leírása', 'Akril', 'Lila', 2499, 9, 0, 'assets/img/1787517410_main_24.jpg'),
(11, 'Lila gyöngyház akril kockaszett', 'Lila gyöngyház', 'Akril', 'Lila', 2590, 10, 0, 'assets/img/1787517438_main_25.jpg'),
(12, 'Lila-rózsaszín-kék csillámos akril kockaszett', 'Lila tömör', 'Akril', 'Lila', 2690, 10, 0, 'assets/img/1787517551_main_10.jpg'),
(13, 'Ancient Gold Bone Collector fém dobókocka készlet', 'fém dobókocka készlet', 'Fém', 'Arany', 15600, 9, 0, 'assets/img/1787517944_main_AC00133.jpg'),
(14, 'Fa dobókocka csomag', 'fábol van', 'Fa', 'Fehér', 1500, 10, 0, 'assets/img/1787518389_main_fadkocka2.jpg'),
(15, 'Áttetsző Borostyán Hatoldalas Dobókocka Készlet', 'Borostyán hűha', 'Borostyán', 'Arany', 1890, 9, 0, 'assets/img/1787518630_main_borostyan2.jpg'),
(16, 'Rózsaszín gyöngyház akril kockaszett', 'ooo', 'Akril', 'Rózsaszín', 1750, 10, 0, 'assets/img/1787518936_main_28.jpg'),
(17, 'Fekete áttetsző akril kockaszett', 'sötét', 'Akril', 'Fekete', 2100, 9, 0, 'assets/img/1787519013_main_35.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `kep_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `kep_url`) VALUES
(1, 3, 'assets/img/1787513553_gal_0_31.jpg'),
(2, 14, 'assets/img/1787518389_gal_0_fadkocka.jpg'),
(3, 15, 'assets/img/1787518630_gal_0_borostyan3.jpg'),
(4, 15, 'assets/img/1787518630_gal_1_borostyankocka.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `felhasznalonev` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vezeteknev` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keresztnev` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iranyitoszam` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepules` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jelszo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `regisztracio_ideje` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `felhasznalonev` (`felhasznalonev`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `felhasznalonev`, `vezeteknev`, `keresztnev`, `email`, `telefon`, `iranyitoszam`, `telepules`, `cim`, `jelszo`, `is_admin`, `regisztracio_ideje`) VALUES
(1, 'admin', NULL, NULL, 'admin@rpgkocka.hu', NULL, NULL, NULL, NULL, '$2y$10$IIrZDHwGcApjr3UcA0DflOjUZQ6m3T3kSigsNWqeN9vsyVg94JUcy', 1, '2026-08-20 23:35:57'),
(2, 'kalandor', 'Kal', 'Andor', 'kalandor@gmail.com', '+36701234567', '2421', 'Ózd', 'Nefelejts utca 4', '$2y$10$qMeGJ6CvvIue/VX3v21bMuw7Z/OpJW/b6ka9EFCOG/qbrAm/ge9j.', 0, '2026-08-20 23:47:16');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
