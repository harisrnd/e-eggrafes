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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_admin_area
CREATE TABLE IF NOT EXISTS `eepal_admin_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eepal_admin_area_field__uuid__value` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_admin_area entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_admin_area: ~59 rows (approximately)
/*!40000 ALTER TABLE `eepal_admin_area` DISABLE KEYS */;
INSERT INTO `eepal_admin_area` (`id`, `uuid`, `langcode`) VALUES
	(1, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f9', 'el'),
	(2, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f10', 'el'),
	(3, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f11', 'el'),
	(4, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f12', 'el'),
	(5, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f13', 'el'),
	(6, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f14', 'el'),
	(7, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f15', 'el'),
	(8, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f16', 'el'),
	(9, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f17', 'el'),
	(10, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f18', 'el'),
	(11, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f19', 'el'),
	(12, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f20', 'el'),
	(13, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f21', 'el'),
	(14, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f22', 'el'),
	(15, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f23', 'el'),
	(16, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f24', 'el'),
	(17, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f25', 'el'),
	(18, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f26', 'el'),
	(19, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f27', 'el'),
	(20, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f28', 'el'),
	(21, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f29', 'el'),
	(22, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f30', 'el'),
	(23, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f31', 'el'),
	(24, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f32', 'el'),
	(25, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f33', 'el'),
	(26, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f34', 'el'),
	(27, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f35', 'el'),
	(28, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f36', 'el'),
	(29, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f37', 'el'),
	(30, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f38', 'el'),
	(31, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f39', 'el'),
	(32, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f40', 'el'),
	(33, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f41', 'el'),
	(34, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f42', 'el'),
	(35, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f43', 'el'),
	(36, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f44', 'el'),
	(37, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f45', 'el'),
	(38, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f46', 'el'),
	(39, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f47', 'el'),
	(40, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f48', 'el'),
	(41, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f49', 'el'),
	(42, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f50', 'el'),
	(43, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f51', 'el'),
	(44, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f52', 'el'),
	(45, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f53', 'el'),
	(46, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f54', 'el'),
	(47, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f55', 'el'),
	(48, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f56', 'el'),
	(49, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f57', 'el'),
	(50, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f58', 'el'),
	(51, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f59', 'el'),
	(52, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f60', 'el'),
	(53, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f61', 'el'),
	(54, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f62', 'el'),
	(55, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f63', 'el'),
	(56, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f64', 'el'),
	(57, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f65', 'el'),
	(58, '639a630f-0ee1-4ae6-af6f-764cfaa9c8f66', 'el'),
	(59, '1bf1af17-e244-42ef-96f0-4af701360162', 'el');
/*!40000 ALTER TABLE `eepal_admin_area` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
