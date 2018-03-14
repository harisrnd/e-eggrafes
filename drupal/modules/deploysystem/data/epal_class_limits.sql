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

-- Dumping structure for table e-epal_live_final_31_10_2017.epal_class_limits
CREATE TABLE IF NOT EXISTS `epal_class_limits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(80) DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `limit_down` int(11) DEFAULT NULL,
  `limit_up` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `epal_class_limits_field__uuid__value` (`uuid`),
  KEY `epal_class_limits_field__user_id__target_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COMMENT='The base table for epal_class_limits entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.epal_class_limits: ~48 rows (approximately)
/*!40000 ALTER TABLE `epal_class_limits` DISABLE KEYS */;
INSERT INTO `epal_class_limits` (`id`, `uuid`, `langcode`, `user_id`, `name`, `category`, `limit_down`, `limit_up`, `status`, `created`, `changed`) VALUES
	(1, 'b4842daf-844b-46a6-b3f5-b6439199bfd2', 'el', 1, '1', 'Α', 12, 25, 1, 1488279192, 1488279192),
	(2, 'b4842daf-844b-46a6-b3f5-b6439199bfd3', 'el', 1, '1', 'Β', 12, 25, 1, 1488279192, 1488279192),
	(3, 'b4842daf-844b-46a6-b3f5-b6439199bfd4', 'el', 1, '1', 'Γ', 12, 25, 1, 1488279192, 1488279192),
	(4, 'b4842daf-844b-46a6-b3f5-b6439199bfd5', 'el', 1, '1', 'Δ', 9, 25, 1, 1488279226, 1488279226),
	(5, 'b4842daf-844b-46a6-b3f5-b6439199bfd6', 'el', 1, '1', 'Ε', 9, 25, 1, 1488279226, 1488279226),
	(6, 'b4842daf-844b-46a6-b3f5-b6439199bfd7', 'el', 1, '1', 'ΣΤ', 9, 25, 1, 1488279226, 1488279226),
	(7, 'b4842daf-844b-46a6-b3f5-b6439199bfd8', 'el', 1, '1', 'Ζ', 9, 25, 1, 1488279226, 1488279226),
	(8, 'b4842daf-844b-46a6-b3f5-b6439199bfd9', 'el', 1, '1', 'Η', 8, 25, 1, 1488279253, 1488279253),
	(9, 'b4842daf-844b-46a6-b3f5-b6439199bfd10', 'el', 1, '1', 'Θ', 8, 25, 1, 1488279253, 1488279253),
	(10, 'b4842daf-844b-46a6-b3f5-b6439199bfd11', 'el', 1, '1', 'Ι', 8, 25, 1, 1488279253, 1488279253),
	(11, 'b4842daf-844b-46a6-b3f5-b6439199bfd12', 'el', 1, '1', 'ΙΑ', 8, 25, 1, 1488279253, 1488279253),
	(12, 'b4842daf-844b-46a6-b3f5-b6439199bfd13', 'el', 1, '1', 'ΙΒ', 8, 25, 1, 1488279253, 1488279253),
	(13, 'b4842daf-844b-46a6-b3f5-b6439199bfd14', 'el', 1, '3', 'Α', 8, 25, 1, 1488279443, 1488279443),
	(14, 'b4842daf-844b-46a6-b3f5-b6439199bfd15', 'el', 1, '3', 'Β', 8, 25, 1, 1488279443, 1488279443),
	(15, 'b4842daf-844b-46a6-b3f5-b6439199bfd16', 'el', 1, '3', 'Γ', 8, 25, 1, 1488279443, 1488279443),
	(16, 'b4842daf-844b-46a6-b3f5-b6439199bfd17', 'el', 1, '3', 'Δ', 6, 25, 1, 1488279465, 1488279465),
	(17, 'b4842daf-844b-46a6-b3f5-b6439199bfd18', 'el', 1, '3', 'Ε', 6, 25, 1, 1488279465, 1488279465),
	(18, 'b4842daf-844b-46a6-b3f5-b6439199bfd19', 'el', 1, '3', 'ΣΤ', 6, 25, 1, 1488279465, 1488279465),
	(19, 'b4842daf-844b-46a6-b3f5-b6439199bfd20', 'el', 1, '3', 'Ζ', 6, 25, 1, 1488279465, 1488279465),
	(20, 'b4842daf-844b-46a6-b3f5-b6439199bfd21', 'el', 1, '3', 'Η', 4, 25, 1, 1488279495, 1488279495),
	(21, 'b4842daf-844b-46a6-b3f5-b6439199bfd22', 'el', 1, '3', 'Θ', 4, 25, 1, 1488279495, 1488279495),
	(22, 'b4842daf-844b-46a6-b3f5-b6439199bfd23', 'el', 1, '3', 'Ι', 4, 25, 1, 1488279495, 1488279495),
	(23, 'b4842daf-844b-46a6-b3f5-b6439199bfd24', 'el', 1, '3', 'ΙΑ', 4, 25, 1, 1488279495, 1488279495),
	(24, 'b4842daf-844b-46a6-b3f5-b6439199bfd25', 'el', 1, '3', 'ΙΒ', 4, 25, 1, 1488279495, 1488279495),
	(25, 'b4842daf-844b-46a6-b3f5-b6439199bfd26', 'el', 1, '2', 'Α', 10, 25, 1, 1488279311, 1488279311),
	(26, 'b4842daf-844b-46a6-b3f5-b6439199bfd27', 'el', 1, '2', 'Β', 10, 25, 1, 1488279311, 1488279311),
	(27, 'b4842daf-844b-46a6-b3f5-b6439199bfd28', 'el', 1, '2', 'Γ', 10, 25, 1, 1488279311, 1488279311),
	(28, 'b4842daf-844b-46a6-b3f5-b6439199bfd29', 'el', 1, '2', 'Δ', 8, 25, 1, 1488279340, 1488279340),
	(29, 'b4842daf-844b-46a6-b3f5-b6439199bfd30', 'el', 1, '2', 'Ε', 8, 25, 1, 1488279340, 1488279340),
	(30, 'b4842daf-844b-46a6-b3f5-b6439199bfd31', 'el', 1, '2', 'ΣΤ', 8, 25, 1, 1488279340, 1488279340),
	(31, 'b4842daf-844b-46a6-b3f5-b6439199bfd32', 'el', 1, '2', 'Ζ', 8, 25, 1, 1488279340, 1488279340),
	(32, 'b4842daf-844b-46a6-b3f5-b6439199bfd33', 'el', 1, '2', 'Η', 6, 25, 1, 1488279367, 1488279367),
	(33, 'b4842daf-844b-46a6-b3f5-b6439199bfd34', 'el', 1, '2', 'Θ', 6, 25, 1, 1488279367, 1488279367),
	(34, 'b4842daf-844b-46a6-b3f5-b6439199bfd35', 'el', 1, '2', 'Ι', 6, 25, 1, 1488279367, 1488279367),
	(35, 'b4842daf-844b-46a6-b3f5-b6439199bfd36', 'el', 1, '2', 'ΙΑ', 6, 25, 1, 1488279367, 1488279367),
	(36, 'b4842daf-844b-46a6-b3f5-b6439199bfd37', 'el', 1, '2', 'ΙΒ', 6, 25, 1, 1488279367, 1488279367),
	(37, 'b4842daf-844b-46a6-b3f5-b6439199bfd38', 'el', 1, '4', 'Α', 8, 25, 1, 1488279443, 1488279443),
	(38, 'b4842daf-844b-46a6-b3f5-b6439199bfd39', 'el', 1, '4', 'Β', 8, 25, 1, 1488279443, 1488279443),
	(39, 'b4842daf-844b-46a6-b3f5-b6439199bfd40', 'el', 1, '4', 'Γ', 8, 25, 1, 1488279443, 1488279443),
	(40, 'b4842daf-844b-46a6-b3f5-b6439199bfd41', 'el', 1, '4', 'Δ', 6, 25, 1, 1488279465, 1488279465),
	(41, 'b4842daf-844b-46a6-b3f5-b6439199bfd42', 'el', 1, '4', 'Ε', 6, 25, 1, 1488279465, 1488279465),
	(42, 'b4842daf-844b-46a6-b3f5-b6439199bfd43', 'el', 1, '4', 'ΣΤ', 6, 25, 1, 1488279465, 1488279465),
	(43, 'b4842daf-844b-46a6-b3f5-b6439199bfd44', 'el', 1, '4', 'Ζ', 6, 25, 1, 1488279465, 1488279465),
	(44, 'b4842daf-844b-46a6-b3f5-b6439199bfd45', 'el', 1, '4', 'Η', 4, 25, 1, 1488279495, 1488279495),
	(45, 'b4842daf-844b-46a6-b3f5-b6439199bfd46', 'el', 1, '4', 'Θ', 4, 25, 1, 1488279495, 1488279495),
	(46, 'b4842daf-844b-46a6-b3f5-b6439199bfd47', 'el', 1, '4', 'Ι', 4, 25, 1, 1488279495, 1488279495),
	(47, 'b4842daf-844b-46a6-b3f5-b6439199bfd48', 'el', 1, '4', 'ΙΑ', 4, 25, 1, 1488279495, 1488279495),
	(48, 'b4842daf-844b-46a6-b3f5-b6439199bfd49', 'el', 1, '4', 'ΙΒ', 4, 25, 1, 1488279495, 1488279495);
/*!40000 ALTER TABLE `epal_class_limits` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;