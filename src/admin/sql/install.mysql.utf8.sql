CREATE TABLE IF NOT EXISTS `#__osmeta_metadata` (
    `id` int(11) NOT NULL auto_increment,
    `item_id` int(11) NOT NULL,
    `item_type` int(11) NOT NULL,
    `title` text NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY  (`id`),
    UNIQUE KEY `UniqueItemIdAndItemType` (`item_id`,`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
