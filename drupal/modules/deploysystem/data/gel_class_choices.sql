-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 17 Ιουλ 2018 στις 11:25:36
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
-- Δομή πίνακα για τον πίνακα `gel_class_choices`
--

CREATE TABLE `gel_class_choices` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `name` varchar(50) DEFAULT NULL,
  `class_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `choice_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `status` tinyint(4) NOT NULL,
  `created` int(11) DEFAULT NULL,
  `changed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='The base table for gel_class_choices entities.';

--
-- Άδειασμα δεδομένων του πίνακα `gel_class_choices`
--

INSERT INTO `gel_class_choices` (`id`, `uuid`, `langcode`, `user_id`, `name`, `class_id`, `choice_id`, `status`, `created`, `changed`) VALUES
(1, 'e6571599-9f05-4f55-bcfc-ecc3ad9f0602', 'el', 1, 'record1', 1, 1, 1, 1515490432, 1515490432),
(2, '014e6cd2-6e09-4a7a-b225-aa40c607fdc7', 'el', 1, 'record2', 1, 2, 1, 1515490508, 1515490508),
(3, 'd3c8a41f-7cd7-45cb-b49f-a0bd167300fa', 'el', 1, 'record3', 1, 3, 1, 1515493140, 1515493140),
(4, '59f4bc51-d98f-419e-8f04-a5642a0c887d', 'el', 1, 'record4', 4, 1, 1, 1515493276, 1515493276),
(5, '1287331a-bf82-4bbe-b5dd-c7184eae2ead', 'el', 1, 'record5', 4, 2, 1, 1515493304, 1515493304),
(6, 'b453971f-8f96-497f-ba25-eb3a8fcf92d9', 'el', 1, 'record6', 4, 3, 1, 1515493323, 1515493323),
(7, 'ce5d5cfb-f440-4378-9556-bbbb7b4ae5c5', 'el', 1, 'record7', 2, 15, 1, 1515493417, 1515493417),
(8, '536f17d2-6949-4bf8-88a0-3404d1d943c9', 'el', 1, 'record8', 2, 16, 1, 1515493444, 1515493444),
(9, 'ceed9583-5b02-4ac1-91c2-96e0a20ef022', 'el', 1, 'record9', 6, 15, 1, 1515493503, 1515493503),
(10, '37cc1110-5fbf-4a81-943e-4a665dd74b32', 'el', 1, 'record10', 6, 16, 1, 1515493533, 1515493533),
(11, 'bf8107cd-c961-48bc-98c0-b80ef6f10c70', 'el', 1, 'record11', 1, 4, 1, 1515493628, 1515493682),
(12, 'bd42bb8a-b029-46e8-9a69-3a7fbc52b277', 'el', 1, 'record12', 1, 5, 1, 1515493658, 1515493693),
(13, 'd756fe47-6956-40a8-bc5b-886216f98921', 'el', 1, 'record13', 1, 6, 1, 1515493714, 1515493714),
(14, '059d0314-21c7-4346-98a6-40a9359c2eff', 'el', 1, 'record14', 1, 7, 1, 1515493731, 1515493731),
(15, '4f3a1bb6-b08e-4788-a239-27fdaa74d0a6', 'el', 1, 'record15', 4, 4, 1, 1515493799, 1515493799),
(16, '2bf4ddb2-5644-462b-b909-4c302be84651', 'el', 1, 'record16', 4, 5, 1, 1515493830, 1515493830),
(17, 'c537b823-1aae-49f9-9da0-c6b48daee1b2', 'el', 1, 'record17', 4, 6, 1, 1515493850, 1515493850),
(18, '67dc2be3-84d9-4fbe-9d1c-2c91a8bdde65', 'el', 1, 'record18', 4, 7, 1, 1515493870, 1515493870),
(19, '55350095-d797-42f9-88fd-cd06564a6105', 'el', 1, 'record19', 3, 15, 1, 1515494423, 1515494423),
(20, 'd7293161-4b72-4c57-8559-4fb55ba583a6', 'el', 1, 'record20', 3, 16, 1, 1515494443, 1515494443),
(21, '878a4faf-140a-4da4-bf53-913b0e6fe03c', 'el', 1, 'record21', 3, 17, 1, 1515494463, 1515494463),
(22, '0ea3788f-e1b9-4b15-b451-154d0db42091', 'el', 1, 'record22', 7, 15, 1, 1515494482, 1515494482),
(23, '02f228df-c198-4e60-a9a1-edbccf8422cb', 'el', 1, 'record23', 7, 16, 1, 1515494500, 1515494663),
(24, 'dc605b95-2c33-437f-bf61-3a7bd7806bc4', 'el', 1, 'record24', 7, 17, 1, 1515494573, 1515494676),
(25, 'e6a09b2f-b3d6-4f92-939c-8dfe9973ea67', 'el', 1, 'record25', 3, 8, 1, 1515494715, 1515494715),
(26, '0cf890d7-36ea-4ae2-b8da-98fcf991a600', 'el', 1, 'record26', 3, 9, 1, 1515494737, 1515494737),
(27, 'e55abcf8-6e7c-4750-9a51-ca8f2797f65d', 'el', 1, 'record27', 3, 10, 1, 1515494757, 1515494757),
(28, 'cdc4a5c3-76a7-42d9-98bb-9100c845d827', 'el', 1, 'record28', 3, 11, 1, 1515494793, 1515494793),
(29, '8af1d0d1-c863-4045-8841-554ed310e7ad', 'el', 1, 'record29', 3, 12, 1, 1515494811, 1515494811),
(30, 'fc604bd2-c237-45ad-88b2-8c85d255c566', 'el', 1, 'record30', 3, 13, 1, 1515494836, 1515494836),
(31, 'a58d7b17-277f-4b1e-8f21-c725fc326357', 'el', 1, 'record31', 3, 14, 1, 1515494856, 1515494856),
(32, 'd8e88698-0ab7-4313-93d3-3edeb307e53e', 'el', 1, 'record32', 1, 18, 1, 1525262413, 1525262413),
(33, 'bb19ae09-6467-4586-9c92-a9f34581e7e2', 'el', 1, 'record33', 4, 18, 1, 1525262517, 1525262517);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `gel_class_choices`
--
ALTER TABLE `gel_class_choices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gel_class_choices_field__uuid__value` (`uuid`),
  ADD KEY `gel_class_choices_field__user_id__target_id` (`user_id`),
  ADD KEY `gel_class_choices_field__class_id__target_id` (`class_id`),
  ADD KEY `gel_class_choices_field__choice_id__target_id` (`choice_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `gel_class_choices`
--
ALTER TABLE `gel_class_choices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
