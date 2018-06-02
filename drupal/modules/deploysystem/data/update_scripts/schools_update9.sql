/* change name of ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ*/
UPDATE `school_list` SET `name` = '1ο ΠΕΙΡΑΜΑΤΙΚΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΑΘΗΝΩΝ - ΓΕΝΝΑΔΕΙΟ' where id = 266;
UPDATE `gel_school` SET `name` = '1ο ΠΕΙΡΑΜΑΤΙΚΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΑΘΗΝΩΝ - ΓΕΝΝΑΔΕΙΟ' where id = 226;

/* change reg_no and name to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΠΕΡΙΣΤΕΡΙ-ΑΘΗΝΑ - Σ.ΑΥΓΟΥΛΕΑ-ΛΙΝΑΡΔΑΤΟΥ */
UPDATE `school_list` SET `registry_no` = '0560001' where id = 455;
UPDATE `gel_school` SET `registry_no` = '0560001' where id = 414;
UPDATE `school_list` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ Σ.ΑΥΓΟΥΛΕΑ-ΛΙΝΑΡΔΑΤΟΥ' where id = 455;
UPDATE `gel_school` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ Σ.ΑΥΓΟΥΛΕΑ-ΛΙΝΑΡΔΑΤΟΥ' where id = 414;

/* change reg_no to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΦΙΔΝΕΣ ΑΤΤΙΚΗΣ - ΕΚΠΑΙΔΕΥΤΙΚΗ ΑΝΑΓΕΝΝΗΣΗ */
UPDATE `school_list` SET `registry_no` = '0560003' where id = 464;
UPDATE `gel_school` SET `registry_no` = '0560003' where id = 423;

/* change reg_no to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΙΓΑΛΕΩ - ΔΙΑΜΑΝΤΟΠΟΥΛΟΥ */
UPDATE `school_list` SET `registry_no` = '0560002' where id = 450;
UPDATE `gel_school` SET `registry_no` = '0560002' where id = 409;
UPDATE `school_list` SET `name` = 'ΕΚΠΑΙΔΕΥΤΗΡΙΑ ΔΙΑΜΑΝΤΟΠΟΥΛΟΥ - ΙΔΙΩΤΙΚΟ ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ' where id = 450;
UPDATE `gel_school` SET `name` = 'ΕΚΠΑΙΔΕΥΤΗΡΙΑ ΔΙΑΜΑΝΤΟΠΟΥΛΟΥ - ΙΔΙΩΤΙΚΟ ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ' where id = 409;

/* change name of ΠΡΟΤΥΠΟ ΠΕΙΡΑΜΑΤΙΚΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΗΡΑΚΛΕΙΟΥ*/
UPDATE `school_list` SET `name` = 'ΠΕΙΡΑΜΑΤΙΚΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΗΡΑΚΛΕΙΟΥ' where id = 11059;
UPDATE `gel_school` SET `name` = 'ΠΕΙΡΑΜΑΤΙΚΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΗΡΑΚΛΕΙΟΥ' where id = 1850;


/* ΕΙΣΑΓΩΓΗ  ΑΡΣΑΚΕΙΟ ΙΔΙΩΤΙΚΟ ΛΥΚΕΙΟ ΙΩΑΝΝΙΝΩΝ*/
INSERT INTO `school_list` (`id`, `uuid`, `langcode`, `user_id`, `name`, `registry_no`, `unit_type`, `unit_type_id`, `status`, `created`, `changed`)
 VALUES
(15062, 'fabd77ba-36c8-4860-b168-4f805a5405f9', 'el', 1, 'ΑΡΣΑΚΕΙΟ ΙΔΙΩΤΙΚΟ ΛΥΚΕΙΟ ΙΩΑΝΝΙΝΩΝ',	'2061004'	,	'ΛΥΚΕΙΟ',	4	, 1, 1525855214, 1525855214);

INSERT INTO `gel_school` (`id`, `uuid`, `langcode`, `user_id`, `mm_id`, `registry_no`, `unit_type`, `unit_type_id`, `postal_code`, `fax_number`, `phone_number`, `maile`, `region_edu_admin_id`, `edu_admin_id`, `prefecture_id`, `municipality`, `operation_shift`, `metathesis_region`, `capacity_class_a`, `approved_a`, `approv_decision`, `approv_role`, `approvdate`, `status`, `created`, `changed`, `name`, `street_address`) VALUES
(3090, 'aba55df4-f4cc-45eb-a10d-7c87c275ac46', 'el', 1, '0000000', '2061004', 'ΛΥΚΕΙΟ',	4	,'45500',	'', '2651052055', 'mail@lyk-arsak-ioann.ioa.sch.gr', 	1	,	25	,NULL, '', 'ΗΜΕΡΗΣΙΟ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1520459597, 1520459597, 'ΑΡΣΑΚΕΙΟ ΙΔΙΩΤΙΚΟ ΛΥΚΕΙΟ ΙΩΑΝΝΙΝΩΝ', 'ΕΠΑΡΧΙΑΚΗ ΟΔΟΣ ΛΟΓΓΑΔΩΝ-ΒΑΣΙΛΙΚΗΣ');

/*
Aλλαγη ονόματος: ΚΩΔΙΚΟΣ ΣΧΟΛΕΙΟΥ :  0560029
ΟΝΟΜΑΣΙΑ ΣΧΟΛΕΙΟΥ : ΠΑΠΑΧΑΡΑΛΑΜΠΕΙΟ ΕΚΠΑΙΔΕΥΤΗΡΙΟ ΙΔΩΤΙΚΟ ΓΥΜΝΑΣΙΟ*/

UPDATE `school_list` SET `name` = 'ΠΑΠΑΧΑΡΑΛΑΜΠΕΙΟ ΕΚΠΑΙΔΕΥΤΗΡΙΟ ΙΔΩΤΙΚΟ ΓΥΜΝΑΣΙΟ' where `registry_no` = '0560029';
UPDATE `gel_school` SET `name` = 'ΠΑΠΑΧΑΡΑΛΑΜΠΕΙΟ ΕΚΠΑΙΔΕΥΤΗΡΙΟ ΙΔΩΤΙΚΟ ΓΥΜΝΑΣΙΟ' where `registry_no` = '0560029';
