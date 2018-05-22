/* PROSTHIKI SPECIALTY EPIPLOPOIIA! */
INSERT INTO `eepal_specialty_field_data` (`id`, `langcode`, `user_id`, `name`, `sector_id`, `status`, `created`, `changed`, `default_langcode`) VALUES
(86, 'el', 1, 'Επιπλοποιίας - Ξυλογλυπτικής', 4, 1, 1482308338, 1485510661, 1);
INSERT INTO `eepal_specialty` (`id`, `uuid`, `langcode`) VALUES
(86, 'cb0321fd-7e79-4f8c-9e1c-f3fcea316656', 'el');

/* PROSTHIKI 1 EPAL KYPARISSISA: OIKONOMIKES YPIRESIES */
INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3300, 'el', 1, 'record3300', 376, 15, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3300, '016b272a-7004-427a-b61c-a18569ff18cb', 'el');

/* PROSTHIKH EPIPLOPOIIAS STO 4 SIVITANIDEIO*/
INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3301, 'el', 1, 'record3301', 4, 86, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3301, 'b43f8926-aa43-4132-a1bb-82a3f83c9020', 'el');

/* PROSTHIKI 1 EPAL XANIA: OIKONOMIKES YPIRESIES */
INSERT INTO `eepal_specialties_in_epal_field_data` (`id`, `langcode`, `user_id`, `name`, `epal_id`, `specialty_id`, `capacity_class_specialty`, `capacity_class_specialty_d`, `approved_speciality`, `approv_decision`, `approv_role`, `approvdate`, `approved_speciality_d`, `approv_decision_d`, `approv_role_d`, `approvdate_d`, `status`, `created`, `changed`, `default_langcode`) VALUES
(3302, 'el', 1, 'record3302', 318, 15, 0, 0, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1525426812, 1525426812, 1);
INSERT INTO `eepal_specialties_in_epal` (`id`, `uuid`, `langcode`) VALUES
(3302, '133c0f46-f377-48a5-a122-d6cb79900889', 'el');

/* ALLAGI SE EPAL KAISARIANIS: DIAKOSMHSHS SE APOKATASTASHS*/ /*MONO AN DEN YPARXOUN AITISEIS??? */
/*
select epal_student.id from epal_student, epal_student_epal_chosen, epal_student_course_field where (epal_student.id = epal_student_epal_chosen.student_id and epal_student.id = epal_student_course_field.student_id) and epal_student_epal_chosen.epal_id = 39 and epal_student_course_field.coursefield_id = 21 and epal_student.delapp = 0
 */
UPDATE `eepal_specialties_in_epal_field_data` SET specialty_id = 39
WHERE id = 221;
