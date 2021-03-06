-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:04:48
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
-- Δομή πίνακα για τον πίνακα `eepal_admin_area_field_data`
--

CREATE TABLE `eepal_admin_area_field_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(80) DEFAULT NULL,
  `registry_no` varchar(50) NOT NULL,
  `region_to_belong` int(10) UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_admin_area entities.';

--
-- Άδειασμα δεδομένων του πίνακα `eepal_admin_area_field_data`
--

INSERT INTO `eepal_admin_area_field_data` (`id`, `langcode`, `user_id`, `name`, `registry_no`, `region_to_belong`, `status`, `created`, `changed`, `default_langcode`) VALUES
(1, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΡΑΜΑΣ', '0900115', 12, 1, 1482342004, 1482342004, 1),
(2, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΒΡΟΥ', '1100115', 12, 1, 1482342004, 1482342004, 1),
(3, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΒΑΛΑΣ', '2100105', 12, 1, 1482342004, 1482342004, 1),
(4, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΞΑΝΘΗΣ', '3700115', 12, 1, 1482342004, 1482342004, 1),
(5, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ', '4200115', 12, 1, 1482342004, 1482342004, 1),
(6, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Α΄ ΑΘΗΝΑΣ', '0500105', 3, 1, 1482342004, 1482342004, 1),
(7, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΝΑΤΟΛΙΚΗΣ ΑΤΤΙΚΗΣ', '0500205', 3, 1, 1482342004, 1482342004, 1),
(8, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΑΘΗΝΑΣ', '0500106', 3, 1, 1482342004, 1482342004, 1),
(9, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Γ΄ ΑΘΗΝΑΣ', '0500107', 3, 1, 1482342004, 1482342004, 1),
(10, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Δ΄ ΑΘΗΝΑΣ', '0500108', 3, 1, 1482342004, 1482342004, 1),
(11, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΥΤΙΚΗΣ ΑΤΤΙΚΗΣ', '0500305', 3, 1, 1482342004, 1482342004, 1),
(12, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΕΙΡΑΙΑ', '5200105', 3, 1, 1482342004, 1482342004, 1),
(13, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΕΣΒΟΥ', '3300115', 8, 1, 1482342004, 1482342004, 1),
(14, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΣΑΜΟΥ', '4300115', 8, 1, 1482342004, 1482342004, 1),
(15, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΙΟΥ', '5100115', 8, 1, 1482342004, 1482342004, 1),
(16, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΙΤΩΛΟΑΚΑΡΝΑΝΙΑΣ', '0100105', 1, 1, 1482342004, 1482342004, 1),
(17, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΧΑΪΑΣ', '0600105', 1, 1, 1482342004, 1482342004, 1),
(18, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΛΕΙΑΣ', '1500115', 1, 1, 1482342004, 1482342004, 1),
(19, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΓΡΕΒΕΝΩΝ', '0800115', 10, 1, 1482342004, 1482342004, 1),
(20, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΣΤΟΡΙΑΣ', '2300115', 10, 1, 1482342004, 1482342004, 1),
(21, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΟΖΑΝΗΣ', '2700105', 10, 1, 1482342004, 1482342004, 1),
(22, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΛΩΡΙΝΑΣ', '4700115', 10, 1, 1482342004, 1482342004, 1),
(23, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΤΑΣ', '0400115', 11, 1, 1482342004, 1482342004, 1),
(24, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΘΕΣΠΡΩΤΙΑΣ', '1800115', 11, 1, 1482342004, 1482342004, 1),
(25, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΙΩΑΝΝΙΝΩΝ', '2000105', 11, 1, 1482342004, 1482342004, 1),
(26, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΡΕΒΕΖΑΣ', '4000115', 11, 1, 1482342004, 1482342004, 1),
(27, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΡΔΙΤΣΑΣ', '2200105', 7, 1, 1482342004, 1482342004, 1),
(28, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΡΙΣΑΣ', '3100105', 7, 1, 1482342004, 1482342004, 1),
(29, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΜΑΓΝΗΣΙΑΣ', '3500105', 7, 1, 1482342004, 1482342004, 1),
(30, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΤΡΙΚΑΛΩΝ', '4500105', 7, 1, 1482342004, 1482342004, 1),
(31, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΖΑΚΥΝΘΟΥ', '1400115', 13, 1, 1482342004, 1482342004, 1),
(32, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΕΡΚΥΡΑΣ', '2400115', 13, 1, 1482342004, 1482342004, 1),
(33, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΕΦΑΛΛΗΝΙΑΣ', '2500115', 13, 1, 1482342004, 1482342004, 1),
(34, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΕΥΚΑΔΑΣ', '3400115', 13, 1, 1482342004, 1482342004, 1),
(35, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΝΑΤ. ΘΕΣ/ΝΙΚΗΣ', '1900105', 9, 1, 1482342004, 1482342004, 1),
(36, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΥΤ. ΘΕΣ/ΝΙΚΗΣ', '1900145', 9, 1, 1482342004, 1482342004, 1),
(37, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΜΑΘΙΑΣ', '1600105', 9, 1, 1482342004, 1482342004, 1),
(38, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΙΛΚΙΣ', '2600115', 9, 1, 1482342004, 1482342004, 1),
(39, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΕΛΛΑΣ', '3800105', 9, 1, 1482342004, 1482342004, 1),
(40, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΙΕΡΙΑΣ', '3900105', 9, 1, 1482342004, 1482342004, 1),
(41, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΣΕΡΡΩΝ', '4400105', 9, 1, 1482342004, 1482342004, 1),
(42, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΑΛΚΙΔΙΚΗΣ', '4900115', 9, 1, 1482342004, 1482342004, 1),
(43, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΡΑΚΛΕΙΟΥ', '1700105', 6, 1, 1482342004, 1482342004, 1),
(44, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΣΙΘΙΟΥ', '3200115', 6, 1, 1482342004, 1482342004, 1),
(45, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΕΘΥΜΝΟΥ', '4100115', 6, 1, 1482342004, 1482342004, 1),
(46, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΑΝΙΩΝ', '5000105', 6, 1, 1482342004, 1482342004, 1),
(47, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΩΔΕΚΑΝΗΣΟΥ', '1000105', 4, 1, 1482342004, 1482342004, 1),
(48, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΥΚΛΑΔΩΝ', '2900105', 4, 1, 1482342004, 1482342004, 1),
(49, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΓΟΛΙΔΑΣ', '0200115', 2, 1, 1482342004, 1482342004, 1),
(50, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΚΑΔΙΑΣ', '0300105', 2, 1, 1482342004, 1482342004, 1),
(51, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΟΡΙΝΘΙΑΣ', '2800105', 2, 1, 1482342004, 1482342004, 1),
(52, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΚΩΝΙΑΣ', '3000115', 2, 1, 1482342004, 1482342004, 1),
(53, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΜΕΣΣΗΝΙΑΣ', '3600115', 2, 1, 1482342004, 1482342004, 1),
(54, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΒΟΙΩΤΙΑΣ', '0700105', 5, 1, 1482342004, 1482342004, 1),
(55, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΥΒΟΙΑΣ', '1200105', 5, 1, 1482342004, 1482342004, 1),
(56, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΥΡΥΤΑΝΙΑΣ', '1300115', 5, 1, 1482342004, 1482342004, 1),
(57, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΘΙΩΤΙΔΑΣ', '4600105', 5, 1, 1482342004, 1482342004, 1),
(58, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΩΚΙΔΑΣ', '4800115', 5, 1, 1482342004, 1482342004, 1),
(59, 'el', 1, 'ΣΙΒΙΤΑΝΙΔΕΙΟΣ ΔΗΜΟΣΙΑ ΣΧΟΛΗ ΤΕΧΝΩΝ ΚΑΙ ΕΠΑΓΓΕΛΜΑΤΩΝ', 'sivit97', 14, 1, 1497445533, 1497445533, 1);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `eepal_admin_area_field_data`
--
ALTER TABLE `eepal_admin_area_field_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD UNIQUE KEY `uidx_adminarea_regno` (`registry_no`),
  ADD KEY `eepal_admin_area__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `eepal_admin_area_field__user_id__target_id` (`user_id`),
  ADD KEY `eepal_admin_area__4ae861cb00` (`region_to_belong`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
