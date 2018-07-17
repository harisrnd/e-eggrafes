-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:05:43
-- Έκδοση διακομιστή: 5.7.22-0ubuntu0.16.04.1
-- Έκδοση PHP: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `live-eggrafes-14_07_18`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `eepal_sectors`
--

CREATE TABLE `eepal_sectors` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_sectors entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_sectors`
--

INSERT INTO `eepal_sectors` (`id`, `uuid`, `langcode`) VALUES
(1, '6814e3d4-97c4-4180-b197-f28a2a137c42', 'el'),
(2, 'f9e68d7e-0364-4131-8240-997e121c0143', 'el'),
(3, '36cf7396-e75b-4100-8ebf-d4f7f98184c7', 'el'),
(4, 'a5015fc0-ba9f-4f55-a0f0-1a01631aec0e', 'el'),
(5, '39f91d7b-dc02-410d-9674-43347c8177d3', 'el'),
(6, 'f15fee66-b049-473e-9c84-309a65e4c2d7', 'el'),
(7, '0e5d63ac-b1e9-41f6-a5e2-5194860eab5e', 'el'),
(8, 'c35ace9e-0609-4a95-8599-9645236167c8', 'el'),
(9, '27e406ca-d465-4876-932a-b0ffface3488', 'el');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_sectors`
--
ALTER TABLE `eepal_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eepal_sectors_field__uuid__value` (`uuid`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `eepal_sectors`
--
ALTER TABLE `eepal_sectors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
