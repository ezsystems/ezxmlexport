CREATE TABLE ezxmlexport_available_contentclass_attributes (
  contentclass_attribute_id int(11) NOT NULL,
  contentclass_id int(11) NOT NULL,
  UNIQUE KEY contentclass_attribute_id (contentclass_attribute_id),
  KEY fk_contentclassattribute_id (contentclass_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxmlexport_available_contentclasses (
  contentclass_id int(11) NOT NULL,
  UNIQUE KEY contentclass_id (contentclass_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxmlexport_customers (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  ftp_target text NOT NULL,
  slicing_mode enum('1','n') NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE ezxmlexport_export_object_log (
  process_log_id int(11) NOT NULL,
  contentobject_id int(11) NOT NULL,
  KEY FK_process_log_id (process_log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxmlexport_exports (
  id int(11) NOT NULL AUTO_INCREMENT,
  customer_id int(11) NOT NULL,
  name varchar(200) NOT NULL,
  description varchar(200) NOT NULL,
  sources text NOT NULL,
  ftp_target varchar(200) NOT NULL,
  slicing_mode enum('1','n') NOT NULL,
  start_date varchar(15) NOT NULL DEFAULT '0',
  end_date varchar(15) NOT NULL DEFAULT '0',
  export_schedule varchar(100) NOT NULL,
  export_limit int(11) NOT NULL,
  export_from_last tinyint(1) NOT NULL DEFAULT '0',
  compression tinyint(1) NOT NULL,
  related_object_handling tinyint(1) NOT NULL,
  xslt_file varchar(70) NOT NULL,
  export_hidden_nodes tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY fk_customers (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE ezxmlexport_process_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  export_id int(11) NOT NULL,
  start_date varchar(10) NOT NULL,
  end_date varchar(10) NOT NULL,
  start_transfert_date varchar(10) NOT NULL,
  end_transfert_date varchar(10) NOT NULL,
  status int(3) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE ezxmlexport_export_object_log
  ADD CONSTRAINT FK_process_log_id FOREIGN KEY (process_log_id) REFERENCES ezxmlexport_process_logs (id) ON DELETE CASCADE;