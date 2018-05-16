INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3292, 'el', 1, 'record3292', 64, 24, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3292, 'b5d3bf33-db99-42bb-8cd9-6b287e09ac53', 'el');

INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3293, 'el', 1, 'record3293', 393, 11, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3293, '5d5e8412-a5f3-4567-8535-50d6e33a6c88', 'el');

INSERT INTO `eepal_sectors_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `sector_id`, `capacity_class_sector`, `approved_sector`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `default_langcode`) VALUES
(1733, 'el', 1, 'record1733', 362, 1, 0, 1, NULL, NULL, NULL, 1, 1525426341, 1525426341, 1);
INSERT INTO `eepal_sectors_in_epal` (`id`, `uuid`, `langcode`) VALUES
(1733, 'e11baea8-9652-4a43-9034-3a4144d6715c', 'el');

DELETE FROM eepal_specialties_in_epal_field_data WHERE id = 400;
DELETE FROM eepal_specialties_in_epal WHERE id = 400;
