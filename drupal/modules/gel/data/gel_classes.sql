-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 09 Ιαν 2018 στις 13:06:25
-- Έκδοση διακομιστή: 5.7.20-0ubuntu0.16.04.1
-- Έκδοση PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `mydrupal`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `gel_classes`
--

CREATE TABLE `gel_classes` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for gel_classes entities.';

--
-- Άδειασμα δεδομένων του πίνακα `gel_classes`
--

INSERT INTO `gel_classes` (`id`, `uuid`, `langcode`, `user_id`, `name`, `status`, `created`, `changed`, `category`) VALUES
(1, '422ae3a9-41c5-4ac5-a47c-5a98ece6428c', 'el', 1, 'Α\' Λυκείου', 1, 1515487406, 1515495918, 'ΗΜΕΡΗΣΙΟ'),
(2, 'a7c70480-0a1c-4fa2-b25e-443d32a63483', 'el', 1, 'Β\' Λυκείου', 1, 1515487432, 1515495926, 'ΗΜΕΡΗΣΙΟ'),
(3, '9ba608fd-ea43-44f3-879d-b3e9189b70b4', 'el', 1, 'Γ\' Λυκείου', 1, 1515487448, 1515495933, 'ΗΜΕΡΗΣΙΟ'),
(4, '57aa09f4-4f88-4294-8aff-82092a225df4', 'el', 1, 'Α\' Λυκείου', 1, 1515487479, 1515495945, 'ΕΣΠΕΡΙΝΟ'),
(5, '65b53e21-23c8-4a67-89f4-00df9957fc07', 'el', 1, 'Β\' Λυκείου', 1, 1515487493, 1515495953, 'ΕΣΠΕΡΙΝΟ'),
(6, '49415b72-1e4e-481f-b5d9-d9943b90177a', 'el', 1, 'Γ\' Λυκείου', 1, 1515487506, 1515495960, 'ΕΣΠΕΡΙΝΟ'),
(7, '1b71cef2-85e8-4512-a4e9-f02dd931cc7c', 'el', 1, 'Δ\' Λυκείου', 1, 1515487524, 1515495966, 'ΕΣΠΕΡΙΝΟ');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `gel_classes`
--
ALTER TABLE `gel_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gel_classes_field__uuid__value` (`uuid`),
  ADD KEY `gel_classes_field__user_id__target_id` (`user_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `gel_classes`
--
ALTER TABLE `gel_classes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
