/* change reg_no to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ- ΕΛΛΗΝΙΚΟ ΚΟΛΛΕΓΙΟ ΘΕΣΣΑΛΟΝΙΚΗΣ */
UPDATE `school_list` SET `registry_no` = '1960007' where id = 726;
UPDATE `gel_school` SET `registry_no` = '1960007' where id = 658;

/* change name of ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ*/
UPDATE `school_list` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ Λ.Τ. ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ' where id = 11665;
UPDATE `gel_school` SET `name` = 'ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ Λ.Τ. ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ' where id = 2374;

/* change reg_no to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΡΓΟΣ - ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΥΤΕΝΕΡΓΩ */
UPDATE `school_list` SET `registry_no` = '0260002' where id = 8;
UPDATE `gel_school` SET `registry_no` = '0260002' where id = 8;

/* change name of ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ*/
UPDATE `school_list` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΥΤΕΝΕΡΓΩ' where id = 8;
UPDATE `gel_school` SET `name` = 'ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΑΥΤΕΝΕΡΓΩ' where id = 8;

/* DELETE ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΣΤΑΥΡΟΥΠΟΛΗΣ ΞΑΝΘΗΣ*/
DELETE FROM `school_list` WHERE id = 11681;
DELETE FROM `gel_school` WHERE id = 2386;

/* change reg_no to ΙΔΙΩΤΙΚΟ ΓΥΜΝΑΣΙΟ ΣΠΥΡΙΔΩΝ ΝΤΑΓΚΑΣ ΚΑΙ ΣΙΑ ΟΕ "ΝΕΑ ΠΑΙΔΕΙΑ" 0581106 --> 0560020*/
UPDATE `school_list` SET `registry_no` = '0560020' where `registry_no` = '0581106';
UPDATE `gel_school` SET `registry_no` = '0560020' where `registry_no` = '0581106';
