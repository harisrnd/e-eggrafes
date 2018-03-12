-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.14 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2017-11-06 11:27:07
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_admin_area_field_data
CREATE TABLE IF NOT EXISTS `eepal_admin_area_field_data` (
  `id` int(10) unsigned NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(80) DEFAULT NULL,
  `region_to_belong` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the target entity.',
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL,
  `registry_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`,`langcode`),
  KEY `eepal_admin_area__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  KEY `eepal_admin_area_field__user_id__target_id` (`user_id`),
  KEY `eepal_admin_area__4ae861cb00` (`region_to_belong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_admin_area entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_admin_area_field_data: ~59 rows (approximately)
/*!40000 ALTER TABLE `eepal_admin_area_field_data` DISABLE KEYS */;
INSERT INTO `eepal_admin_area_field_data` (`id`, `langcode`, `user_id`, `name`, `region_to_belong`, `status`, `created`, `changed`, `default_langcode`, `registry_no`) VALUES
	(1, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΡΑΜΑΣ', 12, 1, 1482342004, 1482342004, 1, '0900115'),
	(2, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΒΡΟΥ', 12, 1, 1482342004, 1482342004, 1, '1100115'),
	(3, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΒΑΛΑΣ', 12, 1, 1482342004, 1482342004, 1, '2100105'),
	(4, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΞΑΝΘΗΣ', 12, 1, 1482342004, 1482342004, 1, '3700115'),
	(5, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ', 12, 1, 1482342004, 1482342004, 1, '4200115'),
	(6, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Α΄ ΑΘΗΝΑΣ', 3, 1, 1482342004, 1482342004, 1, '0500105'),
	(7, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΝΑΤΟΛΙΚΗΣ ΑΤΤΙΚΗΣ', 3, 1, 1482342004, 1482342004, 1, '0500205'),
	(8, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΑΘΗΝΑΣ', 3, 1, 1482342004, 1482342004, 1, '0500106'),
	(9, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Γ΄ ΑΘΗΝΑΣ', 3, 1, 1482342004, 1482342004, 1, '0500107'),
	(10, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. Δ΄ ΑΘΗΝΑΣ', 3, 1, 1482342004, 1482342004, 1, '0500108'),
	(11, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΥΤΙΚΗΣ ΑΤΤΙΚΗΣ', 3, 1, 1482342004, 1482342004, 1, '0500305'),
	(12, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΕΙΡΑΙΑ', 3, 1, 1482342004, 1482342004, 1, '5200105'),
	(13, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΕΣΒΟΥ', 8, 1, 1482342004, 1482342004, 1, '3300115'),
	(14, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΣΑΜΟΥ', 8, 1, 1482342004, 1482342004, 1, '4300115'),
	(15, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΙΟΥ', 8, 1, 1482342004, 1482342004, 1, '5100115'),
	(16, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΙΤΩΛΟΑΚΑΡΝΑΝΙΑΣ', 1, 1, 1482342004, 1482342004, 1, '0100105'),
	(17, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΧΑΪΑΣ', 1, 1, 1482342004, 1482342004, 1, '0600105'),
	(18, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΛΕΙΑΣ', 1, 1, 1482342004, 1482342004, 1, '1500115'),
	(19, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΓΡΕΒΕΝΩΝ', 10, 1, 1482342004, 1482342004, 1, '0800115'),
	(20, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΣΤΟΡΙΑΣ', 10, 1, 1482342004, 1482342004, 1, '2300115'),
	(21, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΟΖΑΝΗΣ', 10, 1, 1482342004, 1482342004, 1, '2700105'),
	(22, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΛΩΡΙΝΑΣ', 10, 1, 1482342004, 1482342004, 1, '4700115'),
	(23, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΤΑΣ', 11, 1, 1482342004, 1482342004, 1, '0400115'),
	(24, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΘΕΣΠΡΩΤΙΑΣ', 11, 1, 1482342004, 1482342004, 1, '1800115'),
	(25, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΙΩΑΝΝΙΝΩΝ', 11, 1, 1482342004, 1482342004, 1, '2000105'),
	(26, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΡΕΒΕΖΑΣ', 11, 1, 1482342004, 1482342004, 1, '4000115'),
	(27, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΑΡΔΙΤΣΑΣ', 7, 1, 1482342004, 1482342004, 1, '2200105'),
	(28, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΡΙΣΑΣ', 7, 1, 1482342004, 1482342004, 1, '3100105'),
	(29, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΜΑΓΝΗΣΙΑΣ', 7, 1, 1482342004, 1482342004, 1, '3500105'),
	(30, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΤΡΙΚΑΛΩΝ', 7, 1, 1482342004, 1482342004, 1, '4500105'),
	(31, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΖΑΚΥΝΘΟΥ', 13, 1, 1482342004, 1482342004, 1, '1400115'),
	(32, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΕΡΚΥΡΑΣ', 13, 1, 1482342004, 1482342004, 1, '2400115'),
	(33, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΕΦΑΛΛΗΝΙΑΣ', 13, 1, 1482342004, 1482342004, 1, '2500115'),
	(34, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΕΥΚΑΔΑΣ', 13, 1, 1482342004, 1482342004, 1, '3400115'),
	(35, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΝΑΤ. ΘΕΣ/ΝΙΚΗΣ', 9, 1, 1482342004, 1482342004, 1, '1900105'),
	(36, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΥΤ. ΘΕΣ/ΝΙΚΗΣ', 9, 1, 1482342004, 1482342004, 1, '1900145'),
	(37, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΜΑΘΙΑΣ', 9, 1, 1482342004, 1482342004, 1, '1600105'),
	(38, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΙΛΚΙΣ', 9, 1, 1482342004, 1482342004, 1, '2600115'),
	(39, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΕΛΛΑΣ', 9, 1, 1482342004, 1482342004, 1, '3800105'),
	(40, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΠΙΕΡΙΑΣ', 9, 1, 1482342004, 1482342004, 1, '3900105'),
	(41, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΣΕΡΡΩΝ', 9, 1, 1482342004, 1482342004, 1, '4400105'),
	(42, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΑΛΚΙΔΙΚΗΣ', 9, 1, 1482342004, 1482342004, 1, '4900115'),
	(43, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΗΡΑΚΛΕΙΟΥ', 6, 1, 1482342004, 1482342004, 1, '1700105'),
	(44, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΣΙΘΙΟΥ', 6, 1, 1482342004, 1482342004, 1, '3200115'),
	(45, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΕΘΥΜΝΟΥ', 6, 1, 1482342004, 1482342004, 1, '4100115'),
	(46, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΧΑΝΙΩΝ', 6, 1, 1482342004, 1482342004, 1, '5000105'),
	(47, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΔΩΔΕΚΑΝΗΣΟΥ', 4, 1, 1482342004, 1482342004, 1, '1000105'),
	(48, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΥΚΛΑΔΩΝ', 4, 1, 1482342004, 1482342004, 1, '2900105'),
	(49, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΓΟΛΙΔΑΣ', 2, 1, 1482342004, 1482342004, 1, '0200115'),
	(50, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΡΚΑΔΙΑΣ', 2, 1, 1482342004, 1482342004, 1, '0300105'),
	(51, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΟΡΙΝΘΙΑΣ', 2, 1, 1482342004, 1482342004, 1, '2800105'),
	(52, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΛΑΚΩΝΙΑΣ', 2, 1, 1482342004, 1482342004, 1, '3000115'),
	(53, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΜΕΣΣΗΝΙΑΣ', 2, 1, 1482342004, 1482342004, 1, '3600115'),
	(54, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΒΟΙΩΤΙΑΣ', 5, 1, 1482342004, 1482342004, 1, '0700105'),
	(55, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΥΒΟΙΑΣ', 5, 1, 1482342004, 1482342004, 1, '1200105'),
	(56, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΕΥΡΥΤΑΝΙΑΣ', 5, 1, 1482342004, 1482342004, 1, '1300115'),
	(57, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΘΙΩΤΙΔΑΣ', 5, 1, 1482342004, 1482342004, 1, '4600105'),
	(58, 'el', 1, 'ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΦΩΚΙΔΑΣ', 5, 1, 1482342004, 1482342004, 1, '4800115'),
	(59, 'el', 1, 'ΣΙΒΙΤΑΝΙΔΕΙΟΣ ΔΗΜΟΣΙΑ ΣΧΟΛΗ ΤΕΧΝΩΝ ΚΑΙ ΕΠΑΓΓΕΛΜΑΤΩΝ', 14, 1, 1497445533, 1497445533, 1, 'sivit97');
/*!40000 ALTER TABLE `eepal_admin_area_field_data` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
