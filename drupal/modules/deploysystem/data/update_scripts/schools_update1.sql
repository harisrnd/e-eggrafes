INSERT INTO `school_list` (`id`, `uuid`, `langcode`, `user_id`, `name`, `registry_no`, `unit_type`, `unit_type_id`, `status`, `created`, `changed`) VALUES
(15023, '447a492e-1a99-42a0-96df-c9cba7200894', 'el', 1, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΖΗΠΑΡΙΟΥ', '1044001', 'ΓΕΝΙΚΟ ΛΥΚΕΙΟ', 4, 1, 1525855214, 1525855214),
(15024, '08535bb1-7414-4eef-8125-a477a2686094', 'el', 1, 'ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΚΕΡΑΤΣΙΝΙΟΥ - ΔΡΑΠΕΤΣΩΝΑ', '0540002', 'ΓΥΜΝΑΣΙΟ', 3, 1, 1525855214, 1525855214),
(15025, '0fccd8d7-cc79-4a86-8284-1e4e492a101f', 'el', 1, '2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΜΕΤΑΜΟΡΦΩΣΗΣ ΑΤΤΙΚΗΣ', '0544004', 'ΓΕΝΙΚΟ ΛΥΚΕΙΟ', 4, 1, 1525855214, 1525855214);

UPDATE `school_list` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΖΗΠΑΡΙΟΥ' where id = 523;
UPDATE `school_list` SET `name` = '1ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΜΕΤΑΜΟΡΦΩΣΗΣ ΑΤΤΙΚΗΣ' where id = 8435;

INSERT INTO `gel_school` (`id`, `uuid`, `langcode`, `user_id`, `mm_id`, `registry_no`, `unit_type`, `unit_type_id`, `postal_code`, `fax_number`, `phone_number`, `maile`, `region_edu_admin_id`, `edu_admin_id`, `prefecture_id`, `municipality`, `operation_shift`, `metathesis_region`, `capacity_class_a`, `approved_a`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `name`, `street_address`) VALUES
(3051, 'e236936b-c651-4355-9850-2d4d18652534', 'el', 1, '0000000', '1044001', 'ΓΕΝΙΚΟ ΛΥΚΕΙΟ', 4, '85300', '', '2242067184', 'mail@lyk-zipar.dod.sch.gr', 4, 47, NULL, 'ΚΩ', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΖΗΠΑΡΙΟΥ', 'Πλάτωνος  &  Αγ. Σπυρίδωνος'),
(3052, 'c1576a09-3887-4091-b57b-f059719cb6d0', 'el', 1, '0000000', '0540002', 'ΓΥΜΝΑΣΙΟ', 3, '18755', '', '2104613060', 'mail@gym-kall-kerats.att.sch.gr', 4, 47, NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΚΕΡΑΤΣΙΝΙΟΥ - ΔΡΑΠΕΤΣΩΝΑ', 'ΕΛΕΥΘΕΡΙΟΥ ΒΕΝΙΖΕΛΟΥ 98'),
(3053, '5782fc75-43e8-4620-ad18-3780a5921ebd', 'el', 1, '0000000', '0544004', 'ΓΕΝΙΚΟ ΛΥΚΕΙΟ', 4, '14451', '2169390169', '2169390168', 'mail@2lyk-metam.att.sch.gr', 4, 47, NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, '2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΜΕΤΑΜΟΡΦΩΣΗΣ ΑΤΤΙΚΗΣ', 'ΑΓΙΟΥ ΝΕΚΤΑΡΙΟΥ 71-75');

UPDATE `gel_school` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΖΗΠΑΡΙΟΥ' where id = 482;
UPDATE `gel_school` SET `name` = '1ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΜΕΤΑΜΟΡΦΩΣΗΣ ΑΤΤΙΚΗΣ' where id = 871;

UPDATE `school_list` SET `registry_no` = '0580150' where id = 452;
UPDATE `gel_school` SET `registry_no` = '0580150' where id = 411;
