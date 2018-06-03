INSERT INTO `school_list` (`id`, `uuid`, `langcode`, `user_id`, `name`, `registry_no`, `unit_type`, `unit_type_id`, `status`, `created`, `changed`)
 VALUES
(15061, 'a479c5d3-0e3a-4e53-8ec2-8d68b6db61e7', 'el', 1, 'ΕΣΠΕΡΙΝΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΟΜΟΤΗΝΗΣ',	'4245001'	,	'ΛΥΚΕΙΟ',	4	, 1, 1525855214, 1525855214);

INSERT INTO `gel_school` (`id`, `uuid`, `langcode`, `user_id`, `mm_id`, `registry_no`, `unit_type`, `unit_type_id`, `postal_code`, `fax_number`, `phone_number`, `maile`, `region_edu_admin_id`, `edu_admin_id`, `prefecture_id`, `municipality`, `operation_shift`, `metathesis_region`, `capacity_class_a`, `approved_a`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `name`, `street_address`) VALUES
(3089, 'f1b18cc0-858d-4a32-b088-52df977cf300', 'el', 1, '0000000', '4245001', 'ΛΥΚΕΙΟ',	4	,'69132',	'', '2531081218', 'mail@lyk-esp-komot.rod.sch.gr', 	12	,	5	,NULL, '', 'ΕΣΠΕΡΙΝΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΕΣΠΕΡΙΝΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΟΜΟΤΗΝΗΣ', 'ΦΙΛΙΠΠΟΥ 33');


UPDATE `school_list` SET `name` = '2ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΑΘΗΝΩΝ - ΘΕΟΔΩΡΟΣ ΑΓΓΕΛΟΠΟΥΛΟΣ' where `registry_no` = '0551198';
UPDATE `school_list` SET `name` = 'ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΞΗΡΟΚΑΜΠΙΟΥ ΛΑΚΩΝΙΑΣ' where `registry_no` = '3009010';
UPDATE `school_list` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΜΑΚΡΥΧΩΡΙΟΥ' where `registry_no` = '3105050';
UPDATE `school_list` SET `name` = 'ΠΕΙΡΑΜΑΤΙΚΟ ΣΧΟΛΕΙΟ ΠΑΝΕΠΙΣΤΗΜΙΟΥ ΘΕΣΣΑΛΟΝΙΚΗΣ ΓΥΜΝΑΣΙΟ - ΛΥΚΕΙΟ' where `registry_no` = '1901001';
UPDATE `school_list` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΕΚΠΑΙΔΕΥΤΗΡΙΑ ΓΕΙΤΟΝΑ ΑΕΜΕ', `registry_no` = '0580505' WHERE id = 9130;
UPDATE `school_list` SET `registry_no` = '2060001' WHERE id = 741;

UPDATE `gel_school` SET `name` = '2ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΑΘΗΝΩΝ - ΘΕΟΔΩΡΟΣ ΑΓΓΕΛΟΠΟΥΛΟΣ' where `registry_no` = '0551198';
UPDATE `gel_school` SET `name` = 'ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΞΗΡΟΚΑΜΠΙΟΥ ΛΑΚΩΝΙΑΣ' where `registry_no` = '3009010';
UPDATE `gel_school` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΜΑΚΡΥΧΩΡΙΟΥ' where `registry_no` = '3105050';
UPDATE `gel_school` SET `name` = 'ΠΕΙΡΑΜΑΤΙΚΟ ΣΧΟΛΕΙΟ ΠΑΝΕΠΙΣΤΗΜΙΟΥ ΘΕΣΣΑΛΟΝΙΚΗΣ ΓΥΜΝΑΣΙΟ - ΛΥΚΕΙΟ' where `registry_no` = '1901001';
UPDATE `gel_school` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΕΚΠΑΙΔΕΥΤΗΡΙΑ ΓΕΙΤΟΝΑ ΑΕΜΕ', `registry_no` = '0580505' WHERE id = 960;
UPDATE `gel_school` SET `registry_no` = '2060001' WHERE id = 673;
