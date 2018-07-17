-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:05:08
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
-- Δομή πίνακα για τον πίνακα `eepal_region_field_data`
--

CREATE TABLE `eepal_region_field_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(80) DEFAULT NULL,
  `registry_no` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_region entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_region_field_data`
--

INSERT INTO `eepal_region_field_data` (`id`, `langcode`, `user_id`, `name`, `registry_no`, `status`, `created`, `changed`, `default_langcode`) VALUES
(1, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΔΥΤΙΚΗΣ ΕΛΛΑΔΑΣ', '9999903', 1, 1482308338, 1482308338, 1),
(2, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΠΕΛΟΠΟΝΝΗΣΟΥ', '9999904', 1, 1482308394, 1482308394, 1),
(3, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΑΤΤΙΚΗΣ', '9999901', 1, 1482308412, 1482308412, 1),
(4, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΝΟΤΙΟΥ ΑΙΓΑΙΟΥ', '9999912', 1, 1482308432, 1482308432, 1),
(5, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΣΤΕΡΕΑΣ ΕΛΛΑΔΑΣ', '9999902', 1, 1482308452, 1482308452, 1),
(6, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΚΡΗΤΗΣ', '9999910', 1, 1482308485, 1482308485, 1),
(7, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΘΕΣΣΑΛΙΑΣ', '9999905', 1, 1482308510, 1482308510, 1),
(8, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΒΟΡΕΙΟΥ ΑΙΓΑΙΟΥ', '9999911', 1, 1482308526, 1482308526, 1),
(9, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΚΕΝΤΡΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ', '9999906', 1, 1482308542, 1482308542, 1),
(10, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΔΥΤΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ', '9999907', 1, 1482308557, 1482308557, 1),
(11, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΗΠΕΙΡΟΥ', '9999909', 1, 1482308571, 1482308571, 1),
(12, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΑΝΑΤΟΛΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ ΚΑΙ ΘΡΑΚΗΣ', '9999908', 1, 1482308610, 1482308610, 1),
(13, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΙΟΝΙΩΝ ΝΗΣΩΝ', '9999913', 1, 1482308625, 1482308625, 1),
(14, 'el', 1, 'ΣΙΒΙΤΑΝΙΔΕΙΟΣ ΔΗΜΟΣΙΑ ΣΧΟΛΗ ΤΕΧΝΩΝ ΚΑΙ ΕΠΑΓΓΕΛΜΑΤΩΝ', 'sivit97', 1, 1497444547, 1497444547, 1);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_region_field_data`
--
ALTER TABLE `eepal_region_field_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD UNIQUE KEY `uidx_region_regno` (`registry_no`),
  ADD KEY `eepal_region__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `eepal_region_field__user_id__target_id` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
