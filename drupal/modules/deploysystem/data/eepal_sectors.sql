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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_sectors
CREATE TABLE IF NOT EXISTS `eepal_sectors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eepal_sectors_field__uuid__value` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_sectors entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_sectors: ~9 rows (approximately)
/*!40000 ALTER TABLE `eepal_sectors` DISABLE KEYS */;
INSERT INTO `eepal_sectors` (`id`, `uuid`, `langcode`) VALUES
	(1, '6814e3d4-97c4-4180-b197-f28a2a137c42', 'el'),
	(2, 'f9e68d7e-0364-4131-8240-997e121c0143', 'el'),
	(3, '36cf7396-e75b-4100-8ebf-d4f7f98184c7', 'el'),
	(4, 'a5015fc0-ba9f-4f55-a0f0-1a01631aec0e', 'el'),
	(5, '39f91d7b-dc02-410d-9674-43347c8177d3', 'el'),
	(6, 'f15fee66-b049-473e-9c84-309a65e4c2d7', 'el'),
	(7, '0e5d63ac-b1e9-41f6-a5e2-5194860eab5e', 'el'),
	(8, 'c35ace9e-0609-4a95-8599-9645236167c8', 'el'),
	(9, '27e406ca-d465-4876-932a-b0ffface3488', 'el');
/*!40000 ALTER TABLE `eepal_sectors` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
