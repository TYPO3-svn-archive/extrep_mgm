#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_extrepmgm_function tinyint(3) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE fe_users (
	tx_extrepmgm_selext blob NOT NULL,
	tx_extrepmgm_isreviewer tinyint(3) DEFAULT '0' NOT NULL,	
	tx_extrepmgm_isdocreviewer tinyint(3) DEFAULT '0' NOT NULL,	
	tx_extrepmgm_maxbytes int(11) unsigned DEFAULT '0' NOT NULL,
	tx_extrepmgm_jobs text NOT NULL,
	tx_extrepmgm_addpoints int(11) unsigned DEFAULT '0' NOT NULL,
	tx_extrepmgm_jobspoints int(11) unsigned DEFAULT '0' NOT NULL,
	tx_extrepmgm_images tinyblob NOT NULL,

	tx_extrepmgm_contribute tinytext NOT NULL,
	tx_extrepmgm_personallife tinytext NOT NULL,
	tx_extrepmgm_typo3experiences tinytext NOT NULL,
);


#
# Table structure for table 'tt_content_tx_extrepmgm_group_mm'
# 
#
CREATE TABLE tx_extrep_keytable_tx_extrepmgm_group_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tt_content'
#
CREATE TABLE tx_extrep_keytable (
    tx_extrepmgm_group int(11) unsigned DEFAULT '0' NOT NULL
	tx_extrepmgm_appr_flag tinyint(3) DEFAULT '0' NOT NULL,
	tx_extrepmgm_nodoc_flag tinyint(3) DEFAULT '0' NOT NULL,

	tx_extrepmgm_cache_state varchar(15) DEFAULT '' NOT NULL,
	tx_extrepmgm_cache_review tinyint(3) DEFAULT '0' NOT NULL,
	tx_extrepmgm_cache_oodoc int(11) unsigned DEFAULT '0' NOT NULL,
	tx_extrepmgm_cache_missingtrans int(11) unsigned DEFAULT '0' NOT NULL,
	tx_extrepmgm_cache_infoarray mediumblob NOT NULL,
	tx_extrepmgm_documentation tinyblob NOT NULL,
	tx_extrepmgm_homepage mediumtext NOT NULL,
	tx_extrepmgm_rev tinytext NOT NULL,
);



#
# Table structure for table 'tx_extrepmgm_extgroup'
#
CREATE TABLE tx_extrepmgm_extgroup (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    title varchar(30) DEFAULT '' NOT NULL,
    descr tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);



#
# Table structure for table 'tx_extrepmgm_langadmin_sub_translators_mm'
# 
#
CREATE TABLE tx_extrepmgm_langadmin_sub_translators_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_extrepmgm_langadmin'
#
CREATE TABLE tx_extrepmgm_langadmin (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    title varchar(50) DEFAULT '' NOT NULL,
    langkey varchar(10) DEFAULT '' NOT NULL,
    auth_translator int(11) unsigned DEFAULT '0' NOT NULL,
    sub_translators int(11) unsigned DEFAULT '0' NOT NULL,
	credits text NOT NULL,
    charset varchar(20) DEFAULT '' NOT NULL,
	sponsor_company tinytext NOT NULL,
	sponsor_url tinytext NOT NULL,
	ref_lang_keys tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);


#
# Table structure for table 'tx_extrepmgm_langadmin'
#
CREATE TABLE tx_extrepmgm_langelements (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	extension_key varchar(30) DEFAULT '' NOT NULL,
	langkey varchar(10) DEFAULT '' NOT NULL,
	data_content mediumblob NOT NULL,
	deleted_tstamp int(11) DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid)
);

