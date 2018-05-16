INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3290, 'el', 1, 'record3290', 395, 24, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3290, '58c53d3f-7332-4e92-bbd7-bbe3409f8f14', 'el');

INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3291, 'el', 1, 'record3291', 161, 12, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3291, 'dd500d7f-3bcf-4b2a-b7d1-65af8a12d8a1', 'el');

UPDATE `eepal_specialties_in_epal_field_data` SET specialty_id = 28
WHERE id = 188;
