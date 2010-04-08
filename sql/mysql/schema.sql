CREATE TABLE ezxport_available_cclass_attr (
  contentclass_attribute_id int(11) NOT NULL,
  contentclass_id int(11) NOT NULL,
  UNIQUE KEY contentclass_attribute_id (contentclass_attribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxport_available_cclasses (
  contentclass_id int(11) NOT NULL,
  UNIQUE KEY contentclass_id (contentclass_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxport_customers (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  ftp_target text NOT NULL,
  slicing_mode CHAR(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxport_export_object_log (
  process_log_id int(11) NOT NULL,
  contentobject_id int(11) NOT NULL,
  KEY FK_process_log_id (process_log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxport_exports (
  id int(11) NOT NULL AUTO_INCREMENT,
  customer_id int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  description varchar(200) NOT NULL,
  sources text NOT NULL,
  ftp_target varchar(200) NOT NULL,
  slicing_mode CHAR(1) NOT NULL DEFAULT '1',
  start_date varchar(15) NOT NULL DEFAULT '0',
  end_date varchar(15) NOT NULL DEFAULT '0',
  export_schedule varchar(100) NOT NULL,
  export_limit int(11) NOT NULL,
  export_from_last tinyint(4) NOT NULL DEFAULT '0',
  compression tinyint(4) NOT NULL,
  related_object_handling tinyint(4) NOT NULL,
  xslt_file varchar(70) NOT NULL,
  export_hidden_nodes tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ezxport_process_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  export_id int(11) NOT NULL,
  start_date varchar(10) NOT NULL,
  end_date varchar(10) NOT NULL,
  start_transfert_date varchar(10) NOT NULL,
  end_transfert_date varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ezxport_export_object_log`
  ADD CONSTRAINT FK_process_log_id FOREIGN KEY (process_log_id) REFERENCES ezxport_process_logs (id) ON DELETE CASCADE;