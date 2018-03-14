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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_specialty
CREATE TABLE IF NOT EXISTS `eepal_specialty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eepal_specialty_field__uuid__value` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_specialty entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_specialty: ~70 rows (approximately)
/*!40000 ALTER TABLE `eepal_specialty` DISABLE KEYS */;
INSERT INTO `eepal_specialty` (`id`, `uuid`, `langcode`) VALUES
	(1, '4972cde0-a100-40a8-bb36-89f9f1a88fe55', 'el'),
	(2, '4972cde0-a100-40a8-bb36-89f9f1a88fe2', 'el'),
	(3, '4972cde0-a100-40a8-bb36-89f9f1a88fe3', 'el'),
	(4, '4972cde0-a100-40a8-bb36-89f9f1a88fe4', 'el'),
	(5, '4972cde0-a100-40a8-bb36-89f9f1a88fe5', 'el'),
	(9, '4972cde0-a100-40a8-bb36-89f9f1a88fe9', 'el'),
	(11, '4972cde0-a100-40a8-bb36-89f9f1a88fe11', 'el'),
	(12, '4972cde0-a100-40a8-bb36-89f9f1a88fe12', 'el'),
	(13, '4972cde0-a100-40a8-bb36-89f9f1a88fe13', 'el'),
	(15, '4972cde0-a100-40a8-bb36-89f9f1a88fe15', 'el'),
	(17, '4972cde0-a100-40a8-bb36-89f9f1a88fe17', 'el'),
	(18, '4972cde0-a100-40a8-bb36-89f9f1a88fe18', 'el'),
	(19, '4972cde0-a100-40a8-bb36-89f9f1a88fe19', 'el'),
	(20, '4972cde0-a100-40a8-bb36-89f9f1a88fe20', 'el'),
	(21, '4972cde0-a100-40a8-bb36-89f9f1a88fe21', 'el'),
	(22, '4972cde0-a100-40a8-bb36-89f9f1a88fe22', 'el'),
	(24, '4972cde0-a100-40a8-bb36-89f9f1a88fe24', 'el'),
	(25, '4972cde0-a100-40a8-bb36-89f9f1a88fe25', 'el'),
	(28, '4972cde0-a100-40a8-bb36-89f9f1a88fe28', 'el'),
	(29, '4972cde0-a100-40a8-bb36-89f9f1a88fe29', 'el'),
	(32, '4972cde0-a100-40a8-bb36-89f9f1a88fe32', 'el'),
	(33, '4972cde0-a100-40a8-bb36-89f9f1a88fe33', 'el'),
	(34, '4972cde0-a100-40a8-bb36-89f9f1a88fe34', 'el'),
	(36, '4972cde0-a100-40a8-bb36-89f9f1a88fe36', 'el'),
	(37, '4972cde0-a100-40a8-bb36-89f9f1a88fe37', 'el'),
	(38, '4972cde0-a100-40a8-bb36-89f9f1a88fe38', 'el'),
	(39, '4972cde0-a100-40a8-bb36-89f9f1a88fe39', 'el'),
	(40, '4972cde0-a100-40a8-bb36-89f9f1a88fe40', 'el'),
	(41, '4972cde0-a100-40a8-bb36-89f9f1a88fe41', 'el'),
	(42, '4972cde0-a100-40a8-bb36-89f9f1a88fe42', 'el'),
	(43, '4972cde0-a100-40a8-bb36-89f9f1a88fe43', 'el'),
	(45, '4972cde0-a100-40a8-bb36-89f9f1a88fe45', 'el'),
	(49, '4972cde0-a100-40a8-bb36-89f9f1a88fe49', 'el'),
	(51, '4972cde0-a100-40a8-bb36-89f9f1a88fe51', 'el'),
	(52, '4972cde0-a100-40a8-bb36-89f9f1a88fe90', 'el'),
	(53, '4972cde0-a100-40a8-bb36-89f9f1a88fe56', 'el'),
	(54, '4972cde0-a100-40a8-bb36-89f9f1a88fe57', 'el'),
	(55, '4972cde0-a100-40a8-bb36-89f9f1a88fe58', 'el'),
	(56, '4972cde0-a100-40a8-bb36-89f9f1a88fe59', 'el'),
	(57, '4972cde0-a100-40a8-bb36-89f9f1a88fe60', 'el'),
	(58, '4972cde0-a100-40a8-bb36-89f9f1a88fe61', 'el'),
	(59, '4972cde0-a100-40a8-bb36-89f9f1a88fe62', 'el'),
	(60, '4972cde0-a100-40a8-bb36-89f9f1a88fe63', 'el'),
	(61, '4972cde0-a100-40a8-bb36-89f9f1a88fe64', 'el'),
	(62, '4972cde0-a100-40a8-bb36-89f9f1a88fe65', 'el'),
	(63, '4972cde0-a100-40a8-bb36-89f9f1a88fe66', 'el'),
	(64, '4972cde0-a100-40a8-bb36-89f9f1a88fe67', 'el'),
	(65, '4972cde0-a100-40a8-bb36-89f9f1a88fe68', 'el'),
	(66, '4972cde0-a100-40a8-bb36-89f9f1a88fe69', 'el'),
	(67, '4972cde0-a100-40a8-bb36-89f9f1a88fe70', 'el'),
	(68, '4972cde0-a100-40a8-bb36-89f9f1a88fe71', 'el'),
	(69, '4972cde0-a100-40a8-bb36-89f9f1a88fe72', 'el'),
	(70, '4972cde0-a100-40a8-bb36-89f9f1a88fe73', 'el'),
	(71, '4972cde0-a100-40a8-bb36-89f9f1a88fe74', 'el'),
	(72, '4972cde0-a100-40a8-bb36-89f9f1a88fe75', 'el'),
	(73, '4972cde0-a100-40a8-bb36-89f9f1a88fe76', 'el'),
	(74, '4972cde0-a100-40a8-bb36-89f9f1a88fe77', 'el'),
	(75, '4972cde0-a100-40a8-bb36-89f9f1a88fe78', 'el'),
	(76, '4972cde0-a100-40a8-bb36-89f9f1a88fe79', 'el'),
	(77, '4972cde0-a100-40a8-bb36-89f9f1a88fe80', 'el'),
	(78, '4972cde0-a100-40a8-bb36-89f9f1a88fe81', 'el'),
	(79, '4972cde0-a100-40a8-bb36-89f9f1a88fe82', 'el'),
	(80, '4972cde0-a100-40a8-bb36-89f9f1a88fe83', 'el'),
	(81, '4972cde0-a100-40a8-bb36-89f9f1a88fe84', 'el'),
	(82, '4972cde0-a100-40a8-bb36-89f9f1a88fe85', 'el'),
	(83, '4972cde0-a100-40a8-bb36-89f9f1a88fe86', 'el'),
	(84, '4972cde0-a100-40a8-bb36-89f9f1a88fe87', 'el'),
	(85, '4972cde0-a100-40a8-bb36-89f9f1a88fe88', 'el'),
	(86, '4972cde0-a100-40a8-bb36-89f9f1a88fe92', 'el'),
	(87, '4972cde0-a100-40a8-bb36-89f9f1a88fe93', 'el');
/*!40000 ALTER TABLE `eepal_specialty` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
