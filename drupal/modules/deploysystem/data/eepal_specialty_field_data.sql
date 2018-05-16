-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 06, 2018 at 03:29 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydrupal`
--

-- --------------------------------------------------------

--
-- Table structure for table `eepal_specialty_field_data`
--

CREATE TABLE IF NOT EXISTS `eepal_specialty_field_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(100) DEFAULT NULL,
  `sector_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_specialty entities.';

--
-- Dumping data for table `eepal_specialty_field_data`
--

INSERT INTO `eepal_specialty_field_data` (`id`, `langcode`, `user_id`, `name`, `sector_id`, `status`, `created`, `changed`, `default_langcode`) VALUES
(1, 'el', 1, 'Αισθητικής Τέχνης', 9, 1, 1482308338, 1485510661, 1),
(2, 'el', 1, 'Βοηθός Βρεφονηπιοκόμων', 9, 1, 1482308338, 1485510661, 1),
(3, 'el', 1, 'Βοηθός Νοσηλευτή', 9, 1, 1482308338, 1485510661, 1),
(4, 'el', 1, 'Βοηθός Φαρμακείου', 9, 1, 1482308338, 1485510661, 1),
(5, 'el', 1, 'Βοηθός Φυσικοθεραπευτή', 9, 1, 1482308338, 1485510661, 1),
(9, 'el', 1, 'Τεχνικός Εγκαταστάσεων Ψύξης Αερισμού και Κλιματισμού', 6, 1, 1482308338, 1485510661, 1),
(11, 'el', 1, 'Τεχνικός Ηλεκτρολογικών Συστημάτων, Εγκαταστάσεων και Δικτύων', 5, 1, 1482308338, 1485510661, 1),
(12, 'el', 1, 'Τεχνικός Μηχανολογικών Εγκαταστάσεων και Κατασκευών', 6, 1, 1482308338, 1485510661, 1),
(13, 'el', 1, 'Τεχνικός Οχημάτων', 6, 1, 1482308338, 1485510661, 1),
(15, 'el', 1, 'Υπάλληλος  Διοίκησης και Οικονομικών Υπηρεσιών', 2, 1, 1482308338, 1485510661, 1),
(17, 'el', 1, 'Τεχνικός Εφαρμογών Πληροφορικής', 8, 1, 1482308338, 1485510661, 1),
(18, 'el', 1, 'Τεχνικός Τεχνολογίας Τροφίμων και Ποτών', 1, 1, 1482308338, 1485510661, 1),
(19, 'el', 1, 'Τεχνικός Φυτικής Παραγωγής', 1, 1, 1482308338, 1485510661, 1),
(20, 'el', 1, 'Κομμωτικής Τέχνης', 9, 1, 1482308338, 1485510661, 1),
(21, 'el', 1, 'Σχεδιασμού-Διακόσμησης Εσωτερικών Χώρων', 4, 1, 1482308338, 1485510661, 1),
(22, 'el', 1, 'Τεχνικός Δομικών Έργων και Γεωπληροφορικής', 3, 1, 1482308338, 1485510661, 1),
(24, 'el', 1, 'Τεχνικός Ηλεκτρονικών και Υπολογιστικών Συστημάτων, Εγκαταστάσεων, Δικτύων και Τηλεπικοινωνιών', 5, 1, 1482308338, 1485510661, 1),
(25, 'el', 1, 'Βοηθός Ιατρικών - Βιολογικών Εργαστηρίων', 9, 1, 1482308338, 1485510661, 1),
(28, 'el', 1, 'Τεχνικός Η/Υ και Δικτύων Η/Υ', 8, 1, 1482308338, 1485510661, 1),
(29, 'el', 1, 'Τεχνικός Θερμικών και Υδραυλικών Εγκαταστάσεων και Τεχνολογίας Πετρελαίου και Φυσικού Αερίου', 6, 1, 1482308338, 1485510661, 1),
(32, 'el', 1, 'Γραφικών Τεχνών', 4, 1, 1482308338, 1485510661, 1),
(33, 'el', 1, 'Υπάλληλος Αποθήκης και Συστημάτων Εφοδιασμού', 2, 1, 1482308338, 1485510661, 1),
(34, 'el', 1, 'Τεχνικός Μηχανοσυνθέτης Αεροσκαφών', 6, 1, 1482308338, 1485510661, 1),
(36, 'el', 1, 'Αργυροχρυσοχοΐας', 4, 1, 1482308338, 1485510661, 1),
(37, 'el', 1, 'Υπάλληλος Εμπορίας και Διαφήμισης', 2, 1, 1482308338, 1485510661, 1),
(38, 'el', 1, 'Βοηθός Οδοντοτεχνίτη', 9, 1, 1482308338, 1485510661, 1),
(39, 'el', 1, 'Συντήρησης Έργων Τέχνης - Αποκατάστασης', 4, 1, 1482308338, 1485510661, 1),
(40, 'el', 1, 'Βοηθός Ακτινολογικών Εργαστηρίων', 9, 1, 1482308338, 1485510661, 1),
(41, 'el', 1, 'Μηχανικός Εμπορικού Ναυτικού', 7, 1, 1482308338, 1485510661, 1),
(42, 'el', 1, 'Τεχνικός Ανθοκομίας και Αρχιτεκτονικής Τοπίου', 1, 1, 1482308338, 1485510661, 1),
(43, 'el', 1, 'Σχεδίασης και Παραγωγής Ενδύματος', 4, 1, 1482308338, 1485510661, 1),
(45, 'el', 1, 'Πλοίαρχος Εμπορικού Ναυτικού', 7, 1, 1482308338, 1485510661, 1),
(49, 'el', 1, 'Υπάλληλος  Τουριστικών Επιχειρήσεων', 2, 1, 1482308338, 1485510661, 1),
(51, 'el', 1, 'Τεχνικός Ζωικής Παραγωγής', 1, 1, 1482308338, 1485510661, 1);
