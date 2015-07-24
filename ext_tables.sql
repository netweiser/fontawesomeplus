#
# Table structure for table 'tx_fontawesomeplus_domain_model_font'
#
CREATE TABLE tx_fontawesomeplus_domain_model_font (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
		
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	
	title varchar(255) DEFAULT '' NOT NULL,	
	version varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	destination varchar(255) DEFAULT '' NOT NULL,
	icons text,
		
	PRIMARY KEY (uid),
	KEY parent (pid),
);

#
# Extend Table structure 'sys_file_reference'
#
CREATE TABLE sys_file_reference (
	tx_fontawesomeplus_classname varchar(255) DEFAULT '' NOT NULL,
);