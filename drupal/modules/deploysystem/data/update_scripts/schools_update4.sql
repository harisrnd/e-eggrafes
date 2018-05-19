/*
INSERTIONS:
0560031 ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ - ΣΧΟΛΗ ΚΑΛΟΓΕΡΟΠΟΥΛΟΥ ΕΠΕ
4944001 ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΠΕΥΚΟΧΩΡΙΟΥ
3144002 14ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΛΑΡΙΣΑΣ
4944002 Ημερήσιο Γενικό Λύκειο Νέου Μαρμαρά
*/

INSERT INTO `school_list` (`id`, `uuid`, `langcode`, `user_id`, `name`, `registry_no`, `unit_type`, `unit_type_id`, `status`, `created`, `changed`)
 VALUES
(15048, 'a4b858bc-3ecf-4dde-bba1-5d8a91868cc3', 'el', 1, 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ - ΣΧΟΛΗ ΚΑΛΟΓΕΡΟΠΟΥΛΟΥ ΕΠΕ',	'0560031'	,	'ΓΥΜΝΑΣΙΟ',	3	, 1, 1525855214, 1525855214),
(15049, '2a0c9a25-9278-4660-bece-e4c44fd5dab3', 'el', 1, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΠΕΥΚΟΧΩΡΙΟΥ',	'4944001'	,	'ΛΥΚΕΙΟ',	4	, 1, 1525855214, 1525855214),
(15050, '1c4d1830-f6c5-4d91-a790-6cc36eb67f3f', 'el', 1, '14ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΛΑΡΙΣΑΣ',	'3144002'	,	'ΛΥΚΕΙΟ',	4	, 1, 1525855214, 1525855214),
(15051, '5341f8f0-0f3f-4302-b45a-b7382657c6af', 'el', 1, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΝΕΟΥ ΜΑΡΜΑΡΑ',	'4944002'	,	'ΛΥΚΕΙΟ',	4	, 1, 1525855214, 1525855214);


INSERT INTO `gel_school` (`id`, `uuid`, `langcode`, `user_id`, `mm_id`, `registry_no`, `unit_type`, `unit_type_id`, `postal_code`, `fax_number`, `phone_number`, `maile`, `region_edu_admin_id`, `edu_admin_id`, `prefecture_id`, `municipality`, `operation_shift`, `metathesis_region`, `capacity_class_a`, `approved_a`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `name`, `street_address`) VALUES
(3076, '3f80158d-854d-42f8-a532-48bb3b7ab167', 'el', 1, '0000000', '0560031', 'ΓΥΜΝΑΣΙΟ',	3	,'19007',	'', '2294067477', 'kalogeropoulou@gmail.com', 	3	,	7	,NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ - ΣΧΟΛΗ ΚΑΛΟΓΕΡΟΠΟΥΛΟΥ ΕΠΕ', 'ΚΑΤΩ ΣΟΥΛΙΟΥ 161'),
(3077, '7c7e590b-553b-4fb5-aaa8-f2db1e0b3cb4', 'el', 1, '0000000', '4944001', 'ΛΥΚΕΙΟ',	4	,'63085',	'', '2374063043', 'mail@lyk-pefkoch.chal.sch.gr', 	9	,	42	,NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΠΕΥΚΟΧΩΡΙΟΥ', 'Πευκοχώρι'),
(3078, 'bc067e12-7d6a-4849-b16e-343b01cb1941', 'el', 1, '0000000', '3144002', 'ΛΥΚΕΙΟ',	4	,'41335',	'', '2410617384', 'mail@14lyk-laris.lar.sch.gr', 	7	,	28	,NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, '14ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΛΑΡΙΣΑΣ', 'ΧΑΤΖΗΚΥΡΙΑΚΟΥ ΓΚΙΚΑ & Μ. ΧΑΤΖΗΔΑΚΗ'),
(3079, '5873c002-60e0-4efe-83dd-2d9ae6771d88', 'el', 1, '0000000', '4944002', 'ΛΥΚΕΙΟ',	4	,'63081',	'', '2375071139', 'mail@lyk-n-marmar.chal.sch.gr', 	9	,	42	,NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΝΕΟΥ ΜΑΡΜΑΡΑ', 'Ν. ΜΑΡΜΑΡΑΣ ΧΑΛΚΙΔΙΚΗΣ');

/* change reg_no to IDIOTIKO GYMNASIO KOSTEA GEITONA */
UPDATE `school_list` SET `registry_no` = '0560010' where id = 463;
UPDATE `gel_school` SET `registry_no` = '0560010' where id = 422;

/* change name of GYMNASIO PEFKOXORIOY (L-T)*/
UPDATE `school_list` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΠΕΥΚΟΧΩΡΙΟΥ ΧΑΛΚΙΔΙΚΗΣ' where  registry_no = '4904030';
UPDATE `gel_school` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΠΕΥΚΟΧΩΡΙΟΥ ΧΑΛΚΙΔΙΚΗΣ' where registry_no = '4904030';
