CREATE TABLE IF NOT EXISTS `#__seoboss_keywords` (
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

CREATE TABLE IF NOT EXISTS `#__seoboss_keywords_items` (
  `item_id` int(11) NOT NULL,
  `item_type_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  UNIQUE KEY `item_id` (`item_id`,`item_type_id`,`keyword_id`),
  KEY `item_id_2` (`item_id`,`item_type_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__seoboss_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_tag` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UniqueItemIdAndItemType` (`item_id`,`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__seoboss_redirects` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` int(11) NOT NULL default '0',
  `ext` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__seoboss_settings` (
  `domain` varchar(255) NOT NULL,
  `google_server` varchar(255) NOT NULL,
  `hilight_keywords` tinyint(4) NOT NULL,
  `hilight_tag` varchar(20) NOT NULL,
  `hilight_class` varchar(50) NOT NULL,
  `hilight_skip` varchar(255) NOT NULL,
  `joomboss_registration_code` varchar(32) NOT NULL,
  `enable_google_ping` tinyint(4) NOT NULL,
  `frontpage_meta` TINYINT NOT NULL ,
  `frontpage_title` VARCHAR( 255 ) NOT NULL ,
  `frontpage_keywords` VARCHAR( 255 ) NOT NULL ,
  `frontpage_description` VARCHAR( 255 ) NOT NULL,
  `frontpage_meta_title` VARCHAR( 255 ) NOT NULL,
  `sa_enable` tinyint(4) NOT NULL,
  `sa_users` VARCHAR( 255 ) NOT NULL,
  `max_description_length` int(11) NOT NULL default '255'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__seoboss_settings` (`domain`, `google_server`, `hilight_keywords`, `hilight_tag`, `hilight_class`, `hilight_skip`, `sa_enable`, `sa_users`) VALUES
('', 'google.com', 1, 'strong', 'keyword', 'textarea', '0', 'admin');

CREATE TABLE  IF NOT EXISTS `#__seoboss_client_features` (
`name` VARCHAR( 50 ) NOT NULL ,
`version` VARCHAR( 16 ) NOT NULL ,
`build` INT NOT NULL,
`minor_version` INT NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL ,
`status` VARCHAR(50) NOT NULL,
UNIQUE (
`name`
)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__seoboss_files_to_delete` (
`feature` VARCHAR( 50 ) NOT NULL ,
`file` VARCHAR( 255 ) NOT NULL ,
UNIQUE (
`file`
)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__seoboss_default_tags` (
    `id` int(11) NOT NULL auto_increment,
    `name` VARCHAR( 255 ) NOT NULL ,
    `value` VARCHAR( 255 ) NOT NULL ,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__seoboss_ping_status` (
`id` INT NOT NULL AUTO_INCREMENT ,
`date` DATETIME NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
`response_code` VARCHAR( 10 ) NOT NULL ,
`response_text` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__seoboss_urls` (
`id` INT NOT NULL AUTO_INCREMENT ,
`url` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__seoboss_meta_extensions` (
`component` VARCHAR( 50 ) NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL ,
`enabled` TINYINT NOT NULL ,
`available` TINYINT NOT NULL ,
PRIMARY KEY (  `component` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__seoboss_meta_extensions` VALUES
  ('com_content-1.5','Joomla 1.5 Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0),
  ('com_content','Joomla Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0),
  ('com_menu','Menu Items','Manage metadata for Menu Items. Be careful with this plugin, because it can override the metadata from other components.',0,0),
  ('com_k2','K2', 'K2 content items',1,0),
  ('com_virtuemart','VirtueMart', 'VirtueMart products and categories.',1,0),
  ('com_virtuemart2','VirtueMart2', 'VirtueMart products and categories.',1,0),
  ('com_mt','Mosets Tree','Mosets Tree Categories and Links.',1,0),
  ('com_joomsport','JoomSport','JoomSport content items.',1,0),
  ('com_cobalt','Cobalt CCK','Cobalt CCK content items.',1,0),
  ('com_hikashop','Hikashop','Hikashop items.',1,0);

CREATE TABLE  IF NOT EXISTS `#__seoboss_canonical_url` (
`id` INT NOT NULL AUTO_INCREMENT ,
`url` VARCHAR( 255 ) NOT NULL ,
`canonical_url` VARCHAR( 255 ) NOT NULL ,
`action` TINYINT NOT NULL,
PRIMARY KEY (  `id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