CREATE TABLE tx_extrepmgm_oodocreview (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	extension_key varchar(30) DEFAULT '' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	oodoc mediumblob NOT NULL,
	oodoc_md5 varchar(32) DEFAULT '' NOT NULL,
	oodoc_size int(11) unsigned DEFAULT '0' NOT NULL,
	oodoc_filename tinytext NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_extrepmgm_oodoctoc (
	document_unique_ref int(11) unsigned DEFAULT '0' NOT NULL,
	extension_uid int(11) unsigned DEFAULT '0' NOT NULL,
	is_included_hash int(11) unsigned DEFAULT '0' NOT NULL,
	sxwfile tinytext NOT NULL,
	cur_tmp_file tinytext NOT NULL,
	cur_oodoc_ref int(11) unsigned DEFAULT '0' NOT NULL,
	doc_title tinytext NOT NULL,
	doc_author tinytext NOT NULL,
	doc_author_email tinytext NOT NULL,
	doc_images int(11) unsigned DEFAULT '0' NOT NULL,
	doc_tables int(11) unsigned DEFAULT '0' NOT NULL,
	doc_objects int(11) unsigned DEFAULT '0' NOT NULL,
	doc_pages int(11) unsigned DEFAULT '0' NOT NULL,
	doc_words int(11) unsigned DEFAULT '0' NOT NULL,
	doc_chars int(11) unsigned DEFAULT '0' NOT NULL,
	doc_size int(11) unsigned DEFAULT '0' NOT NULL,
	doc_mtime int(11) unsigned DEFAULT '0' NOT NULL,
	cat tinyint(3) DEFAULT '0' NOT NULL,
	lang int(11) DEFAULT '0' NOT NULL,
	toc_cache blob NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (document_unique_ref),
    KEY extension_uid (extension_uid)
);

CREATE TABLE tx_extrepmgm_oodoctocel (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	document_unique_ref int(11) unsigned DEFAULT '0' NOT NULL,

	extension_uid int(11) unsigned DEFAULT '0' NOT NULL,
#	is_included_hash int(11) unsigned DEFAULT '0' NOT NULL,
#	sxwfile tinytext NOT NULL,
#	cur_tmp_file tinytext NOT NULL,
#	cur_oodoc_ref int(11) unsigned DEFAULT '0' NOT NULL,

	arr_key int(11) unsigned DEFAULT '0' NOT NULL,
	stripped_value tinytext NOT NULL,
	xmlarr_index int(11) unsigned DEFAULT '0' NOT NULL,
	hlevel tinyint(3) DEFAULT '0' NOT NULL,
	stripped_next tinytext NOT NULL,
	typeofcontent tinyint(3) DEFAULT '0' NOT NULL,
	aud tinyint(3) DEFAULT '0' NOT NULL,
	show_level3 tinyint(3) DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid),
    KEY document_unique_ref (document_unique_ref)
    KEY extension_uid (extension_uid)
);


CREATE TABLE tx_extrepmgm_oodoccache (
	cache_ref int(11) unsigned DEFAULT '0' NOT NULL,
	document_unique_ref int(11) unsigned DEFAULT '0' NOT NULL,
	content mediumblob NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (cache_ref),
    KEY document_unique_ref (document_unique_ref)
);










CREATE TABLE tx_extrepmgm_team (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    title varchar(50) DEFAULT '' NOT NULL,
	purpose text NOT NULL,
	supervisor int(11) unsigned DEFAULT '0' NOT NULL,
	leader int(11) unsigned DEFAULT '0' NOT NULL,
	team int(11) unsigned DEFAULT '0' NOT NULL,
	
    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_extrepmgm_project (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    title varchar(50) DEFAULT '' NOT NULL,
	description text NOT NULL,
	skills text NOT NULL,
	status text NOT NULL,
	priority tinyint(4) unsigned DEFAULT '0' NOT NULL,
	team_id int(11) unsigned DEFAULT '0' NOT NULL,
	leader int(11) unsigned DEFAULT '0' NOT NULL,
	team int(11) unsigned DEFAULT '0' NOT NULL,
	notepad text NOT NULL,
	
    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_extrepmgm_team_members_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);
CREATE TABLE tx_extrepmgm_project_members_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);
