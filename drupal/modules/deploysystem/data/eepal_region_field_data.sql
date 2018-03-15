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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_region_field_data
CREATE TABLE IF NOT EXISTS `eepal_region_field_data` (
  `id` int(10) unsigned NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(80) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  `default_langcode` tinyint(4) NOT NULL,
  `registry_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`,`langcode`),
  KEY `eepal_region__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  KEY `eepal_region_field__user_id__target_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The data table for eepal_region entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_region_field_data: ~14 rows (approximately)
/*!40000 ALTER TABLE `eepal_region_field_data` DISABLE KEYS */;
INSERT INTO `eepal_region_field_data` (`id`, `langcode`, `user_id`, `name`, `status`, `created`, `changed`, `default_langcode`, `registry_no`) VALUES
	(1, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΔΥΤΙΚΗΣ ΕΛΛΑΔΑΣ', 1, 1482308338, 1482308338, 1, '9999903'),
	(2, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΠΕΛΟΠΟΝΝΗΣΟΥ', 1, 1482308394, 1482308394, 1, '9999904'),
	(3, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΑΤΤΙΚΗΣ', 1, 1482308412, 1482308412, 1, '9999901'),
	(4, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΝΟΤΙΟΥ ΑΙΓΑΙΟΥ', 1, 1482308432, 1482308432, 1, '9999912'),
	(5, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΣΤΕΡΕΑΣ ΕΛΛΑΔΑΣ', 1, 1482308452, 1482308452, 1, '9999902'),
	(6, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΚΡΗΤΗΣ', 1, 1482308485, 1482308485, 1, '9999910'),
	(7, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΘΕΣΣΑΛΙΑΣ', 1, 1482308510, 1482308510, 1, '9999905'),
	(8, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΒΟΡΕΙΟΥ ΑΙΓΑΙΟΥ', 1, 1482308526, 1482308526, 1, '9999911'),
	(9, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΚΕΝΤΡΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ', 1, 1482308542, 1482308542, 1, '9999906'),
	(10, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΔΥΤΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ', 1, 1482308557, 1482308557, 1, '9999907'),
	(11, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΗΠΕΙΡΟΥ', 1, 1482308571, 1482308571, 1, '9999909'),
	(12, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΑΝΑΤΟΛΙΚΗΣ ΜΑΚΕΔΟΝΙΑΣ ΚΑΙ ΘΡΑΚΗΣ', 1, 1482308610, 1482308610, 1, '9999908'),
	(13, 'el', 1, 'ΠΕΡΙΦΕΡΕΙΑΚΗ ΔΙΕΥΘΥΝΣΗ ΙΟΝΙΩΝ ΝΗΣΩΝ', 1, 1482308625, 1482308625, 1, '9999913'),
	(14, 'el', 1, 'ΣΙΒΙΤΑΝΙΔΕΙΟΣ ΔΗΜΟΣΙΑ ΣΧΟΛΗ ΤΕΧΝΩΝ ΚΑΙ ΕΠΑΓΓΕΛΜΑΤΩΝ', 1, 1497444547, 1497444547, 1, 'sivit97');
/*!40000 ALTER TABLE `eepal_region_field_data` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
