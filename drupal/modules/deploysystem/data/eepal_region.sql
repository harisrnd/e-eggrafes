-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:04:59
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
-- Δομή πίνακα για τον πίνακα `eepal_region`
--

CREATE TABLE `eepal_region` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_region entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_region`
--

INSERT INTO `eepal_region` (`id`, `uuid`, `langcode`) VALUES
(1, 'f52ad308-915b-482d-a242-d81b13a04a5a', 'el'),
(2, '8f2ca395-a1b0-4d42-bee3-095546729b7d', 'el'),
(3, 'd3e0c5c5-1dd3-482d-84c5-19ad0560df25', 'el'),
(4, '49a1a2ea-f24d-4784-8bcf-f3e0c851b639', 'el'),
(5, 'b08f260c-1cff-4337-b7cd-ae19ccc80919', 'el'),
(6, '48afe4cf-f3a6-46fc-83ca-5e44595a3827', 'el'),
(7, '18d799d2-fafa-47f6-957a-7cf28b5ce4d8', 'el'),
(8, '8e0900b5-793e-42e2-af7b-cc7988bebff3', 'el'),
(9, '00f2ee22-6b13-426e-bbdf-952b41cf7a86', 'el'),
(10, '4e627770-2ae5-4879-a9d7-b1696468825d', 'el'),
(11, '650866a1-b5bf-46b6-8e81-61b61016ac70', 'el'),
(12, 'e705638d-6d6c-4b80-ac36-42eb9458a191', 'el'),
(13, 'a6e3e44b-b8e8-40fd-8ade-8dd65f5e6cf2', 'el'),
(14, '3371288b-7d74-4860-961c-bfbee6c4e4e4', 'el');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_region`
--
ALTER TABLE `eepal_region`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eepal_region_field__uuid__value` (`uuid`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `eepal_region`
--
ALTER TABLE `eepal_region`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
