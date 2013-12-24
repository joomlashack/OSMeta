CREATE TABLE IF NOT EXISTS `#__osmeta_keywords` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `published` tinyint(4) NOT NULL default '1',
  `url` varchar(255) NOT NULL,
  `google_rank` int(11) NOT NULL,
  `google_rank_change` int(11) NOT NULL,
  `google_rank_change_date` datetime NOT NULL,
  `sticky` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__osmeta_keywords_items` (
  `item_id` int(11) NOT NULL,
  `item_type_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  UNIQUE KEY `item_id` (`item_id`,`item_type_id`,`keyword_id`),
  KEY `item_id_2` (`item_id`,`item_type_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__osmeta_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_tag` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UniqueItemIdAndItemType` (`item_id`,`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__osmeta_meta_extensions` (
`component` VARCHAR(50) NOT NULL ,
`name` VARCHAR(50) NOT NULL ,
`description` VARCHAR(255) NOT NULL ,
`enabled` TINYINT NOT NULL ,
`available` TINYINT NOT NULL ,
PRIMARY KEY ( `component`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__osmeta_meta_extensions` VALUES
  ('com_content-1.5','Joomla 1.5 Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0),
  ('com_content','Joomla Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0);

CREATE TABLE  IF NOT EXISTS `#__osmeta_canonical_url` (
`id` INT NOT NULL AUTO_INCREMENT ,
`url` VARCHAR(255) NOT NULL ,
`canonical_url` VARCHAR(255) NOT NULL ,
`action` TINYINT NOT NULL,
PRIMARY KEY ( `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
