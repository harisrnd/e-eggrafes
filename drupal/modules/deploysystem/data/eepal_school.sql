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

-- Dumping structure for table e-epal_live_final_31_10_2017.eepal_school
CREATE TABLE IF NOT EXISTS `eepal_school` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eepal_school_field__uuid__value` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf8mb4 COMMENT='The base table for eepal_school entities.';

-- Dumping data for table e-epal_live_final_31_10_2017.eepal_school: ~398 rows (approximately)
/*!40000 ALTER TABLE `eepal_school` DISABLE KEYS */;
INSERT INTO `eepal_school` (`id`, `uuid`, `langcode`) VALUES
	(1, 'f52ad308-915b-482d-a242-d81b13a04000', 'el'),
	(2, 'f52ad308-915b-482d-a242-d81b13a04001', 'el'),
	(3, 'f52ad308-915b-482d-a242-d81b13a04002', 'el'),
	(4, 'f52ad308-915b-482d-a242-d81b13a04003', 'el'),
	(5, 'f52ad308-915b-482d-a242-d81b13a04004', 'el'),
	(6, 'f52ad308-915b-482d-a242-d81b13a04005', 'el'),
	(7, 'f52ad308-915b-482d-a242-d81b13a04006', 'el'),
	(8, 'f52ad308-915b-482d-a242-d81b13a04007', 'el'),
	(9, 'f52ad308-915b-482d-a242-d81b13a04008', 'el'),
	(10, 'f52ad308-915b-482d-a242-d81b13a04009', 'el'),
	(11, 'f52ad308-915b-482d-a242-d81b13a04010', 'el'),
	(12, 'f52ad308-915b-482d-a242-d81b13a04011', 'el'),
	(13, 'f52ad308-915b-482d-a242-d81b13a04012', 'el'),
	(14, 'f52ad308-915b-482d-a242-d81b13a04013', 'el'),
	(15, 'f52ad308-915b-482d-a242-d81b13a04014', 'el'),
	(16, 'f52ad308-915b-482d-a242-d81b13a04015', 'el'),
	(17, 'f52ad308-915b-482d-a242-d81b13a04016', 'el'),
	(18, 'f52ad308-915b-482d-a242-d81b13a04017', 'el'),
	(19, 'f52ad308-915b-482d-a242-d81b13a04018', 'el'),
	(20, 'f52ad308-915b-482d-a242-d81b13a04019', 'el'),
	(21, 'f52ad308-915b-482d-a242-d81b13a04020', 'el'),
	(22, 'f52ad308-915b-482d-a242-d81b13a04021', 'el'),
	(23, 'f52ad308-915b-482d-a242-d81b13a04022', 'el'),
	(24, 'f52ad308-915b-482d-a242-d81b13a04023', 'el'),
	(25, 'f52ad308-915b-482d-a242-d81b13a04024', 'el'),
	(26, 'f52ad308-915b-482d-a242-d81b13a04025', 'el'),
	(27, 'f52ad308-915b-482d-a242-d81b13a04026', 'el'),
	(28, 'f52ad308-915b-482d-a242-d81b13a04027', 'el'),
	(29, 'f52ad308-915b-482d-a242-d81b13a04028', 'el'),
	(30, 'f52ad308-915b-482d-a242-d81b13a04029', 'el'),
	(31, 'f52ad308-915b-482d-a242-d81b13a04030', 'el'),
	(32, 'f52ad308-915b-482d-a242-d81b13a04031', 'el'),
	(33, 'f52ad308-915b-482d-a242-d81b13a04032', 'el'),
	(34, 'f52ad308-915b-482d-a242-d81b13a04033', 'el'),
	(35, 'f52ad308-915b-482d-a242-d81b13a04034', 'el'),
	(36, 'f52ad308-915b-482d-a242-d81b13a04035', 'el'),
	(37, 'f52ad308-915b-482d-a242-d81b13a04036', 'el'),
	(38, 'f52ad308-915b-482d-a242-d81b13a04037', 'el'),
	(39, 'f52ad308-915b-482d-a242-d81b13a04038', 'el'),
	(40, 'f52ad308-915b-482d-a242-d81b13a04039', 'el'),
	(41, 'f52ad308-915b-482d-a242-d81b13a04040', 'el'),
	(42, 'f52ad308-915b-482d-a242-d81b13a04041', 'el'),
	(43, 'f52ad308-915b-482d-a242-d81b13a04042', 'el'),
	(44, 'f52ad308-915b-482d-a242-d81b13a04043', 'el'),
	(45, 'f52ad308-915b-482d-a242-d81b13a04044', 'el'),
	(46, 'f52ad308-915b-482d-a242-d81b13a04045', 'el'),
	(47, 'f52ad308-915b-482d-a242-d81b13a04046', 'el'),
	(48, 'f52ad308-915b-482d-a242-d81b13a04047', 'el'),
	(49, 'f52ad308-915b-482d-a242-d81b13a04048', 'el'),
	(50, 'f52ad308-915b-482d-a242-d81b13a04049', 'el'),
	(51, 'f52ad308-915b-482d-a242-d81b13a04050', 'el'),
	(52, 'f52ad308-915b-482d-a242-d81b13a04051', 'el'),
	(53, 'f52ad308-915b-482d-a242-d81b13a04052', 'el'),
	(54, 'f52ad308-915b-482d-a242-d81b13a04053', 'el'),
	(55, 'f52ad308-915b-482d-a242-d81b13a04054', 'el'),
	(56, 'f52ad308-915b-482d-a242-d81b13a04055', 'el'),
	(57, 'f52ad308-915b-482d-a242-d81b13a04056', 'el'),
	(58, 'f52ad308-915b-482d-a242-d81b13a04057', 'el'),
	(60, 'f52ad308-915b-482d-a242-d81b13a04059', 'el'),
	(61, 'f52ad308-915b-482d-a242-d81b13a04060', 'el'),
	(62, 'f52ad308-915b-482d-a242-d81b13a04061', 'el'),
	(63, 'f52ad308-915b-482d-a242-d81b13a04062', 'el'),
	(64, 'f52ad308-915b-482d-a242-d81b13a04063', 'el'),
	(65, 'f52ad308-915b-482d-a242-d81b13a04064', 'el'),
	(66, 'f52ad308-915b-482d-a242-d81b13a04065', 'el'),
	(67, 'f52ad308-915b-482d-a242-d81b13a04066', 'el'),
	(68, 'f52ad308-915b-482d-a242-d81b13a04067', 'el'),
	(69, 'f52ad308-915b-482d-a242-d81b13a04068', 'el'),
	(70, 'f52ad308-915b-482d-a242-d81b13a04069', 'el'),
	(71, 'f52ad308-915b-482d-a242-d81b13a04070', 'el'),
	(72, 'f52ad308-915b-482d-a242-d81b13a04071', 'el'),
	(73, 'f52ad308-915b-482d-a242-d81b13a04072', 'el'),
	(74, 'f52ad308-915b-482d-a242-d81b13a04073', 'el'),
	(75, 'f52ad308-915b-482d-a242-d81b13a04074', 'el'),
	(76, 'f52ad308-915b-482d-a242-d81b13a04075', 'el'),
	(77, 'f52ad308-915b-482d-a242-d81b13a04076', 'el'),
	(78, 'f52ad308-915b-482d-a242-d81b13a04077', 'el'),
	(79, 'f52ad308-915b-482d-a242-d81b13a04078', 'el'),
	(80, 'f52ad308-915b-482d-a242-d81b13a04079', 'el'),
	(81, 'f52ad308-915b-482d-a242-d81b13a04080', 'el'),
	(82, 'f52ad308-915b-482d-a242-d81b13a04081', 'el'),
	(83, 'f52ad308-915b-482d-a242-d81b13a04082', 'el'),
	(84, 'f52ad308-915b-482d-a242-d81b13a04083', 'el'),
	(85, 'f52ad308-915b-482d-a242-d81b13a04084', 'el'),
	(86, 'f52ad308-915b-482d-a242-d81b13a04085', 'el'),
	(87, 'f52ad308-915b-482d-a242-d81b13a04086', 'el'),
	(88, 'f52ad308-915b-482d-a242-d81b13a04087', 'el'),
	(89, 'f52ad308-915b-482d-a242-d81b13a04088', 'el'),
	(90, 'f52ad308-915b-482d-a242-d81b13a04089', 'el'),
	(91, 'f52ad308-915b-482d-a242-d81b13a04090', 'el'),
	(92, 'f52ad308-915b-482d-a242-d81b13a04091', 'el'),
	(93, 'f52ad308-915b-482d-a242-d81b13a04092', 'el'),
	(94, 'f52ad308-915b-482d-a242-d81b13a04093', 'el'),
	(95, 'f52ad308-915b-482d-a242-d81b13a04094', 'el'),
	(96, 'f52ad308-915b-482d-a242-d81b13a04095', 'el'),
	(97, 'f52ad308-915b-482d-a242-d81b13a04096', 'el'),
	(98, 'f52ad308-915b-482d-a242-d81b13a04097', 'el'),
	(99, 'f52ad308-915b-482d-a242-d81b13a04098', 'el'),
	(100, 'f52ad308-915b-482d-a242-d81b13a04099', 'el'),
	(101, 'f52ad308-915b-482d-a242-d81b13a04100', 'el'),
	(102, 'f52ad308-915b-482d-a242-d81b13a04101', 'el'),
	(103, 'f52ad308-915b-482d-a242-d81b13a04102', 'el'),
	(104, 'f52ad308-915b-482d-a242-d81b13a04103', 'el'),
	(105, 'f52ad308-915b-482d-a242-d81b13a04104', 'el'),
	(106, 'f52ad308-915b-482d-a242-d81b13a04105', 'el'),
	(107, 'f52ad308-915b-482d-a242-d81b13a04106', 'el'),
	(108, 'f52ad308-915b-482d-a242-d81b13a04107', 'el'),
	(109, 'f52ad308-915b-482d-a242-d81b13a04108', 'el'),
	(110, 'f52ad308-915b-482d-a242-d81b13a04109', 'el'),
	(111, 'f52ad308-915b-482d-a242-d81b13a04110', 'el'),
	(112, 'f52ad308-915b-482d-a242-d81b13a04111', 'el'),
	(113, 'f52ad308-915b-482d-a242-d81b13a04112', 'el'),
	(114, 'f52ad308-915b-482d-a242-d81b13a04113', 'el'),
	(115, 'f52ad308-915b-482d-a242-d81b13a04114', 'el'),
	(116, 'f52ad308-915b-482d-a242-d81b13a04115', 'el'),
	(117, 'f52ad308-915b-482d-a242-d81b13a04116', 'el'),
	(118, 'f52ad308-915b-482d-a242-d81b13a04117', 'el'),
	(119, 'f52ad308-915b-482d-a242-d81b13a04118', 'el'),
	(120, 'f52ad308-915b-482d-a242-d81b13a04119', 'el'),
	(121, 'f52ad308-915b-482d-a242-d81b13a04120', 'el'),
	(122, 'f52ad308-915b-482d-a242-d81b13a04121', 'el'),
	(123, 'f52ad308-915b-482d-a242-d81b13a04122', 'el'),
	(124, 'f52ad308-915b-482d-a242-d81b13a04123', 'el'),
	(125, 'f52ad308-915b-482d-a242-d81b13a04124', 'el'),
	(126, 'f52ad308-915b-482d-a242-d81b13a04125', 'el'),
	(127, 'f52ad308-915b-482d-a242-d81b13a04126', 'el'),
	(128, 'f52ad308-915b-482d-a242-d81b13a04127', 'el'),
	(129, 'f52ad308-915b-482d-a242-d81b13a04128', 'el'),
	(130, 'f52ad308-915b-482d-a242-d81b13a04129', 'el'),
	(131, 'f52ad308-915b-482d-a242-d81b13a04130', 'el'),
	(132, 'f52ad308-915b-482d-a242-d81b13a04131', 'el'),
	(133, 'f52ad308-915b-482d-a242-d81b13a04132', 'el'),
	(134, 'f52ad308-915b-482d-a242-d81b13a04133', 'el'),
	(135, 'f52ad308-915b-482d-a242-d81b13a04134', 'el'),
	(136, 'f52ad308-915b-482d-a242-d81b13a04135', 'el'),
	(137, 'f52ad308-915b-482d-a242-d81b13a04136', 'el'),
	(138, 'f52ad308-915b-482d-a242-d81b13a04137', 'el'),
	(139, 'f52ad308-915b-482d-a242-d81b13a04138', 'el'),
	(140, 'f52ad308-915b-482d-a242-d81b13a04139', 'el'),
	(141, 'f52ad308-915b-482d-a242-d81b13a04140', 'el'),
	(142, 'f52ad308-915b-482d-a242-d81b13a04141', 'el'),
	(143, 'f52ad308-915b-482d-a242-d81b13a04142', 'el'),
	(144, 'f52ad308-915b-482d-a242-d81b13a04143', 'el'),
	(145, 'f52ad308-915b-482d-a242-d81b13a04144', 'el'),
	(146, 'f52ad308-915b-482d-a242-d81b13a04145', 'el'),
	(147, 'f52ad308-915b-482d-a242-d81b13a04146', 'el'),
	(148, 'f52ad308-915b-482d-a242-d81b13a04147', 'el'),
	(149, 'f52ad308-915b-482d-a242-d81b13a04148', 'el'),
	(150, 'f52ad308-915b-482d-a242-d81b13a04149', 'el'),
	(151, 'f52ad308-915b-482d-a242-d81b13a04150', 'el'),
	(152, 'f52ad308-915b-482d-a242-d81b13a04151', 'el'),
	(153, 'f52ad308-915b-482d-a242-d81b13a04152', 'el'),
	(154, 'f52ad308-915b-482d-a242-d81b13a04153', 'el'),
	(155, 'f52ad308-915b-482d-a242-d81b13a04154', 'el'),
	(156, 'f52ad308-915b-482d-a242-d81b13a04155', 'el'),
	(157, 'f52ad308-915b-482d-a242-d81b13a04156', 'el'),
	(158, 'f52ad308-915b-482d-a242-d81b13a04157', 'el'),
	(159, 'f52ad308-915b-482d-a242-d81b13a04158', 'el'),
	(160, 'f52ad308-915b-482d-a242-d81b13a04159', 'el'),
	(161, 'f52ad308-915b-482d-a242-d81b13a04160', 'el'),
	(162, 'f52ad308-915b-482d-a242-d81b13a04161', 'el'),
	(163, 'f52ad308-915b-482d-a242-d81b13a04162', 'el'),
	(164, 'f52ad308-915b-482d-a242-d81b13a04163', 'el'),
	(165, 'f52ad308-915b-482d-a242-d81b13a04164', 'el'),
	(166, 'f52ad308-915b-482d-a242-d81b13a04165', 'el'),
	(167, 'f52ad308-915b-482d-a242-d81b13a04166', 'el'),
	(168, 'f52ad308-915b-482d-a242-d81b13a04167', 'el'),
	(169, 'f52ad308-915b-482d-a242-d81b13a04168', 'el'),
	(170, 'f52ad308-915b-482d-a242-d81b13a04169', 'el'),
	(171, 'f52ad308-915b-482d-a242-d81b13a04170', 'el'),
	(172, 'f52ad308-915b-482d-a242-d81b13a04171', 'el'),
	(173, 'f52ad308-915b-482d-a242-d81b13a04172', 'el'),
	(174, 'f52ad308-915b-482d-a242-d81b13a04173', 'el'),
	(175, 'f52ad308-915b-482d-a242-d81b13a04174', 'el'),
	(176, 'f52ad308-915b-482d-a242-d81b13a04175', 'el'),
	(177, 'f52ad308-915b-482d-a242-d81b13a04176', 'el'),
	(178, 'f52ad308-915b-482d-a242-d81b13a04177', 'el'),
	(179, 'f52ad308-915b-482d-a242-d81b13a04178', 'el'),
	(180, 'f52ad308-915b-482d-a242-d81b13a04179', 'el'),
	(181, 'f52ad308-915b-482d-a242-d81b13a04180', 'el'),
	(182, 'f52ad308-915b-482d-a242-d81b13a04181', 'el'),
	(183, 'f52ad308-915b-482d-a242-d81b13a04182', 'el'),
	(184, 'f52ad308-915b-482d-a242-d81b13a04183', 'el'),
	(185, 'f52ad308-915b-482d-a242-d81b13a04184', 'el'),
	(186, 'f52ad308-915b-482d-a242-d81b13a04185', 'el'),
	(187, 'f52ad308-915b-482d-a242-d81b13a04186', 'el'),
	(188, 'f52ad308-915b-482d-a242-d81b13a04187', 'el'),
	(189, 'f52ad308-915b-482d-a242-d81b13a04188', 'el'),
	(190, 'f52ad308-915b-482d-a242-d81b13a04189', 'el'),
	(191, 'f52ad308-915b-482d-a242-d81b13a04190', 'el'),
	(192, 'f52ad308-915b-482d-a242-d81b13a04191', 'el'),
	(193, 'f52ad308-915b-482d-a242-d81b13a04192', 'el'),
	(194, 'f52ad308-915b-482d-a242-d81b13a04193', 'el'),
	(195, 'f52ad308-915b-482d-a242-d81b13a04194', 'el'),
	(196, 'f52ad308-915b-482d-a242-d81b13a04195', 'el'),
	(197, 'f52ad308-915b-482d-a242-d81b13a04196', 'el'),
	(198, 'f52ad308-915b-482d-a242-d81b13a04197', 'el'),
	(199, 'f52ad308-915b-482d-a242-d81b13a04198', 'el'),
	(200, 'f52ad308-915b-482d-a242-d81b13a04199', 'el'),
	(201, 'f52ad308-915b-482d-a242-d81b13a04200', 'el'),
	(202, 'f52ad308-915b-482d-a242-d81b13a04201', 'el'),
	(203, 'f52ad308-915b-482d-a242-d81b13a04202', 'el'),
	(204, 'f52ad308-915b-482d-a242-d81b13a04203', 'el'),
	(205, 'f52ad308-915b-482d-a242-d81b13a04204', 'el'),
	(206, 'f52ad308-915b-482d-a242-d81b13a04205', 'el'),
	(207, 'f52ad308-915b-482d-a242-d81b13a04206', 'el'),
	(208, 'f52ad308-915b-482d-a242-d81b13a04207', 'el'),
	(209, 'f52ad308-915b-482d-a242-d81b13a04208', 'el'),
	(210, 'f52ad308-915b-482d-a242-d81b13a04209', 'el'),
	(211, 'f52ad308-915b-482d-a242-d81b13a04210', 'el'),
	(212, 'f52ad308-915b-482d-a242-d81b13a04211', 'el'),
	(213, 'f52ad308-915b-482d-a242-d81b13a04212', 'el'),
	(214, 'f52ad308-915b-482d-a242-d81b13a04213', 'el'),
	(215, 'f52ad308-915b-482d-a242-d81b13a04214', 'el'),
	(216, 'f52ad308-915b-482d-a242-d81b13a04215', 'el'),
	(217, 'f52ad308-915b-482d-a242-d81b13a04216', 'el'),
	(218, 'f52ad308-915b-482d-a242-d81b13a04217', 'el'),
	(219, 'f52ad308-915b-482d-a242-d81b13a04218', 'el'),
	(220, 'f52ad308-915b-482d-a242-d81b13a04219', 'el'),
	(221, 'f52ad308-915b-482d-a242-d81b13a04220', 'el'),
	(222, 'f52ad308-915b-482d-a242-d81b13a04221', 'el'),
	(223, 'f52ad308-915b-482d-a242-d81b13a04222', 'el'),
	(224, 'f52ad308-915b-482d-a242-d81b13a04223', 'el'),
	(225, 'f52ad308-915b-482d-a242-d81b13a04224', 'el'),
	(226, 'f52ad308-915b-482d-a242-d81b13a04225', 'el'),
	(227, 'f52ad308-915b-482d-a242-d81b13a04226', 'el'),
	(228, 'f52ad308-915b-482d-a242-d81b13a04227', 'el'),
	(229, 'f52ad308-915b-482d-a242-d81b13a04228', 'el'),
	(230, 'f52ad308-915b-482d-a242-d81b13a04229', 'el'),
	(231, 'f52ad308-915b-482d-a242-d81b13a04230', 'el'),
	(232, 'f52ad308-915b-482d-a242-d81b13a04231', 'el'),
	(233, 'f52ad308-915b-482d-a242-d81b13a04232', 'el'),
	(234, 'f52ad308-915b-482d-a242-d81b13a04233', 'el'),
	(235, 'f52ad308-915b-482d-a242-d81b13a04234', 'el'),
	(236, 'f52ad308-915b-482d-a242-d81b13a04235', 'el'),
	(237, 'f52ad308-915b-482d-a242-d81b13a04236', 'el'),
	(238, 'f52ad308-915b-482d-a242-d81b13a04237', 'el'),
	(239, 'f52ad308-915b-482d-a242-d81b13a04238', 'el'),
	(240, 'f52ad308-915b-482d-a242-d81b13a04239', 'el'),
	(242, 'f52ad308-915b-482d-a242-d81b13a04241', 'el'),
	(243, 'f52ad308-915b-482d-a242-d81b13a04242', 'el'),
	(244, 'f52ad308-915b-482d-a242-d81b13a04243', 'el'),
	(245, 'f52ad308-915b-482d-a242-d81b13a04244', 'el'),
	(246, 'f52ad308-915b-482d-a242-d81b13a04245', 'el'),
	(247, 'f52ad308-915b-482d-a242-d81b13a04246', 'el'),
	(248, 'f52ad308-915b-482d-a242-d81b13a04247', 'el'),
	(249, 'f52ad308-915b-482d-a242-d81b13a04248', 'el'),
	(250, 'f52ad308-915b-482d-a242-d81b13a04249', 'el'),
	(251, 'f52ad308-915b-482d-a242-d81b13a04250', 'el'),
	(252, 'f52ad308-915b-482d-a242-d81b13a04251', 'el'),
	(253, 'f52ad308-915b-482d-a242-d81b13a04252', 'el'),
	(254, 'f52ad308-915b-482d-a242-d81b13a04253', 'el'),
	(255, 'f52ad308-915b-482d-a242-d81b13a04254', 'el'),
	(256, 'f52ad308-915b-482d-a242-d81b13a04255', 'el'),
	(257, 'f52ad308-915b-482d-a242-d81b13a04256', 'el'),
	(258, 'f52ad308-915b-482d-a242-d81b13a04257', 'el'),
	(259, 'f52ad308-915b-482d-a242-d81b13a04258', 'el'),
	(260, 'f52ad308-915b-482d-a242-d81b13a04259', 'el'),
	(261, 'f52ad308-915b-482d-a242-d81b13a04260', 'el'),
	(262, 'f52ad308-915b-482d-a242-d81b13a04261', 'el'),
	(263, 'f52ad308-915b-482d-a242-d81b13a04262', 'el'),
	(264, 'f52ad308-915b-482d-a242-d81b13a04263', 'el'),
	(265, 'f52ad308-915b-482d-a242-d81b13a04264', 'el'),
	(266, 'f52ad308-915b-482d-a242-d81b13a04265', 'el'),
	(267, 'f52ad308-915b-482d-a242-d81b13a04266', 'el'),
	(268, 'f52ad308-915b-482d-a242-d81b13a04267', 'el'),
	(269, 'f52ad308-915b-482d-a242-d81b13a04268', 'el'),
	(270, 'f52ad308-915b-482d-a242-d81b13a04269', 'el'),
	(271, 'f52ad308-915b-482d-a242-d81b13a04270', 'el'),
	(272, 'f52ad308-915b-482d-a242-d81b13a04271', 'el'),
	(273, 'f52ad308-915b-482d-a242-d81b13a04272', 'el'),
	(274, 'f52ad308-915b-482d-a242-d81b13a04273', 'el'),
	(275, 'f52ad308-915b-482d-a242-d81b13a04274', 'el'),
	(276, 'f52ad308-915b-482d-a242-d81b13a04275', 'el'),
	(277, 'f52ad308-915b-482d-a242-d81b13a04276', 'el'),
	(278, 'f52ad308-915b-482d-a242-d81b13a04277', 'el'),
	(279, 'f52ad308-915b-482d-a242-d81b13a04278', 'el'),
	(280, 'f52ad308-915b-482d-a242-d81b13a04279', 'el'),
	(281, 'f52ad308-915b-482d-a242-d81b13a04280', 'el'),
	(282, 'f52ad308-915b-482d-a242-d81b13a04281', 'el'),
	(283, 'f52ad308-915b-482d-a242-d81b13a04282', 'el'),
	(284, 'f52ad308-915b-482d-a242-d81b13a04283', 'el'),
	(285, 'f52ad308-915b-482d-a242-d81b13a04284', 'el'),
	(286, 'f52ad308-915b-482d-a242-d81b13a04285', 'el'),
	(287, 'f52ad308-915b-482d-a242-d81b13a04286', 'el'),
	(288, 'f52ad308-915b-482d-a242-d81b13a04287', 'el'),
	(289, 'f52ad308-915b-482d-a242-d81b13a04288', 'el'),
	(290, 'f52ad308-915b-482d-a242-d81b13a04289', 'el'),
	(291, 'f52ad308-915b-482d-a242-d81b13a04290', 'el'),
	(292, 'f52ad308-915b-482d-a242-d81b13a04291', 'el'),
	(293, 'f52ad308-915b-482d-a242-d81b13a04292', 'el'),
	(294, 'f52ad308-915b-482d-a242-d81b13a04293', 'el'),
	(295, 'f52ad308-915b-482d-a242-d81b13a04294', 'el'),
	(296, 'f52ad308-915b-482d-a242-d81b13a04295', 'el'),
	(297, 'f52ad308-915b-482d-a242-d81b13a04296', 'el'),
	(298, 'f52ad308-915b-482d-a242-d81b13a04297', 'el'),
	(299, 'f52ad308-915b-482d-a242-d81b13a04298', 'el'),
	(300, 'f52ad308-915b-482d-a242-d81b13a04299', 'el'),
	(301, 'f52ad308-915b-482d-a242-d81b13a04300', 'el'),
	(302, 'f52ad308-915b-482d-a242-d81b13a04301', 'el'),
	(303, 'f52ad308-915b-482d-a242-d81b13a04302', 'el'),
	(304, 'f52ad308-915b-482d-a242-d81b13a04303', 'el'),
	(305, 'f52ad308-915b-482d-a242-d81b13a04304', 'el'),
	(306, 'f52ad308-915b-482d-a242-d81b13a04305', 'el'),
	(307, 'f52ad308-915b-482d-a242-d81b13a04306', 'el'),
	(308, 'f52ad308-915b-482d-a242-d81b13a04307', 'el'),
	(309, 'f52ad308-915b-482d-a242-d81b13a04308', 'el'),
	(310, 'f52ad308-915b-482d-a242-d81b13a04309', 'el'),
	(311, 'f52ad308-915b-482d-a242-d81b13a04310', 'el'),
	(312, 'f52ad308-915b-482d-a242-d81b13a04311', 'el'),
	(313, 'f52ad308-915b-482d-a242-d81b13a04312', 'el'),
	(314, 'f52ad308-915b-482d-a242-d81b13a04313', 'el'),
	(315, 'f52ad308-915b-482d-a242-d81b13a04314', 'el'),
	(316, 'f52ad308-915b-482d-a242-d81b13a04315', 'el'),
	(317, 'f52ad308-915b-482d-a242-d81b13a04316', 'el'),
	(318, 'f52ad308-915b-482d-a242-d81b13a04317', 'el'),
	(319, 'f52ad308-915b-482d-a242-d81b13a04318', 'el'),
	(320, 'f52ad308-915b-482d-a242-d81b13a04319', 'el'),
	(321, 'f52ad308-915b-482d-a242-d81b13a04320', 'el'),
	(322, 'f52ad308-915b-482d-a242-d81b13a04321', 'el'),
	(323, 'f52ad308-915b-482d-a242-d81b13a04322', 'el'),
	(324, 'f52ad308-915b-482d-a242-d81b13a04323', 'el'),
	(325, 'f52ad308-915b-482d-a242-d81b13a04324', 'el'),
	(326, 'f52ad308-915b-482d-a242-d81b13a04325', 'el'),
	(328, 'f52ad308-915b-482d-a242-d81b13a04327', 'el'),
	(329, 'f52ad308-915b-482d-a242-d81b13a04328', 'el'),
	(330, 'f52ad308-915b-482d-a242-d81b13a04329', 'el'),
	(331, 'f52ad308-915b-482d-a242-d81b13a04330', 'el'),
	(332, 'f52ad308-915b-482d-a242-d81b13a04331', 'el'),
	(333, 'f52ad308-915b-482d-a242-d81b13a04332', 'el'),
	(334, 'f52ad308-915b-482d-a242-d81b13a04333', 'el'),
	(335, 'f52ad308-915b-482d-a242-d81b13a04334', 'el'),
	(336, 'f52ad308-915b-482d-a242-d81b13a04335', 'el'),
	(337, 'f52ad308-915b-482d-a242-d81b13a04336', 'el'),
	(338, 'f52ad308-915b-482d-a242-d81b13a04337', 'el'),
	(339, 'f52ad308-915b-482d-a242-d81b13a04338', 'el'),
	(340, 'f52ad308-915b-482d-a242-d81b13a04339', 'el'),
	(341, 'f52ad308-915b-482d-a242-d81b13a04340', 'el'),
	(342, 'f52ad308-915b-482d-a242-d81b13a04341', 'el'),
	(343, 'f52ad308-915b-482d-a242-d81b13a04342', 'el'),
	(344, 'f52ad308-915b-482d-a242-d81b13a04343', 'el'),
	(345, 'f52ad308-915b-482d-a242-d81b13a04344', 'el'),
	(346, 'f52ad308-915b-482d-a242-d81b13a04345', 'el'),
	(347, 'f52ad308-915b-482d-a242-d81b13a04346', 'el'),
	(348, 'f52ad308-915b-482d-a242-d81b13a04347', 'el'),
	(349, 'f52ad308-915b-482d-a242-d81b13a04348', 'el'),
	(350, 'f52ad308-915b-482d-a242-d81b13a04349', 'el'),
	(351, 'f52ad308-915b-482d-a242-d81b13a04350', 'el'),
	(352, 'f52ad308-915b-482d-a242-d81b13a04351', 'el'),
	(353, 'f52ad308-915b-482d-a242-d81b13a04352', 'el'),
	(354, 'f52ad308-915b-482d-a242-d81b13a04353', 'el'),
	(355, 'f52ad308-915b-482d-a242-d81b13a04354', 'el'),
	(356, 'f52ad308-915b-482d-a242-d81b13a04355', 'el'),
	(357, 'f52ad308-915b-482d-a242-d81b13a04356', 'el'),
	(358, 'f52ad308-915b-482d-a242-d81b13a04357', 'el'),
	(359, 'f52ad308-915b-482d-a242-d81b13a04358', 'el'),
	(360, 'f52ad308-915b-482d-a242-d81b13a04359', 'el'),
	(361, 'f52ad308-915b-482d-a242-d81b13a04360', 'el'),
	(362, 'f52ad308-915b-482d-a242-d81b13a04361', 'el'),
	(363, 'f52ad308-915b-482d-a242-d81b13a04362', 'el'),
	(364, 'f52ad308-915b-482d-a242-d81b13a04363', 'el'),
	(365, 'f52ad308-915b-482d-a242-d81b13a04364', 'el'),
	(366, 'f52ad308-915b-482d-a242-d81b13a04365', 'el'),
	(367, 'f52ad308-915b-482d-a242-d81b13a04366', 'el'),
	(368, 'f52ad308-915b-482d-a242-d81b13a04367', 'el'),
	(369, 'f52ad308-915b-482d-a242-d81b13a04368', 'el'),
	(370, 'f52ad308-915b-482d-a242-d81b13a04369', 'el'),
	(371, 'f52ad308-915b-482d-a242-d81b13a04370', 'el'),
	(372, 'f52ad308-915b-482d-a242-d81b13a04371', 'el'),
	(373, 'f52ad308-915b-482d-a242-d81b13a04372', 'el'),
	(374, 'f52ad308-915b-482d-a242-d81b13a04373', 'el'),
	(375, 'f52ad308-915b-482d-a242-d81b13a04374', 'el'),
	(376, 'f52ad308-915b-482d-a242-d81b13a04375', 'el'),
	(377, 'f52ad308-915b-482d-a242-d81b13a04376', 'el'),
	(378, 'f52ad308-915b-482d-a242-d81b13a04377', 'el'),
	(379, 'f52ad308-915b-482d-a242-d81b13a04378', 'el'),
	(380, 'f52ad308-915b-482d-a242-d81b13a04379', 'el'),
	(381, 'f52ad308-915b-482d-a242-d81b13a04380', 'el'),
	(382, 'f52ad308-915b-482d-a242-d81b13a04381', 'el'),
	(383, 'f52ad308-915b-482d-a242-d81b13a04382', 'el'),
	(384, 'f52ad308-915b-482d-a242-d81b13a04383', 'el'),
	(385, 'f52ad308-915b-482d-a242-d81b13a04384', 'el'),
	(386, 'f52ad308-915b-482d-a242-d81b13a04385', 'el'),
	(387, 'f52ad308-915b-482d-a242-d81b13a04386', 'el'),
	(388, 'f52ad308-915b-482d-a242-d81b13a04387', 'el'),
	(389, 'f52ad308-915b-482d-a242-d81b13a04388', 'el'),
	(390, 'f52ad308-915b-482d-a242-d81b13a04389', 'el'),
	(391, 'f52ad308-915b-482d-a242-d81b13a04390', 'el'),
	(392, 'f52ad308-915b-482d-a242-d81b13a04391', 'el'),
	(393, 'f52ad308-915b-482d-a242-d81b13a04392', 'el'),
	(394, 'f52ad308-915b-482d-a242-d81b13a04393', 'el'),
	(395, 'f52ad308-915b-482d-a242-d81b13a04394', 'el'),
	(396, 'f52ad308-915b-482d-a242-d81b13a04395', 'el'),
	(397, 'f52ad308-915b-482d-a242-d81b13a04396', 'el'),
	(398, 'f52ad308-915b-482d-a242-d81b13a04397', 'el'),
	(399, 'f52ad308-915b-482d-a242-d81b13a04398', 'el'),
	(400, 'f52ad308-915b-482d-a242-d81b13a04399', 'el'),
	(401, 'f52ad308-915b-482d-a242-d81b13a04400', 'el');
/*!40000 ALTER TABLE `eepal_school` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;