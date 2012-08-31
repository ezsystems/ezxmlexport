

ALTER TABLE ezxport_available_cclass_attr ADD INDEX fk_contentclassattribute_id ( contentclass_id );
ALTER TABLE ezxport_available_cclass_attr DROP INDEX contentclass_attribute_id;
ALTER TABLE ezxport_available_cclass_attr ADD UNIQUE INDEX idx_contentclass_attribute_id (contentclass_attribute_id);

ALTER TABLE ezxport_available_cclasses DROP INDEX contentclass_id;
ALTER TABLE ezxport_available_cclasses ADD UNIQUE INDEX idx_contentclass_id (contentclass_id);

ALTER TABLE ezxport_customers CHANGE COLUMN ftp_target ftp_target longtext NOT NULL DEFAULT '';

ALTER TABLE ezxport_export_object_log DROP FOREIGN KEY FK_process_log_id;
ALTER TABLE ezxport_export_object_log DROP INDEX FK_process_log_id;
ALTER TABLE ezxport_export_object_log ADD INDEX fk_process_log_id ( process_log_id );
ALTER TABLE ezxport_export_object_log ADD CONSTRAINT fk_process_log_id FOREIGN KEY (process_log_id) REFERENCES ezxport_process_logs (id) ON DELETE CASCADE;

ALTER TABLE ezxport_exports CHANGE COLUMN compression compression int(1) NOT NULL DEFAULT '0';
ALTER TABLE ezxport_exports CHANGE COLUMN export_from_last export_from_last int(1) NOT NULL DEFAULT '0';
ALTER TABLE ezxport_exports CHANGE COLUMN export_hidden_nodes export_hidden_nodes int(1) NOT NULL DEFAULT '0';
ALTER TABLE ezxport_exports CHANGE COLUMN xslt_file xslt_file varchar(70) DEFAULT '';
ALTER TABLE ezxport_exports CHANGE COLUMN ftp_target ftp_target varchar(200) NOT NULL DEFAULT 'false';
ALTER TABLE ezxport_exports CHANGE COLUMN related_object_handling related_object_handling int(1) NOT NULL DEFAULT '0';
ALTER TABLE ezxport_exports CHANGE COLUMN sources sources longtext NOT NULL DEFAULT '';
ALTER TABLE ezxport_exports ADD INDEX fk_customers ( customer_id );
