CREATE TABLE ezxport_available_cclass_attr (
  contentclass_attribute_id INT    NOT NULL,
  contentclass_id           INT    NOT NULL,
     UNIQUE (contentclass_attribute_id ));

CREATE TABLE ezxport_available_cclasses (
  contentclass_id INT    NOT NULL,
     UNIQUE (contentclass_id ));

CREATE TABLE ezxport_customers (
  ID           INT    NOT NULL,
  NAME         VARCHAR(200)    NOT NULL,
  ftp_target   CLOB    NOT NULL,
  slicing_mode CHAR(1),
  CONSTRAINT cons_slicing_mode CHECK ( slicing_mode IN ('1','n') ),
  CONSTRAINT pk_ezxport_customers PRIMARY KEY ( id ));

CREATE TABLE ezxport_exports (
  ID                      INT    NOT NULL,
  customer_id             INT    NOT NULL,
  NAME                    VARCHAR(200)    NOT NULL,
  description             VARCHAR(200)    NOT NULL,
  sources                 CLOB    NOT NULL,
  ftp_target              VARCHAR(200)    NOT NULL,
  slicing_mode            CHAR(1),
  start_date              VARCHAR(15)    DEFAULT '0'    NOT NULL,
  end_date                VARCHAR(15)    DEFAULT '0'    NOT NULL,
  export_schedule         VARCHAR(100)    NOT NULL,
  export_limit            INT    NOT NULL,
  export_from_last        SMALLINT    DEFAULT '0'    NOT NULL,
  compression             SMALLINT    NOT NULL,
  related_object_handling SMALLINT    NOT NULL,
  xslt_file               VARCHAR(70)    NOT NULL,
  export_hidden_nodes     SMALLINT    DEFAULT '0'    NOT NULL,
  CONSTRAINT cons_slicing_mode CHECK ( slicing_mode IN ('1','n') ),
  CONSTRAINT pk_ezxport_exports PRIMARY KEY ( id ));

CREATE TABLE ezxport_process_logs (
  ID                   INT    NOT NULL,
  export_id            INT    NOT NULL,
  start_date           VARCHAR(10)    NOT NULL,
  end_date             VARCHAR(10)    NOT NULL,
  start_transfert_date VARCHAR(10)    NOT NULL,
  end_transfert_date   VARCHAR(10)    NOT NULL,
  status               INT    NOT NULL,
  CONSTRAINT pk_ezxport_process_logs PRIMARY KEY ( id ));

CREATE TABLE ezxport_export_object_log (
  process_log_id   INT    NOT NULL,
  contentobject_id INT    NOT NULL,
  CONSTRAINT fk_process_log_id FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs(id) ON DELETE CASCADE);

CREATE SEQUENCE seq_ezxport_customers;

CREATE SEQUENCE seq_ezxport_exports;

CREATE SEQUENCE seq_ezxport_process_logs;

CREATE OR REPLACE TRIGGER ezxport_customers_tr
  BEFORE INSERT ON ezxport_customers
  FOR EACH ROW
  WHEN ( NEW.ID IS NULL )
BEGIN
  SELECT seq_ezxport_customers.nextval
  INTO   :new.ID
  FROM   dual;
END;
/

CREATE OR REPLACE TRIGGER ezxport_exports_tr
  BEFORE INSERT ON ezxport_exports
  FOR EACH ROW
  WHEN ( NEW.ID IS NULL )
BEGIN
  SELECT seq_ezxport_exports.nextval
  INTO   :new.ID
  FROM   dual;
END;
/

CREATE OR REPLACE TRIGGER ezxport_process_logs_tr
  BEFORE INSERT ON ezxport_process_logs
  FOR EACH ROW
  WHEN ( NEW.ID IS NULL )
BEGIN
  SELECT seq_ezxport_process_logs.nextval
  INTO   :new.ID
  FROM   dual;
END;
/ 
