/* insert ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΠΕΡΙΣΤΕΡΙΟΥ */
INSERT INTO `school_list` (`id`, `uuid`, `langcode`, `user_id`, `name`, `registry_no`, `unit_type`, `unit_type_id`, `status`, `created`, `changed`) VALUES
(15026, '1d12295d-5b5c-464c-87dd-ffab9213ba4d', 'el', 1, 'ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΠΕΡΙΣΤΕΡΙΟΥ', '0540001', 'ΓΥΜΝΑΣΙΟ', 3, 1, 1525855214, 1525855214);

INSERT INTO `gel_school` (`id`, `uuid`, `langcode`, `user_id`, `mm_id`, `registry_no`, `unit_type`, `unit_type_id`, `postal_code`, `fax_number`, `phone_number`, `maile`, `region_edu_admin_id`, `edu_admin_id`, `prefecture_id`, `municipality`, `operation_shift`, `metathesis_region`, `capacity_class_a`, `approved_a`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `name`, `street_address`) VALUES
(3054, '72e3392b-7cac-4291-a972-12e0f7c879b2', 'el', 1, '1025328', '0540001', 'ΓΥΜΝΑΣΙΟ', 3, '12133', '', '2105743178', 'mail@gym-kall-perist.att.sch.gr', 3, 9, NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΠΕΡΙΣΤΕΡΙΟΥ', 'ΑΓ. ΙΩΑΝΝΟΥ ΘΕΟΛΟΓΟΥ ΚΑΙ ΛΕΥΚΩΣΙΑΣ 50');

/*  fixed entries regarding ZHRIDI */
UPDATE `school_list` SET `registry_no` = '0560006' where id = 460;
UPDATE `gel_school` SET `registry_no` = '0560006' where id = 419;
DELETE FROM `school_list` WHERE id = 14594;
DELETE FROM `gel_school` WHERE id = 2990;

/* update errors in region / adminarea  in previous insertions.. */
UPDATE `gel_school` SET `region_edu_admin_id` = '3',  `edu_admin_id` = '12'  where id = 3052;
UPDATE `gel_school` SET `region_edu_admin_id` = '3',  `edu_admin_id` = '8'  where id = 3053;
