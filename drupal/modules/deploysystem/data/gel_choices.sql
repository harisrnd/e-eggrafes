-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:25:22
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
-- Δομή πίνακα για τον πίνακα `gel_choices`
--

CREATE TABLE `gel_choices` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(60) DEFAULT NULL,
  `choicetype` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for gel_choices entities.';

--
-- Άδειασμα δεδομένων του πίνακα `gel_choices`
--

INSERT INTO `gel_choices` (`id`, `uuid`, `langcode`, `user_id`, `name`, `choicetype`, `status`, `created`, `changed`) VALUES
(1, '93f9f8a4-bbf9-486e-8d21-07629ac3edf0', 'el', 1, 'Αγγλικά', 'ΞΓ', 1, 1515488381, 1515488381),
(2, '4434753d-10fd-4227-aa33-ee4a48d14cba', 'el', 1, 'Γαλλικά', 'ΞΓ', 1, 1515488400, 1515488400),
(3, 'f199a4dc-7b09-4536-aa74-4a5a62fd4684', 'el', 1, 'Γερμανικά', 'ΞΓ', 1, 1515488424, 1515488424),
(4, '721ca07d-0295-4d34-b9d3-ac9cce05f027', 'el', 1, 'Εφαρμογές Πληροφορικής', 'ΕΠΙΛΟΓΗ', 1, 1515488491, 1515488491),
(5, 'fb1ce555-43dd-4028-9fd4-d1ccc1c44422', 'el', 1, 'Γεωλογία και Διαχείριση Φυσικών Πόρων', 'ΕΠΙΛΟΓΗ', 1, 1515488532, 1515488532),
(6, '7b410e28-8896-46e6-8b62-41d51ceaee6b', 'el', 1, 'Ελληνικός και Ευρωπαϊκός Πολιτισμός', 'ΕΠΙΛΟΓΗ', 1, 1515488569, 1515488569),
(7, 'ffb55e0b-2fa5-4c1e-b95c-41956a995565', 'el', 1, 'Καλλιτεχνική Παιδεία', 'ΕΠΙΛΟΓΗ', 1, 1515488593, 1515488593),
(8, 'cf48364b-21bc-4991-8c77-fe1bbf13874c', 'el', 1, 'Δεύτερη Ξένη Γλώσσα - Αγγλικά', 'ΕΠΙΛΟΓΗ', 1, 1515488657, 1515488657),
(9, 'a69243e5-af28-4ac6-b68f-dd1fd84a72b4', 'el', 1, 'Δεύτερη Ξένη Γλώσσα - Γαλλικά', 'ΕΠΙΛΟΓΗ', 1, 1515488677, 1515488677),
(10, 'fb4374e4-edd4-40b2-8b51-0beba7460cd7', 'el', 1, 'Δεύτερη Ξένη Γλώσσα - Γερμανικά', 'ΕΠΙΛΟΓΗ', 1, 1515488694, 1515488694),
(11, 'b958af86-fa9d-47ba-a025-8cd2f83d9c90', 'el', 1, 'Ελεύθερο Σχέδιο', 'ΕΠΙΛΟΓΗ', 1, 1515488720, 1515488720),
(12, 'd1b3efad-5e71-41e7-9781-d9b8759351fb', 'el', 1, 'Γραμμικό Σχέδιο', 'ΕΠΙΛΟΓΗ', 1, 1515488737, 1515488737),
(13, '1d5fef72-5f95-4558-92f1-1131861165d2', 'el', 1, 'Ιστορία της Τέχνης', 'ΕΠΙΛΟΓΗ', 1, 1515488759, 1515488759),
(14, 'f7dc6628-f122-44a4-b995-98c39c703280', 'el', 1, 'Αρχές Οργάνωσης και Διοίκησης Υπηρεσιών', 'ΕΠΙΛΟΓΗ', 1, 1515488798, 1515488798),
(15, '50be4626-45ee-40d7-afee-eebf9e5135bc', 'el', 1, 'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών', 'ΟΠ', 1, 1515488864, 1515488864),
(16, '95e08266-2d72-4eb7-9f15-2c0c5de2474a', 'el', 1, 'Ομάδα Προσανατολισμού Θετικών Σπουδών', 'ΟΠ', 1, 1515488909, 1515488909),
(17, 'e3de9dc8-15b5-47f8-a894-b2028e014e4f', 'el', 1, 'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής', 'ΟΠ', 1, 1515488970, 1515489678),
(18, '7096e2ba-2aa2-4e95-9c01-8c7a8411676c', 'el', 1, 'Ιταλικά', NULL, 1, 1525262325, 1525943763);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `gel_choices`
--
ALTER TABLE `gel_choices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gel_choices_field__uuid__value` (`uuid`),
  ADD KEY `gel_choices_field__user_id__target_id` (`user_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `gel_choices`
--
ALTER TABLE `gel_choices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
