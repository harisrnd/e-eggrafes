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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_sectors_field_data
CREATE TABLE IF NOT EXISTS `eepal_sectors_field_data` (
  `id` int(10) unsigned NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`,`langcode`),
  KEY `eepal_sectors__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  KEY `eepal_sectors_field__user_id__target_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_sectors entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_sectors_field_data: ~9 rows (approximately)
/*!40000 ALTER TABLE `eepal_sectors_field_data` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `eepal_sectors_field_data` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
