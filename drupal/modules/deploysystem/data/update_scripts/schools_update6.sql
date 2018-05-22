
/* change reg_no to IDIOTIKO GYMNASIO TSIAMOULH */
UPDATE `school_list` SET `registry_no` = '0560013' where id = 454;
UPDATE `gel_school` SET `registry_no` = '0560013' where id = 413;

/* change name of ESPERINO MYRINAS*/
UPDATE `school_list` SET `name` = 'ΕΣΠΕΡΙΝΟ ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΜΥΡΙΝΑΣ' where id = 827;
UPDATE `gel_school` SET `name` = 'ΕΣΠΕΡΙΝΟ ΓΥΜΝΑΣΙΟ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΜΥΡΙΝΑΣ' where id = 741;

/* DELETE LYKEIO MYRINAS*/
DELETE FROM `school_list` WHERE id = 832;
DELETE FROM `gel_school` WHERE id = 742;
