CREATE TABLE ezxport_available_cclass_attr (
  contentclass_attribute_id INT DEFAULT 0 NOT NULL,
  contentclass_id           INT DEFAULT 0 NOT NULL,
  CONSTRAINT idx_contentclass_attribute_id UNIQUE (contentclass_attribute_id ));

CREATE INDEX fk_contentclassattribute_id ON ezxport_available_cclass_attr(contentclass_id);

CREATE TABLE ezxport_available_cclasses (
  contentclass_id INT DEFAULT 0 NOT NULL,
  CONSTRAINT idx_contentclass_id UNIQUE (contentclass_id ));

CREATE TABLE ezxport_customers (
  id           INT    NOT NULL,
  NAME         VARCHAR2(200)    NOT NULL,
  ftp_target   CLOB DEFAULT '' NOT NULL,
  slicing_mode CHAR(1) DEFAULT '1' NOT NULL,
  PRIMARY KEY ( id ));

CREATE TABLE ezxport_exports (
  id                      INT    NOT NULL,
  customer_id             INT DEFAULT 0 NOT NULL,
  NAME                    VARCHAR2(200)    NOT NULL,
  description             VARCHAR2(200)    NOT NULL,
  sources                 CLOB DEFAULT '' NOT NULL,
  ftp_target              VARCHAR2(200) DEFAULT 'false' NOT NULL,
  slicing_mode            CHAR(1) DEFAULT '1' NOT NULL,
  start_date              VARCHAR2(15)    DEFAULT '0'    NOT NULL,
  end_date                VARCHAR2(15)    DEFAULT '0'    NOT NULL,
  export_schedule         VARCHAR2(100)    NOT NULL,
  export_limit            INT DEFAULT 0 NOT NULL,
  export_from_last        INT DEFAULT 0    NOT NULL,
  compression             INT DEFAULT 0 NOT NULL,
  related_object_handling INT DEFAULT 0  NOT NULL,
  xslt_file               VARCHAR2(70),
  export_hidden_nodes     INT    DEFAULT 0    NOT NULL,
  PRIMARY KEY ( id ));

CREATE INDEX fk_customers ON ezxport_exports(customer_id);

CREATE TABLE ezxport_process_logs (
  id                   INT    NOT NULL,
  export_id            INT DEFAULT 0 NOT NULL,
  start_date           VARCHAR2(10)    NOT NULL,
  end_date             VARCHAR2(10)    NOT NULL,
  start_transfert_date VARCHAR2(10)    NOT NULL,
  end_transfert_date   VARCHAR2(10)    NOT NULL,
  status               INT DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id ));

CREATE TABLE ezxport_export_object_log (
  process_log_id   INT DEFAULT 0 NOT NULL,
  contentobject_id INT DEFAULT 0 NOT NULL,
  CONSTRAINT fk_process_log_id FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs(id) ON DELETE CASCADE);

CREATE INDEX fk_process_log_id ON ezxport_export_object_log(process_log_id);

CREATE SEQUENCE s_xport_customers;
CREATE SEQUENCE s_xport_exports;
CREATE SEQUENCE s_xport_process_logs;

CREATE OR REPLACE TRIGGER ezxport_customers_id_tr
BEFORE INSERT ON ezxport_customers FOR EACH ROW WHEN ( new.id IS NULL )
BEGIN
  SELECT s_xport_customers.nextval INTO :new.id FROM dual;
END;
/

CREATE OR REPLACE TRIGGER ezxport_exports_id_tr
BEFORE INSERT ON ezxport_exports FOR EACH ROW WHEN ( new.id IS NULL )
BEGIN
  SELECT s_xport_exports.nextval INTO :new.id FROM dual;
END;
/

CREATE OR REPLACE TRIGGER ezxport_process_logs_id_tr
BEFORE INSERT ON ezxport_process_logs FOR EACH ROW WHEN ( new.id IS NULL )
BEGIN
  SELECT s_xport_process_logs.nextval INTO :new.id FROM dual;
END;
/
