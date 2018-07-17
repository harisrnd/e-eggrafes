-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:05:52
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
-- Δομή πίνακα για τον πίνακα `eepal_sectors_field_data`
--

CREATE TABLE `eepal_sectors_field_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_sectors entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_sectors_field_data`
--

INSERT INTO `eepal_sectors_field_data` (`id`, `langcode`, `user_id`, `name`, `status`, `created`, `changed`, `default_langcode`) VALUES
(1, 'el', 1, 'Τομέας Γεωπονίας, Τροφίμων και Περιβάλλοντος', 1, 1485510661, 1485510661, 1),
(2, 'el', 1, 'Τομέας Διοίκησης και Οικονομίας', 1, 1485510681, 1485510681, 1),
(3, 'el', 1, 'Τομέας Δομικών Έργων, Δομημένου Περιβάλλοντος και Αρχιτεκτονικού Σχεδιασμού', 1, 1485510698, 1485510698, 1),
(4, 'el', 1, 'Τομέας Εφαρμοσμένων Τεχνών', 1, 1485510719, 1485510719, 1),
(5, 'el', 1, 'Τομέας Ηλεκτρολογίας, Ηλεκτρονικής και Αυτοματισμού', 1, 1485510736, 1485510736, 1),
(6, 'el', 1, 'Τομέας Μηχανολογίας', 1, 1485510754, 1485510754, 1),
(7, 'el', 1, 'Τομέας Ναυτιλιακών Επαγγελμάτων', 1, 1485510770, 1485510770, 1),
(8, 'el', 1, 'Τομέας Πληροφορικής', 1, 1485510786, 1485510786, 1),
(9, 'el', 1, 'Τομέας Υγείας - Πρόνοιας - Ευεξίας', 1, 1485510805, 1485510805, 1);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_sectors_field_data`
--
ALTER TABLE `eepal_sectors_field_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD KEY `eepal_sectors__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `eepal_sectors_field__user_id__target_id` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
