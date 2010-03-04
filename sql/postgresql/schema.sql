CREATE TABLE ezxport_available_cclass_attr (
  contentclass_attribute_id INT   NOT NULL,
  contentclass_id           INT   NOT NULL,
    UNIQUE (contentclass_attribute_id ));

CREATE TABLE ezxport_available_cclasses (
  contentclass_id INT   NOT NULL,
    UNIQUE (contentclass_id ));

CREATE TABLE ezxport_customers (
  ID           SERIAL   NOT NULL,
  NAME         VARCHAR(200)   NOT NULL,
  ftp_target   TEXT   NOT NULL,
  slicing_mode CHAR(1)   CHECK ( slicing_mode IN ('1','n') )   NOT NULL,
  CONSTRAINT pk_ezxport_customers PRIMARY KEY ( id ));

CREATE TABLE ezxport_exports (
  ID                      SERIAL   NOT NULL,
  customer_id             INT   NOT NULL,
  NAME                    VARCHAR(200)   NOT NULL,
  description             VARCHAR(200)   NOT NULL,
  sources                 TEXT   NOT NULL,
  ftp_target              VARCHAR(200)   NOT NULL,
  slicing_mode            CHAR(1)   CHECK ( slicing_mode IN ('1','n') )   NOT NULL,
  start_date              VARCHAR(15)   DEFAULT '0'   NOT NULL,
  end_date                VARCHAR(15)   DEFAULT '0'   NOT NULL,
  export_schedule         VARCHAR(100)   NOT NULL,
  export_limit            INT   NOT NULL,
  export_from_last        SMALLINT   DEFAULT '0'   NOT NULL,
  compression             SMALLINT   NOT NULL,
  related_object_handling SMALLINT   NOT NULL,
  xslt_file               VARCHAR(70)   NOT NULL,
  export_hidden_nodes     SMALLINT   DEFAULT '0'   NOT NULL,
  CONSTRAINT pk_ezxport_exports PRIMARY KEY ( id ));

CREATE TABLE ezxport_process_logs (
  ID                   SERIAL   NOT NULL,
  export_id            INT   NOT NULL,
  start_date           VARCHAR(10)   NOT NULL,
  end_date             VARCHAR(10)   NOT NULL,
  start_transfert_date VARCHAR(10)   NOT NULL,
  end_transfert_date   VARCHAR(10)   NOT NULL,
  status               INT   NOT NULL,
  CONSTRAINT pk_ezxport_process_logs PRIMARY KEY ( id ));

CREATE TABLE ezxport_export_object_log (
  process_log_id   INT   NOT NULL,
  contentobject_id INT   NOT NULL,
  CONSTRAINT "FK_process_log_id" FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs(id) ON DELETE CASCADE);