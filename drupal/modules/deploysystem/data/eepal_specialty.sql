-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:23:43
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
-- Δομή πίνακα για τον πίνακα `eepal_specialty`
--

CREATE TABLE `eepal_specialty` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_specialty entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_specialty`
--

INSERT INTO `eepal_specialty` (`id`, `uuid`, `langcode`) VALUES
(1, '4972cde0-a100-40a8-bb36-89f9f1a88fe55', 'el'),
(2, '4972cde0-a100-40a8-bb36-89f9f1a88fe2', 'el'),
(3, '4972cde0-a100-40a8-bb36-89f9f1a88fe3', 'el'),
(4, '4972cde0-a100-40a8-bb36-89f9f1a88fe4', 'el'),
(5, '4972cde0-a100-40a8-bb36-89f9f1a88fe5', 'el'),
(9, '4972cde0-a100-40a8-bb36-89f9f1a88fe9', 'el'),
(11, '4972cde0-a100-40a8-bb36-89f9f1a88fe11', 'el'),
(12, '4972cde0-a100-40a8-bb36-89f9f1a88fe12', 'el'),
(13, '4972cde0-a100-40a8-bb36-89f9f1a88fe13', 'el'),
(15, '4972cde0-a100-40a8-bb36-89f9f1a88fe15', 'el'),
(17, '4972cde0-a100-40a8-bb36-89f9f1a88fe17', 'el'),
(18, '4972cde0-a100-40a8-bb36-89f9f1a88fe18', 'el'),
(19, '4972cde0-a100-40a8-bb36-89f9f1a88fe19', 'el'),
(20, '4972cde0-a100-40a8-bb36-89f9f1a88fe20', 'el'),
(21, '4972cde0-a100-40a8-bb36-89f9f1a88fe21', 'el'),
(22, '4972cde0-a100-40a8-bb36-89f9f1a88fe22', 'el'),
(24, '4972cde0-a100-40a8-bb36-89f9f1a88fe24', 'el'),
(25, '4972cde0-a100-40a8-bb36-89f9f1a88fe25', 'el'),
(28, '4972cde0-a100-40a8-bb36-89f9f1a88fe28', 'el'),
(29, '4972cde0-a100-40a8-bb36-89f9f1a88fe29', 'el'),
(32, '4972cde0-a100-40a8-bb36-89f9f1a88fe32', 'el'),
(33, '4972cde0-a100-40a8-bb36-89f9f1a88fe33', 'el'),
(34, '4972cde0-a100-40a8-bb36-89f9f1a88fe34', 'el'),
(36, '4972cde0-a100-40a8-bb36-89f9f1a88fe36', 'el'),
(37, '4972cde0-a100-40a8-bb36-89f9f1a88fe37', 'el'),
(38, '4972cde0-a100-40a8-bb36-89f9f1a88fe38', 'el'),
(39, '4972cde0-a100-40a8-bb36-89f9f1a88fe39', 'el'),
(40, '4972cde0-a100-40a8-bb36-89f9f1a88fe40', 'el'),
(41, '4972cde0-a100-40a8-bb36-89f9f1a88fe41', 'el'),
(42, '4972cde0-a100-40a8-bb36-89f9f1a88fe42', 'el'),
(43, '4972cde0-a100-40a8-bb36-89f9f1a88fe43', 'el'),
(45, '4972cde0-a100-40a8-bb36-89f9f1a88fe45', 'el'),
(49, '4972cde0-a100-40a8-bb36-89f9f1a88fe49', 'el'),
(51, '4972cde0-a100-40a8-bb36-89f9f1a88fe51', 'el'),
(86, 'cb0321fd-7e79-4f8c-9e1c-f3fcea316656', 'el');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_specialty`
--
ALTER TABLE `eepal_specialty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eepal_specialty_field__uuid__value` (`uuid`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `eepal_specialty`
--
ALTER TABLE `eepal_specialty`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
