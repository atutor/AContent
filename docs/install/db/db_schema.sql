#########################################################
# Database setup SQL for a new install of Transformable #
#########################################################

# --------------------------------------------------------
# Table structure for table `config`
# since 0.1

CREATE TABLE `config` (
  `name` CHAR( 30 ) NOT NULL default '',
  `value` CHAR( 255 ) NOT NULL default '',
  PRIMARY KEY ( `name` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `content`
# since 0.1

CREATE TABLE `content` (
  `content_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `content_parent_id` mediumint(8) unsigned NOT NULL default '0',
  `ordering` mediumint(8) NOT NULL default '0',
  `last_modified` TIMESTAMP NOT NULL,
  `revision` tinyint(3) unsigned NOT NULL default '0',
  `formatting` tinyint(4) NOT NULL default '0',
  `keywords` TEXT NOT NULL ,
  `content_path` TEXT NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `text` TEXT NOT NULL ,
  `head` TEXT NOT NULL,
  `use_customized_head` TINYINT(4) NOT NULL,
  `test_message` TEXT NOT NULL,
  `content_type` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY  (`content_id`),
  KEY `course_id` (`course_id`),
  FULLTEXT(keywords, title, text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `content_forums_assoc`

CREATE TABLE `content_forums_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `forum_id` INTEGER UNSIGNED NOT NULL,
PRIMARY KEY ( `content_id` , `forum_id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `course_tests_assoc`
# since 0.1
CREATE TABLE `content_tests_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `test_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `test_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `courses`
# since 0.1

CREATE TABLE `courses` (
  `course_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `category_id` mediumint(8) unsigned NOT NULL default '0',
  `content_packaging` enum('none','top','all') NOT NULL default 'top',
  `access` enum('public','protected','private') NOT NULL default 'public',
  `title` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `course_dir_name` VARCHAR(255) NOT NULL,
  `max_quota` varchar(30) NOT NULL default '',
  `max_file_size` varchar(30) NOT NULL default '',
  `copyright` text NOT NULL ,
  `primary_language` varchar(5) NOT NULL default '',
  `icon` varchar(75) NOT NULL default '',
  `side_menu` VARCHAR( 255 ) NOT NULL default '',
  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`course_id`),
  FULLTEXT(`title`, `description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `course_categories`
# since 0.1

CREATE TABLE `course_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `category_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `forums`

CREATE TABLE `forums` (
  `forum_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(240) NOT NULL default '',
  `description` TEXT ,
  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `forums_courses`
# since 0.1

CREATE TABLE `forums_courses` (
  `forum_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`forum_id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `languages`
# since 0.1

CREATE TABLE `languages` (
  `language_code` varchar(20) NOT NULL default '',
  `charset` varchar(80) NOT NULL default '',
  `reg_exp` varchar(124) NOT NULL default '',
  `native_name` varchar(80) NOT NULL default '',
  `english_name` varchar(80) NOT NULL default '',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`language_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `lang_codes`
# since 0.1

CREATE TABLE `lang_codes` (
  `code_3letters` varchar(3) NOT NULL default '',
  `direction` varchar(16) NOT NULL default '',
  `code_2letters` varchar(2) default NULL,
  `description` varchar(50) default NULL,
  PRIMARY KEY  (`code_3letters`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `mail_queue`
# since 0.1

CREATE TABLE `mail_queue` (
  `mail_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `to_email` VARCHAR( 50 ) NOT NULL default '',
  `to_name` VARCHAR( 50 ) NOT NULL default '',
  `from_email` VARCHAR( 50 ) NOT NULL default '',
  `from_name` VARCHAR( 50 ) NOT NULL default '',
  `char_set` VARCHAR( 20 ) NOT NULL default '',
  `subject` VARCHAR(255) NOT NULL ,
  `body` TEXT,
  PRIMARY KEY ( `mail_id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `oauth_client_servers`
# since 0.1

CREATE TABLE `oauth_client_servers` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_server` VARCHAR(255) NOT NULL default '',
  `consumer_key` TEXT NOT NULL ,
  `consumer_secret` TEXT NOT NULL ,
  `expire_threshold` INT NOT NULL default 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id` ),
  UNIQUE INDEX idx_consumer ( `oauth_server` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `oauth_client_tokens`
# since 0.1

CREATE TABLE `oauth_client_tokens` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL,
  `token` VARCHAR(50) NOT NULL default '',
  `token_type` VARCHAR(50) NOT NULL NOT NULL default '',
  `token_secret` TEXT NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL ,
  `assign_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id`, `token` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `oauth_server_consumers`
# since 0.1

CREATE TABLE `oauth_server_consumers` (
  `consumer_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `consumer` VARCHAR(255) NOT NULL default '',
  `consumer_key` TEXT NOT NULL ,
  `consumer_secret` TEXT NOT NULL ,
  `expire_threshold` INT NOT NULL default 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY ( `consumer_id` ),
  UNIQUE INDEX idx_consumer ( `consumer` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `oauth_server_tokens`
# since 0.1

CREATE TABLE `oauth_server_tokens` (
  `consumer_id` MEDIUMINT UNSIGNED NOT NULL,
  `token` VARCHAR(50) NOT NULL default '',
  `token_type` VARCHAR(50) NOT NULL NOT NULL default '',
  `token_secret` TEXT NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL ,
  `assign_date` datetime NOT NULL,
  PRIMARY KEY ( `consumer_id`, `token` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `patches`
# since 0.1

CREATE TABLE `patches` (
	`patches_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`system_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
	`patch_folder` VARCHAR(250) NOT NULL default '',
  `description` TEXT,
	`available_to` VARCHAR(250) NOT NULL default '',
  `sql_statement` text,
  `status` varchar(20) NOT NULL default '',
  `remove_permission_files` text,
  `backup_files` text,
  `patch_files` text,
  `author` VARCHAR(255) NOT NULL,
  `installed_date` datetime NOT NULL,
	PRIMARY KEY  (`patches_id`)
);


# --------------------------------------------------------
# Table structure for table `patches_files`
# since 0.1

CREATE TABLE `patches_files` (
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`name` TEXT,
	`location` VARCHAR(250) NOT NULL default '',
	PRIMARY KEY  (`patches_files_id`)
);

# --------------------------------------------------------
# Table structure for table `patches_files_actions`
# since 0.1

CREATE TABLE `patches_files_actions` (
	`patches_files_actions_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`code_from` TEXT,
	`code_to` TEXT,
	PRIMARY KEY  (`patches_files_actions_id`)
);

# --------------------------------------------------------
# Table structure for table `myown_patches`
# since 0.1

CREATE TABLE `myown_patches` (
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`system_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
  `description` TEXT,
  `sql_statement` text,
  `status` varchar(20) NOT NULL default '',
  `last_modified` datetime NOT NULL,
	PRIMARY KEY  (`myown_patch_id`)
);

# --------------------------------------------------------
# Table structure for table `myown_patches_dependent`
# since 0.1

CREATE TABLE `myown_patches_dependent` (
	`myown_patches_dependent_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`dependent_patch_id` VARCHAR(50) NOT NULL default '',
	PRIMARY KEY  (`myown_patches_dependent_id`)
);

# --------------------------------------------------------
# Table structure for table `myown_patches_files`
# since 0.1

CREATE TABLE `myown_patches_files` (
	`myown_patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`action` VARCHAR(20) NOT NULL default '',
	`name` VARCHAR(250) NOT NULL,
	`location` VARCHAR(250) NOT NULL default '',
	`code_from` TEXT,
	`code_to` TEXT,
	`uploaded_file` TEXT,
	PRIMARY KEY  (`myown_patches_files_id`)
);

# --------------------------------------------------------
# Table structure for table `privileges`
# since 0.1

CREATE TABLE `privileges` (
  `privilege_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title_var` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `create_date` datetime NOT NULL,
  `last_update` datetime,
  `link` varchar(255) NOT NULL DEFAULT '',
  `menu_sequence` tinyint(4) NOT NULL,
  `open_to_public` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`privilege_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

# --------------------------------------------------------
# Table structure for table `tests`
# since 0.1

CREATE TABLE `tests` (
  `test_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL ,
  `description` TEXT,
  PRIMARY KEY  (`test_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# --------------------------------------------------------
# Table structure for table `tests_questions`
# since 0.1

CREATE TABLE `tests_questions` (
  `question_id` mediumint(8) unsigned NOT NULL auto_increment,
  `category_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `feedback` TEXT ,
  `question` TEXT ,
  `choice_0` TEXT ,
  `choice_1` TEXT ,
  `choice_2` TEXT ,
  `choice_3` TEXT ,
  `choice_4` TEXT ,
  `choice_5` TEXT ,
  `choice_6` TEXT ,
  `choice_7` TEXT ,
  `choice_8` TEXT ,
  `choice_9` TEXT ,
  `answer_0` tinyint(4) NOT NULL default '0',
  `answer_1` tinyint(4) NOT NULL default '0',
  `answer_2` tinyint(4) NOT NULL default '0',
  `answer_3` tinyint(4) NOT NULL default '0',
  `answer_4` tinyint(4) NOT NULL default '0',
  `answer_5` tinyint(4) NOT NULL default '0',
  `answer_6` tinyint(4) NOT NULL default '0',
  `answer_7` tinyint(4) NOT NULL default '0',
  `answer_8` tinyint(4) NOT NULL default '0',
  `answer_9` tinyint(4) NOT NULL default '0',
  `option_0` TEXT ,
  `option_1` TEXT ,
  `option_2` TEXT ,
  `option_3` TEXT ,
  `option_4` TEXT ,
  `option_5` TEXT ,
  `option_6` TEXT ,
  `option_7` TEXT ,
  `option_8` TEXT ,
  `option_9` TEXT ,
  `properties` tinyint(4) NOT NULL default '0',
  `content_id` mediumint(8) NOT NULL,  
  PRIMARY KEY  (`question_id`),
  KEY `category_id` (category_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `tests_questions_assoc`
# since 0.1

CREATE TABLE `tests_questions_assoc` (
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `weight` varchar(4) NOT NULL default '',
  `ordering` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`test_id`,`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `tests_questions_categories`
# since 0.1

CREATE TABLE `tests_questions_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` char(200) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `themes`
# since 0.1

CREATE TABLE `themes` (
  `title` varchar(80) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `dir_name` varchar(20) NOT NULL default '',
  `last_updated` date NOT NULL default '0000-00-00',
  `extra_info` TEXT,
  `status` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `users`
# since 0.1
CREATE TABLE `users` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `user_group_id` mediumint(8) NOT NULL,
  `first_name` varchar(100),
  `last_name` varchar(100),
  `email` varchar(50),
  `web_service_id` varchar(40) NOT NULL,
  `status` tinyint(3) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `last_login` datetime,
  `preferences` text,
  `is_author` tinyint(3) NOT NULL default '0',
  `organization` varchar(100),
  `phone` varchar(30),
  `address` varchar(100),
  `city` varchar(100),
  `province` varchar(100),
  `country` varchar(30),
  `postal_code` varchar(10),
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

# --------------------------------------------------------
# Table structure for table `user_courses`
# since 0.1

CREATE TABLE `user_courses` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `course_id` mediumint(8) unsigned NOT NULL,
  `role` mediumint(8) unsigned NOT NULL,
  `last_cid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

# --------------------------------------------------------
# Table structure for table `user_groups`
# since 0.1

CREATE TABLE `user_groups` (
  `user_group_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `create_date` datetime NOT NULL,
  `last_update` datetime,
  PRIMARY KEY  (`user_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

# --------------------------------------------------------
# Table structure for table `user_group_privilege`
# since 0.1

CREATE TABLE `user_group_privilege` (
  `user_group_id` mediumint(8) unsigned NOT NULL,
  `privilege_id` mediumint(8) unsigned NOT NULL,
  `user_requirement` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_group_id`, `privilege_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------
# Table structure for table `user_links`
# since 0.1
CREATE TABLE `user_links` (
  `user_link_id` mediumint(8) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `URI` text,
  `last_guideline_ids` varchar(50) NOT NULL,
  `last_sessionID` varchar(40) NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY  (`user_link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

#Setup Table for Access4All
CREATE TABLE `primary_resources` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`primary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `primary_resources_types` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`primary_resource_id`,`type_id`)
) TYPE = MYISAM;

CREATE TABLE `resource_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` TEXT,
  PRIMARY KEY  (`type_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `secondary_resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`secondary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources_types` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`secondary_resource_id`,`type_id`)
) TYPE = MYISAM;

INSERT INTO `resource_types` VALUES
(1, 'auditory'),
(2, 'sign_language'),
(3, 'textual'),
(4, 'visual');

INSERT INTO `config` (`name`, `value`) VALUES('encyclopedia', 'http://www.wikipedia.org');
INSERT INTO `config` (`name`, `value`) VALUES('dictionary', 'http://dictionary.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('thesaurus', 'http://thesaurus.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('atlas', 'http://maps.google.ca/');
INSERT INTO `config` (`name`, `value`) VALUES('calculator', 'http://www.calculateforfree.com/');
INSERT INTO `config` (`name`, `value`) VALUES('note_taking', 'http://www.aypwip.org/webnote/');
INSERT INTO `config` (`name`, `value`) VALUES('abacas', 'http://www.mandarintools.com/abacus.html');

#End Access4All setup 

-- Dumping data for table `languages`

INSERT INTO `languages` (`language_code`, `charset`, `reg_exp`, `native_name`, `english_name`, `status`) VALUES
('en', 'utf-8', 'en([-_][[:alpha:]]{2})?|english', 'English', 'English', 1);

-- Dumping data for table `lang_codes`

INSERT INTO `lang_codes` (`code_3letters`, `direction`, `code_2letters`, `description`) VALUES
('aar', 'ltr', 'aa', 'Afar'),
('abk', 'ltr', 'ab', 'Abkhazian'),
('ace', 'ltr', '', 'Achinese'),
('ach', 'ltr', '', 'Acoli'),
('ada', 'ltr', '', 'Adangme'),
('ady', 'ltr', '', 'Adyghe'),
('afa', 'ltr', '', 'Afro-Asiatic'),
('afh', 'ltr', '', 'Afrihili'),
('afr', 'ltr', 'af', 'Afrikaans'),
('ain', 'ltr', '', 'Ainu'),
('aka', 'ltr', 'ak', 'Akan'),
('akk', 'ltr', '', 'Akkadian'),
('alb', 'ltr', 'sq', 'Albanian'),
('ale', 'ltr', '', 'Aleut'),
('alg', 'ltr', '', 'Algonquianlanguages'),
('alt', 'ltr', '', 'Southern'),
('amh', 'ltr', 'am', 'Amharic'),
('anp', 'ltr', '', 'Angika'),
('apa', 'ltr', '', 'Apachelanguages'),
('ang', 'ltr', '', 'English Old(ca.450-1100)'),
('ara', 'rtl', 'ar', 'Arabic'),
('arc', 'ltr', '', 'Aramaic'),
('arg', 'ltr', 'an', 'Aragonese'),
('arm', 'ltr', 'hy', 'Armenian'),
('arn', 'ltr', '', 'Araucanian'),
('arp', 'ltr', '', 'Arapaho'),
('art', 'ltr', '', 'Artificial(Other)'),
('arw', 'ltr', '', 'Arawak'),
('asm', 'ltr', 'as', 'Assamese'),
('ast', 'ltr', '', 'Asturian'),
('ath', 'ltr', '', 'Athapascanlanguages'),
('aus', 'ltr', '', 'Australianlanguages'),
('ava', 'ltr', 'av', 'Avaric'),
('ave', 'ltr', 'ae', 'Avestan'),
('awa', 'ltr', '', 'Awadhi'),
('aym', 'ltr', 'ay', 'Aymara'),
('aze', 'ltr', 'az', 'Azerbaijani'),
('bad', 'ltr', '', 'Banda'),
('bai', 'ltr', '', 'Bamilekelanguages'),
('bak', 'ltr', 'ba', 'Bashkir'),
('bal', 'ltr', '', 'Baluchi'),
('bam', 'ltr', 'bm', 'Bambara'),
('ban', 'ltr', '', 'Balinese'),
('baq', 'ltr', 'eu', 'Basque'),
('bas', 'ltr', '', 'Basa'),
('bat', 'ltr', '', 'Baltic'),
('bej', 'ltr', '', 'Beja'),
('bel', 'ltr', 'be', 'Belarusian'),
('bem', 'ltr', '', 'Bemba'),
('ben', 'ltr', 'bn', 'Bengali'),
('ber', 'ltr', '', 'Berber(Other)'),
('bho', 'ltr', '', 'Bhojpuri'),
('bih', 'ltr', 'bh', 'Bihari'),
('bik', 'ltr', '', 'Bikol'),
('bin', 'ltr', '', 'Bini'),
('bis', 'ltr', 'bi', 'Bislama'),
('bla', 'ltr', '', 'Siksika'),
('bnt', 'ltr', '', 'Bantu(Other)'),
('tib', 'ltr', 'bo', 'Tibetan'),
('bos', 'ltr', 'bs', 'Bosnian'),
('bra', 'ltr', '', 'Braj'),
('bre', 'ltr', 'br', 'Breton'),
('btk', 'ltr', '', 'Batak(Indonesia)'),
('bua', 'ltr', '', 'Buriat'),
('bug', 'ltr', '', 'Buginese'),
('bul', 'ltr', 'bg', 'Bulgarian'),
('bur', 'ltr', 'my', 'Burmese'),
('byn', 'ltr', '', 'Blin;Bilin'),
('cad', 'ltr', '', 'Caddo'),
('cai', 'ltr', '', 'CentralAmericanIndian'),
('car', 'ltr', '', 'Caribcaribe'),
('cat', 'ltr', 'ca', 'Catalan;Valencian'),
('cau', 'ltr', '', 'Caucasian(Other)'),
('ceb', 'ltr', '', 'Cebuano'),
('cel', 'ltr', '', 'Celtic(Other)'),
('cze', 'ltr', 'cs', 'Czech'),
('ces', 'ltr', 'cs', 'Czech'),
('cha', 'ltr', 'ch', 'Chamorro'),
('chb', 'ltr', '', 'Chibcha'),
('che', 'ltr', 'ce', 'Chechen'),
('chg', 'ltr', '', 'Chagatai'),
('chi', 'ltr', 'zh', 'Chinese'),
('zho', 'ltr', 'zh', 'Chinese'),
('chk', 'ltr', '', 'Chuukese'),
('chm', 'ltr', '', 'Mari'),
('chn', 'ltr', '', 'Chinookjargon'),
('cho', 'ltr', '', 'Choctaw'),
('chp', 'ltr', '', 'Chipewyan'),
('chr', 'ltr', '', 'Cherokee'),
('chu', 'ltr', 'cu', 'ChurchSlavic'),
('chv', 'ltr', 'cv', 'Chuvash'),
('chy', 'ltr', '', 'Cheyenne'),
('cmc', 'ltr', '', 'Chamiclanguages'),
('cop', 'ltr', '', 'Coptic'),
('cor', 'ltr', 'kw', 'Cornish'),
('cos', 'ltr', 'co', 'Corsican'),
('cpp', 'ltr', '', 'Creoles'),
('cre', 'ltr', 'cr', 'Cree'),
('crh', 'ltr', '', 'CrimeanTatar'),
('crp', 'ltr', '', 'Creolesandpidgins(Other)'),
('csb', 'ltr', '', 'Kashubian'),
('cus', 'ltr', '', 'Cushitic(Other)'),
('wel', 'ltr', 'cy', 'Welsh'),
('cym', 'ltr', 'cy', 'Welsh'),
('dak', 'ltr', '', 'Dakota'),
('dan', 'ltr', 'da', 'Danish'),
('dar', 'ltr', '', 'Dargwa'),
('day', 'ltr', '', 'Dayak'),
('del', 'ltr', '', 'Delaware'),
('den', 'ltr', '', 'Slave(Athapascan)'),
('ger', 'ltr', 'de', 'German'),
('deu', 'ltr', 'de', 'German'),
('dgr', 'ltr', '', 'Dogrib'),
('din', 'ltr', '', 'Dinka'),
('div', 'ltr', 'dv', 'Divehi;Dhivehi'),
('doi', 'ltr', '', 'Dogri'),
('dra', 'ltr', '', 'Dravidian(Other)'),
('dsb', 'ltr', '', 'LowerSorbian'),
('dua', 'ltr', '', 'Duala'),
('dut', 'ltr', 'nl', 'Dutch;Flemish'),
('nld', 'ltr', 'nl', 'Dutch;Flemish'),
('dyu', 'ltr', '', 'Dyula'),
('dzo', 'ltr', 'dz', 'Dzongkha'),
('efi', 'ltr', '', 'Efik'),
('egy', 'ltr', '', 'Egyptian(Ancient)'),
('eka', 'ltr', '', 'Ekajuk'),
('elx', 'ltr', '', 'Elamite'),
('cpe', 'ltr', '', 'Creolesandpidgins Englishbased(Other)'),
('cpf', 'ltr', '', 'Creolesandpidgins French-based(Other)'),
('dum', 'ltr', '', 'Dutch Middle(ca.1050-1350)'),
('gre', 'ltr', 'el', 'Greek Modern(1453-)'),
('ell', 'ltr', 'el', 'Greek Modern(1453-)'),
('eng', 'ltr', 'en', 'English'),
('epo', 'ltr', 'eo', 'Esperanto'),
('est', 'ltr', 'et', 'Estonian'),
('eus', 'ltr', 'eu', 'Basque'),
('ewe', 'ltr', 'ee', 'Ewe'),
('ewo', 'ltr', '', 'Ewondo'),
('fan', 'ltr', '', 'Fang'),
('fao', 'ltr', 'fo', 'Faroese'),
('per', 'ltr', 'fa', 'Persian'),
('fas', 'ltr', 'fa', 'Persian'),
('fat', 'ltr', '', 'Fanti'),
('fij', 'ltr', 'fj', 'Fijian'),
('fil', 'ltr', '', 'Filipino'),
('fin', 'ltr', 'fi', 'Finnish'),
('fiu', 'ltr', '', 'Finno-Ugrian(Other)'),
('fon', 'ltr', '', 'Fon'),
('fre', 'ltr', 'fr', 'French'),
('fra', 'ltr', 'fr', 'French'),
('frr', 'ltr', '', 'NorthernFrisian'),
('frs', 'ltr', '', 'EasternFrisian'),
('fry', 'ltr', 'fy', 'WesternFrisian'),
('ful', 'ltr', 'ff', 'Fulah'),
('fur', 'ltr', '', 'Friulian'),
('gaa', 'ltr', '', 'Ga'),
('gay', 'ltr', '', 'Gayo'),
('gba', 'ltr', '', 'Gbaya'),
('gem', 'ltr', '', 'Germanic(Other)'),
('kat', 'ltr', 'ka', 'Georgian'),
('geo', 'ltr', 'ka', 'Georgian'),
('gez', 'ltr', '', 'Geez'),
('gil', 'ltr', '', 'Gilbertese'),
('gla', 'ltr', 'gd', 'Gaelic'),
('gle', 'ltr', 'ga', 'Irish'),
('glg', 'ltr', 'gl', 'Galician'),
('glv', 'ltr', 'gv', 'Manxmanx;'),
('gon', 'ltr', '', 'Gondi'),
('gor', 'ltr', '', 'Gorontalo'),
('got', 'ltr', '', 'Gothic'),
('grb', 'ltr', '', 'Grebo'),
('frm', 'ltr', '', 'French Middle(ca.1400-1600)'),
('fro', 'ltr', '', 'French Old(842-ca.1400)'),
('grc', 'ltr', '', 'Greek Ancient(to1453)'),
('grn', 'ltr', 'gn', 'Guarani'),
('gsw', 'ltr', '', 'Alemanic'),
('guj', 'ltr', 'gu', 'Gujarati'),
('gwi', 'ltr', '', 'Gwich'),
('hai', 'ltr', '', 'Haida'),
('hat', 'ltr', 'ht', 'Haitian'),
('hau', 'ltr', 'ha', 'Hausa'),
('haw', 'ltr', '', 'Hawaiian'),
('heb', 'rtl', 'he', 'Hebrew'),
('her', 'ltr', 'hz', 'Herero'),
('hil', 'ltr', '', 'Hiligaynon'),
('him', 'ltr', '', 'Himachali'),
('hin', 'ltr', 'hi', 'Hindi'),
('hit', 'ltr', '', 'Hittite'),
('hmn', 'ltr', '', 'Hmong'),
('hmo', 'ltr', 'ho', 'HiriMotu'),
('scr', 'ltr', 'hr', 'Croatian'),
('hrv', 'ltr', 'hr', 'Croatian'),
('hsb', 'ltr', '', 'UpperSorbian'),
('hun', 'ltr', 'hu', 'Hungarian'),
('hup', 'ltr', '', 'Hupa'),
('iba', 'ltr', '', 'Iban'),
('ibo', 'ltr', 'ig', 'Igbo'),
('ice', 'ltr', 'is', 'Icelandic'),
('isl', 'ltr', 'is', 'Icelandic'),
('ido', 'ltr', 'io', 'Ido'),
('iii', 'ltr', 'ii', 'SichuanYi'),
('ijo', 'ltr', '', 'Ijo'),
('iku', 'ltr', 'iu', 'Inuktitut'),
('ile', 'ltr', 'ie', 'Interlingue'),
('ilo', 'ltr', '', 'Iloko'),
('ina', 'ltr', 'ia', 'Interlingua'),
('inc', 'ltr', '', 'Indic(Other)'),
('ind', 'ltr', 'id', 'Indonesian'),
('ine', 'ltr', '', 'Indo-European(Other)'),
('inh', 'ltr', '', 'Ingush'),
('ipk', 'ltr', 'ik', 'Inupiaq'),
('ira', 'ltr', '', 'Iranian(Other)'),
('iro', 'ltr', '', 'Iroquoianlanguages'),
('ita', 'ltr', 'it', 'Italian'),
('jav', 'ltr', 'jv', 'Javanese'),
('jbo', 'ltr', '', 'Lojban'),
('jpn', 'ltr', 'ja', 'Japanese'),
('jpr', 'ltr', '', 'Judeo-Persian'),
('jrb', 'ltr', '', 'Judeo-Arabic'),
('kaa', 'ltr', '', 'Kara-Kalpak'),
('kab', 'ltr', '', 'Kabyle'),
('kac', 'ltr', '', 'Kachin'),
('kal', 'ltr', 'kl', 'Kalaallisut'),
('kam', 'ltr', '', 'Kamba'),
('kan', 'ltr', 'kn', 'Kannada'),
('kar', 'ltr', '', 'Karen'),
('kas', 'ltr', 'ks', 'Kashmiri'),
('kau', 'ltr', 'kr', 'Kanuri'),
('kaw', 'ltr', '', 'Kawi'),
('kaz', 'ltr', 'kk', 'Kazakh'),
('kbd', 'ltr', '', 'Kabardian'),
('kha', 'ltr', '', 'Khasi'),
('khi', 'ltr', '', 'Khoisan(Other)'),
('khm', 'ltr', 'km', 'Khmer'),
('kho', 'ltr', '', 'Khotanese'),
('kik', 'ltr', 'ki', 'Kikuyu;'),
('kin', 'ltr', 'rw', 'Kinyarwanda'),
('kir', 'ltr', 'ky', 'Kirghiz'),
('kmb', 'ltr', '', 'Kimbundu'),
('kok', 'ltr', '', 'Konkani'),
('kom', 'ltr', 'kv', 'Komi'),
('kon', 'ltr', 'kg', 'Kongo'),
('kor', 'ltr', 'ko', 'Korean'),
('kos', 'ltr', '', 'Kosraean'),
('kpe', 'ltr', '', 'Kpelle'),
('krc', 'ltr', '', 'Karachay-Balkar'),
('krl', 'ltr', '', 'Karelian'),
('kro', 'ltr', '', 'Kru'),
('kru', 'ltr', '', 'Kurukh'),
('kua', 'ltr', 'kj', 'Kuanyama'),
('kum', 'ltr', '', 'Kumyk'),
('kur', 'ltr', 'ku', 'Kurdish'),
('kut', 'ltr', '', 'Kutenai'),
('lad', 'ltr', '', 'Ladino'),
('lah', 'ltr', '', 'Lahnda'),
('lam', 'ltr', '', 'Lamba'),
('lao', 'ltr', 'lo', 'Lao'),
('lat', 'ltr', 'la', 'Latin'),
('lav', 'ltr', 'lv', 'Latvian'),
('lez', 'ltr', '', 'Lezghian'),
('lim', 'ltr', 'li', 'Limburgan'),
('lin', 'ltr', 'ln', 'Lingala'),
('lit', 'ltr', 'lt', 'Lithuanian'),
('lol', 'ltr', '', 'Mongo'),
('loz', 'ltr', '', 'Lozi'),
('ltz', 'ltr', 'lb', 'Luxembourgish'),
('lua', 'ltr', '', 'Luba-Lulua'),
('lub', 'ltr', 'lu', 'Luba-Katanga'),
('lug', 'ltr', 'lg', 'Ganda'),
('lui', 'ltr', '', 'Luiseno'),
('lun', 'ltr', '', 'Lunda'),
('luo', 'ltr', '', 'Luo(KenyaandTanzania)'),
('lus', 'ltr', '', 'lushai'),
('mac', 'ltr', 'mk', 'Macedonian'),
('mkd', 'ltr', 'mk', 'Macedonian'),
('mad', 'ltr', '', 'Madurese'),
('mag', 'ltr', '', 'Magahi'),
('mah', 'ltr', 'mh', 'Marshallese'),
('mai', 'ltr', '', 'Maithili'),
('mak', 'ltr', '', 'Makasar'),
('mal', 'ltr', 'ml', 'Malayalam'),
('man', 'ltr', '', 'Mandingo'),
('mao', 'ltr', 'mi', 'Maori'),
('mri', 'ltr', 'mi', 'Maori'),
('map', 'ltr', '', 'Austronesian(Other)'),
('mar', 'ltr', 'mr', 'Marathi'),
('mas', 'ltr', '', 'Masai'),
('may', 'ltr', 'ms', 'Malay'),
('msa', 'ltr', 'ms', 'Malay'),
('mdf', 'ltr', '', 'Moksha'),
('mdr', 'ltr', '', 'Mandar'),
('men', 'ltr', '', 'Mende'),
('mic', 'ltr', '', 'Mi''kmaq'),
('min', 'ltr', '', 'Minangkabau'),
('mis', 'ltr', '', 'Miscellaneouslanguages'),
('mkh', 'ltr', '', 'Mon-Khmer(Other)'),
('mlg', 'ltr', 'mg', 'Malagasy'),
('mlt', 'ltr', 'mt', 'Maltese'),
('mnc', 'ltr', '', 'Manchu'),
('mni', 'ltr', '', 'Manipuri'),
('mno', 'ltr', '', 'Manobolanguages'),
('moh', 'ltr', '', 'Mohawk'),
('mol', 'ltr', 'mo', 'Moldavian'),
('mon', 'ltr', 'mn', 'Mongolian'),
('mga', 'ltr', '', 'Irish Middle(900-1200)'),
('mos', 'ltr', '', 'Mossi'),
('mul', 'ltr', '', 'Multiple'),
('mun', 'ltr', '', 'Mundalanguages'),
('mus', 'ltr', '', 'Creek'),
('mwl', 'ltr', '', 'Mirandese'),
('mwr', 'ltr', '', 'Marwari'),
('mya', 'ltr', 'my', 'Burmese'),
('myn', 'ltr', '', 'Mayanlanguages'),
('myv', 'ltr', '', 'Erzya'),
('nah', 'ltr', '', 'Nahuatl'),
('nai', 'ltr', '', 'NorthAmericanIndian'),
('nap', 'ltr', '', 'Neapolitan'),
('nau', 'ltr', 'na', 'Nauru'),
('nav', 'ltr', 'nv', 'Navajo'),
('ndo', 'ltr', 'ng', 'Ndonga'),
('nds', 'ltr', '', 'LowGerman'),
('nep', 'ltr', 'ne', 'Nepali'),
('new', 'ltr', '', 'Newaria'),
('nia', 'ltr', '', 'Nias'),
('nic', 'ltr', '', 'Niger-Kordofanian(Other)s'),
('niu', 'ltr', '', 'Niuean'),
('nno', 'ltr', 'nn', 'Norwegian'),
('nob', 'ltr', 'nb', 'Norwegian'),
('nog', 'ltr', '', 'Nogai'),
('non', 'ltr', '', 'Norse'),
('nor', 'ltr', 'no', 'Norwegian'),
('nqo', 'ltr', '', 'N''ko'),
('nso', 'ltr', '', 'NorthernSotho'),
('nub', 'ltr', '', 'Nubianlanguages'),
('nwc', 'ltr', '', 'ClassicalNewari'),
('nya', 'ltr', 'ny', 'Chichewa;Chewa'),
('nym', 'ltr', '', 'Nyamwezi'),
('nyn', 'ltr', '', 'Nyankole'),
('nyo', 'ltr', '', 'Nyoro'),
('oci', 'ltr', 'oc', 'Occitan(post1500)'),
('oji', 'ltr', 'oj', 'Ojibwa'),
('ori', 'ltr', 'or', 'Oriya'),
('orm', 'ltr', 'om', 'Oromo'),
('osa', 'ltr', '', 'Osage'),
('oss', 'ltr', 'os', 'Ossetian'),
('oto', 'ltr', '', 'Otomianlanguages'),
('nbl', 'ltr', 'nr', 'Ndebele South'),
('nde', 'ltr', 'nd', 'Ndebele North'),
('ota', 'ltr', '', 'Turkish Ottoman(1500-1928)'),
('paa', 'ltr', '', 'Papuan(Other)papoues'),
('pag', 'ltr', '', 'Pangasinan'),
('pal', 'ltr', '', 'Pahlavi'),
('pam', 'ltr', '', 'Pampanga'),
('pan', 'ltr', 'pa', 'Panjabi;Punjabi'),
('pap', 'ltr', '', 'Papiamento'),
('pau', 'ltr', '', 'Palauan'),
('phi', 'ltr', '', 'Philippine(Other)philippines'),
('phn', 'ltr', '', 'Phoenician'),
('pli', 'ltr', 'pi', 'Pali'),
('pol', 'ltr', 'pl', 'Polish'),
('pon', 'ltr', '', 'Pohnpeian'),
('por', 'ltr', 'pt', 'Portuguese'),
('pra', 'ltr', '', 'Prakritlanguages'),
('pus', 'ltr', 'ps', 'Pushto'),
('que', 'ltr', 'qu', 'Quechua'),
('raj', 'ltr', '', 'Rajasthani'),
('rap', 'ltr', '', 'Rapanui'),
('rar', 'ltr', '', 'Rarotongan'),
('roa', 'ltr', 'R', 'omance(Other)romanes'),
('roh', 'ltr', 'rm', 'Raeto-Romance'),
('rom', 'ltr', '', 'Romany'),
('rum', 'ltr', 'ro', 'Romanian'),
('ron', 'ltr', 'ro', 'Romanian'),
('run', 'ltr', 'rn', 'Rundi'),
('rup', 'ltr', '', 'Aromanian'),
('rus', 'ltr', 'ru', 'Russian'),
('sad', 'ltr', '', 'Sandawe'),
('sag', 'ltr', 'sg', 'Sango'),
('sah', 'ltr', '', 'Yakut'),
('sai', 'ltr', '', 'SouthAmericanIndian(Other)'),
('sal', 'ltr', '', 'Salishanlanguages'),
('sam', 'ltr', '', 'SamaritanAramaic'),
('san', 'ltr', 'sa', 'Sanskrit'),
('sas', 'ltr', '', 'Sasak'),
('sat', 'ltr', '', 'Santali'),
('scc', 'ltr', 'sr', 'Serbian'),
('srp', 'ltr', 'sr', 'Serbian'),
('scn', 'ltr', '', 'Sicilian'),
('sco', 'ltr', '', 'Scots'),
('sel', 'ltr', '', 'Selkup'),
('peo', 'ltr', '', 'Persian Old(ca.600-400B.C.)'),
('sem', 'ltr', '', 'Semitic(Other)'),
('sgn', 'ltr', '', 'SignLanguages'),
('shn', 'ltr', '', 'Shan'),
('sid', 'ltr', '', 'Sidamo'),
('sin', 'ltr', 'si', 'Sinhala'),
('sio', 'ltr', '', 'Siouanlanguages'),
('sit', 'ltr', '', 'Sino-Tibetan(Other)'),
('sla', 'ltr', '', 'Slavic(Other)slaves'),
('slo', 'ltr', 'sk', 'Slovak'),
('slk', 'ltr', 'sk', 'Slovak'),
('slv', 'ltr', 'sl', 'Slovenian'),
('sma', 'ltr', '', 'SouthernSami'),
('sme', 'ltr', 'se', 'NorthernSami'),
('smi', 'ltr', '', 'Samilanguages(Other)'),
('smj', 'ltr', '', 'LuleSami'),
('smn', 'ltr', '', 'InariSami'),
('smo', 'ltr', 'sm', 'Samoan'),
('sms', 'ltr', '', 'SkoltSami'),
('sna', 'ltr', 'sn', 'Shona'),
('snd', 'ltr', 'sd', 'Sindhi'),
('snk', 'ltr', '', 'Soninke'),
('sog', 'ltr', '', 'Sogdian'),
('som', 'ltr', 'so', 'Somali'),
('son', 'ltr', '', 'Songhai'),
('spa', 'ltr', 'es', 'Spanish;Castilian'),
('srd', 'ltr', 'sc', 'Sardinian'),
('srn', 'ltr', '', 'SrananTogosranan'),
('srr', 'ltr', '', 'Serer'),
('ssa', 'ltr', '', 'Nilo-Saharan(Other)'),
('ssw', 'ltr', 'ss', 'Swati'),
('suk', 'ltr', '', 'Sukuma'),
('sun', 'ltr', 'su', 'Sundanese'),
('sus', 'ltr', '', 'Susu'),
('sux', 'ltr', '', 'Sumerian'),
('swa', 'ltr', 'sw', 'Swahili'),
('swe', 'ltr', 'sv', 'Swedish'),
('syr', 'ltr', '', 'Syriac'),
('tah', 'ltr', 'ty', 'Tahitian'),
('tai', 'ltr', '', 'Tai(Other)'),
('tam', 'ltr', 'ta', 'Tamil'),
('tat', 'ltr', 'tt', 'Tatar'),
('tel', 'ltr', 'te', 'Telugu'),
('tem', 'ltr', '', 'Timne'),
('sga', 'ltr', '', 'Irish Old(to900)'),
('sot', 'ltr', 'st', 'Sotho Southern'),
('ter', 'ltr', '', 'Tereno'),
('tet', 'ltr', '', 'Tetum'),
('tgk', 'ltr', 'tg', 'Tajik'),
('tgl', 'ltr', 'tl', 'Tagalog'),
('tha', 'ltr', 'th', 'Thai'),
('bod', 'ltr', 'bo', 'Tibetan'),
('tig', 'ltr', '', 'Tigre'),
('tir', 'ltr', 'ti', 'Tigrinya'),
('tiv', 'ltr', '', 'Tiv'),
('tkl', 'ltr', '', 'Tokelau'),
('tlh', 'ltr', '', 'Klingon'),
('tli', 'ltr', '', 'Tlingit'),
('tmh', 'ltr', '', 'Tamashek'),
('tog', 'ltr', '', 'Tonga(Nyasa)'),
('ton', 'ltr', 'to', 'Tonga(TongaIslands)'),
('tpi', 'ltr', '', 'TokPisin'),
('tsi', 'ltr', '', 'Tsimshian'),
('tsn', 'ltr', 'tn', 'Tswana'),
('tso', 'ltr', 'ts', 'Tsonga'),
('tuk', 'ltr', 'tk', 'Turkmen'),
('tum', 'ltr', '', 'Tumbuka'),
('tup', 'ltr', '', 'Tupilanguages'),
('tur', 'ltr', 'tr', 'Turkish'),
('tut', 'ltr', '', 'Altaic(Other)'),
('tvl', 'ltr', '', 'Tuvalu'),
('twi', 'ltr', 'tw', 'Twi'),
('tyv', 'ltr', '', 'Tuvinian'),
('udm', 'ltr', '', 'Udmurt'),
('uga', 'ltr', '', 'Ugaritic'),
('uig', 'ltr', 'ug', 'Uighur'),
('ukr', 'ltr', 'uk', 'Ukrainian'),
('umb', 'ltr', '', 'Umbundu'),
('und', 'ltr', '', 'Undetermined'),
('urd', 'ltr', 'ur', 'Urdu'),
('uzb', 'ltr', 'uz', 'Uzbek'),
('vai', 'ltr', '', 'Vai'),
('ven', 'ltr', 've', 'Venda'),
('vie', 'ltr', 'vi', 'Vietnamese'),
('vol', 'ltr', 'vo', 'VolapÃ‚Âk'),
('vot', 'ltr', '', 'Votic'),
('wak', 'ltr', '', 'Wakashanlanguages'),
('wal', 'ltr', '', 'Walamo'),
('war', 'ltr', '', 'Waray'),
('was', 'ltr', '', 'Washo'),
('wen', 'ltr', '', 'Sorbianlanguages'),
('wln', 'ltr', 'wa', 'Walloon'),
('wol', 'ltr', 'wo', 'Wolof'),
('xal', 'ltr', '', 'Kalmyk'),
('xho', 'ltr', 'xh', 'Xhosa'),
('yao', 'ltr', '', 'Yao'),
('yap', 'ltr', '', 'Yapese'),
('yid', 'ltr', 'yi', 'Yiddish'),
('yor', 'ltr', 'yo', 'Yoruba'),
('ypk', 'ltr', '', 'Yupiklanguages'),
('zap', 'ltr', '', 'Zapotec'),
('zen', 'ltr', '', 'Zenaga'),
('zha', 'ltr', 'za', 'Zhuang'),
('znd', 'ltr', '', 'Zande'),
('zul', 'ltr', 'zu', 'Zulu'),
('zun', 'ltr', '', 'Zuni'),
('zxx', 'ltr', '', 'Nolinguisticcontent');

# insert the default theme
INSERT INTO `themes` VALUES ('Transformable', '0.1', 'default', NOW(), 'This is the default Transformable theme and cannot be deleted as other themes inherit from it. Please do not alter this theme directly as it would complicate upgrading. Instead, create a new theme derived from this one.', 2);

# insert privileges, user groups and user group privileges
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (1, 'home', 'Home', NOW(), 'home/index.php', 10, 1);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (2, 'system', 'System configuration..', NOW(), 'system/index.php', 20, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (3, 'course_categories', 'Course category management: Create, edit, delete course categories.', NOW(), 'course_category/index.php', 30, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (4, 'users', 'User management: Create, edit, delete users.', NOW(), 'user/index.php', 40, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (5, 'language', 'Language management: Create, edit, delete, enable, disable languages.', NOW(), 'language/index.php', 50, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (6, 'translation', 'Translation: Translate all Transformable terms into other languages.', NOW(), 'translation/index.php', 60, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (7, 'updater', 'Updater: Install, create, edit updates.', NOW(), 'updater/index.php', 70, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (8, 'manage_tests', 'Tests management: Used by instructors to create, edit, delete course-related test questions and tests.', NOW(), 'tests/index.php?_course_id={COURSE_ID}', 80, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (9, 'file_manager', 'File Manager: Allows an instructor to upload and manage files for a course. Files can then be made available to students by linking them into content pages.', NOW(), 'file_manager/index.php?_course_id={COURSE_ID}', 90, 0);
INSERT INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (10, 'profile', 'Profile management: Edit profile, change password or email.', NOW(), 'profile/index.php', 100, 0);

INSERT INTO `user_groups` (`user_group_id`, `title`, `description`, `create_date`) VALUES (1, 'Administrator', 'Administrate users, user groups, languages and updates.', now());
INSERT INTO `user_groups` (`user_group_id`, `title`, `description`, `create_date`) VALUES (2, 'User', 'Regular user.', now());
INSERT INTO `user_groups` (`user_group_id`, `title`, `description`, `create_date`) VALUES (3, 'Translator', 'Translate Transformable terms into a foreign lanugage.', now());

INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 1, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 2, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 3, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 4, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 5, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 6, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 10, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (2, 1, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (2, 8, 2);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (2, 9, 2);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (2, 10, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (3, 1, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (3, 5, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (3, 6, 0);
INSERT INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (3, 10, 0);

# insert default atutor account
INSERT INTO `users` (`user_id`, `login`, `password`, `user_group_id`, `first_name`, `last_name`, `web_service_id`, `status`, `create_date`) VALUES (1, 'ATutor', '0cbab2aec26a53b0107487d43b1b8eb29384ad10', 2, 'ATutor', 'ATutor', '90c3cd6f656739969847f3a99ac0f3c7', 1, now());
