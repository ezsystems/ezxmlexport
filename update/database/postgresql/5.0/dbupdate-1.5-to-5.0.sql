
ALTER TABLE ezxport_exports RENAME COLUMN export_from_last TO export_from_last_tmp;
ALTER TABLE ezxport_exports ADD COLUMN export_from_last integer default '0' NOT NULL;
UPDATE ezxport_exports SET export_from_last=export_from_last_tmp;
ALTER TABLE ezxport_exports DROP COLUMN export_from_last_tmp;

ALTER TABLE ezxport_exports RENAME COLUMN compression TO compression_tmp;
ALTER TABLE ezxport_exports ADD COLUMN compression integer default '0' NOT NULL;
UPDATE ezxport_exports SET compression=compression_tmp;
ALTER TABLE ezxport_exports DROP COLUMN compression_tmp;

ALTER TABLE ezxport_exports RENAME COLUMN related_object_handling TO related_object_handling_tmp;
ALTER TABLE ezxport_exports ADD COLUMN related_object_handling integer default '0' NOT NULL;
UPDATE ezxport_exports SET related_object_handling=related_object_handling_tmp;
ALTER TABLE ezxport_exports DROP COLUMN related_object_handling_tmp;

ALTER TABLE ezxport_exports RENAME COLUMN export_hidden_nodes TO export_hidden_nodes_tmp;
ALTER TABLE ezxport_exports ADD COLUMN export_hidden_nodes integer default '0' NOT NULL;
UPDATE ezxport_exports SET export_hidden_nodes=export_hidden_nodes_tmp;
ALTER TABLE ezxport_exports DROP COLUMN export_hidden_nodes_tmp;

ALTER TABLE ezxport_available_cclass_attr DROP CONSTRAINT ezxport_available_cclass_attr_contentclass_attribute_id_key;
CREATE UNIQUE INDEX idx_contentclass_attribute_id ON ezxport_available_cclass_attr USING btree (contentclass_attribute_id);
CREATE INDEX fk_contentclassattribute_id ON ezxport_available_cclass_attr USING btree ( contentclass_id );

ALTER TABLE ezxport_available_cclasses DROP CONSTRAINT ezxport_available_cclasses_contentclass_id_key;
CREATE UNIQUE INDEX idx_contentclass_id ON ezxport_available_cclasses USING btree ( contentclass_id );

ALTER TABLE ezxport_customers RENAME COLUMN ftp_target TO ftp_target_tmp;
ALTER TABLE ezxport_customers ADD COLUMN ftp_target text;
ALTER TABLE ezxport_customers ALTER ftp_target SET NOT NULL ;
UPDATE ezxport_customers SET ftp_target=ftp_target_tmp;
ALTER TABLE ezxport_customers DROP COLUMN ftp_target_tmp;
ALTER TABLE ezxport_customers DROP CONSTRAINT pk_ezxport_customers;
ALTER TABLE ezxport_customers ADD CONSTRAINT ezxport_customers_pkey PRIMARY KEY ( id );
CREATE INDEX fk_customers ON ezxport_exports USING btree ( customer_id );


ALTER TABLE ezxport_exports RENAME COLUMN ftp_target TO ftp_target_tmp;
ALTER TABLE ezxport_exports ADD COLUMN ftp_target character varying(200);
ALTER TABLE ezxport_exports ALTER ftp_target SET DEFAULT 'false'::character varying ;
ALTER TABLE ezxport_exports ALTER ftp_target SET NOT NULL ;
UPDATE ezxport_exports SET ftp_target=ftp_target_tmp;
ALTER TABLE ezxport_exports DROP COLUMN ftp_target_tmp;
ALTER TABLE ezxport_exports RENAME COLUMN xslt_file TO xslt_file_tmp;
ALTER TABLE ezxport_exports ADD COLUMN xslt_file character varying(70);
ALTER TABLE ezxport_exports ALTER xslt_file SET DEFAULT ''::character varying ;
UPDATE ezxport_exports SET xslt_file=xslt_file_tmp;
ALTER TABLE ezxport_exports DROP COLUMN xslt_file_tmp;
ALTER TABLE ezxport_exports DROP CONSTRAINT pk_ezxport_exports;
ALTER TABLE ezxport_exports ADD CONSTRAINT ezxport_exports_pkey PRIMARY KEY ( id );

ALTER TABLE ezxport_process_logs DROP CONSTRAINT pk_ezxport_process_logs CASCADE;
ALTER TABLE ezxport_process_logs ADD CONSTRAINT ezxport_process_logs_pkey PRIMARY KEY ( id );
ALTER TABLE ezxport_export_object_log ADD CONSTRAINT FK_process_log_id FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs (id) ON DELETE CASCADE;

CREATE INDEX fk_process_log_id ON ezxport_export_object_log USING btree ( process_log_id );
