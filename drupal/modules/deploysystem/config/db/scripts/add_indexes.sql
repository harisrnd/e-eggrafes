CREATE UNIQUE INDEX uidx_region_regno ON eepal_region_field_data(registry_no);
CREATE UNIQUE INDEX uidx_adminarea_regno ON eepal_admin_area_field_data(registry_no);
CREATE UNIQUE INDEX uidx_taxis_userid ON applicant_users(taxis_userid);
CREATE INDEX uidx_authtoken ON applicant_users(authtoken(150));
