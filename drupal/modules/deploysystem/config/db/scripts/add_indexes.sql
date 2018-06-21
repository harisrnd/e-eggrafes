CREATE UNIQUE INDEX uidx_region_regno ON eepal_region_field_data(registry_no);
CREATE UNIQUE INDEX uidx_adminarea_regno ON eepal_admin_area_field_data(registry_no);
CREATE UNIQUE INDEX uidx_taxis_userid ON applicant_users(taxis_userid);
CREATE INDEX uidx_authtoken ON applicant_users(authtoken(150));
CREATE INDEX gel_lastschool_index on gel_student (lastschool_unittypeid, lastschool_class);
CREATE INDEX gel_regno_index on gel_student (lastschool_registrynumber);

CREATE INDEX oauthses_name ON oauthost_session(name);
CREATE INDEX oauthses_authtoken ON oauthost_session(authtoken);

ALTER TABLE `gel_student` ADD KEY `myschool_id` (`myschool_id`);
ALTER TABLE `epal_student` ADD KEY `myschool_id` (`myschool_id`);
