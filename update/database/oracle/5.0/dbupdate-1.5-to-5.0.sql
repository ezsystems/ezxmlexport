ALTER TABLE ezxport_available_cclass_attr RENAME COLUMN contentclass_attribute_id TO contentclass_attribute_id_tmp;
ALTER TABLE ezxport_available_cclass_attr ADD contentclass_attribute_id INT DEFAULT 0 NOT NULL;
UPDATE ezxport_available_cclass_attr SET contentclass_attribute_id=contentclass_attribute_id_tmp;
ALTER TABLE ezxport_available_cclass_attr DROP COLUMN contentclass_attribute_id_tmp;
ALTER TABLE ezxport_available_cclass_attr MODIFY (contentclass_id INTEGER DEFAULT 0);
CREATE INDEX fk_contentclassattribute_id ON ezxport_available_cclass_attr ( contentclass_id );
CREATE UNIQUE INDEX idx_contentclass_attribute_id ON ezxport_available_cclass_attr ( contentclass_attribute_id );

ALTER TABLE ezxport_available_cclasses RENAME COLUMN contentclass_id TO contentclass_id_tmp;
ALTER TABLE ezxport_available_cclasses ADD contentclass_id INT DEFAULT 0 NOT NULL;
UPDATE ezxport_available_cclasses SET contentclass_id=contentclass_id_tmp;
ALTER TABLE ezxport_available_cclasses DROP COLUMN contentclass_id_tmp;
CREATE UNIQUE INDEX idx_contentclass_id ON ezxport_available_cclasses ( contentclass_id );

ALTER TABLE ezxport_customers MODIFY (ftp_target DEFAULT '');
ALTER TABLE ezxport_customers DROP CONSTRAINT pk_ezxport_customers;
ALTER TABLE ezxport_customers ADD PRIMARY KEY ( id );

ALTER TABLE ezxport_export_object_log MODIFY (contentobject_id INTEGER DEFAULT 0);
ALTER TABLE ezxport_export_object_log MODIFY (process_log_id INTEGER DEFAULT 0);
CREATE INDEX fk_process_log_id ON ezxport_export_object_log (process_log_id);

ALTER TABLE ezxport_exports MODIFY (compression INTEGER DEFAULT 0);
ALTER TABLE ezxport_exports MODIFY (customer_id INTEGER DEFAULT 0);
ALTER TABLE ezxport_exports MODIFY (export_limit INTEGER DEFAULT 0);
ALTER TABLE ezxport_exports MODIFY (ftp_target VARCHAR2(200) DEFAULT 'false');
ALTER TABLE ezxport_exports MODIFY (related_object_handling INTEGER DEFAULT 0);
ALTER TABLE ezxport_exports MODIFY (sources DEFAULT '');
ALTER TABLE ezxport_exports MODIFY (xslt_file VARCHAR2(70) NULL);
ALTER TABLE ezxport_exports DROP CONSTRAINT pk_ezxport_exports;
ALTER TABLE ezxport_exports ADD PRIMARY KEY ( id );
CREATE INDEX fk_customers ON ezxport_exports ( customer_id );

ALTER TABLE ezxport_process_logs MODIFY (export_id INTEGER DEFAULT 0);
ALTER TABLE ezxport_process_logs MODIFY (status INTEGER DEFAULT 0);
ALTER TABLE ezxport_process_logs RENAME COLUMN end_id_transfert_date TO end_transfert_date;
ALTER TABLE ezxport_process_logs RENAME COLUMN start_id_transfert_date TO start_transfert_date;
ALTER TABLE ezxport_process_logs DROP CONSTRAINT pk_ezxport_process_logs CASCADE;
ALTER TABLE ezxport_process_logs ADD PRIMARY KEY ( id );

ALTER TABLE ezxport_export_object_log ADD CONSTRAINT FK_process_log_id FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs (id) ON DELETE CASCADE;

