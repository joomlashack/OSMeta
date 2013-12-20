<?php
/*------------------------------------------------------------------------
# SEO Boss Pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!function_exists('jb_com_install')){
function jb_com_install()
{
  $db = JFactory::getDBO();
  //Check that database schema is consistent
  // seoboss_metadata
  $db->setQuery("SHOW COLUMNS FROM #__seoboss_metadata WHERE field LIKE 'title_tag'");
  if (!$db->loadResult()){
    $db->setQuery('ALTER TABLE #__seoboss_metadata ADD `title_tag` text NOT NULL');
    $db->query();
  }
  // seoboss_keywords_items
  $db->setQuery("SHOW INDEX FROM #__seoboss_keywords_items WHERE Key_name LIKE 'item_id_2'");
  if (!$db->loadResult()){
    $db->setQuery('ALTER TABLE  `#__seoboss_keywords_items` ADD INDEX  `item_id_2` (  `item_id`, `item_type_id` )');
    $db->query();
  }
  $db->setQuery("SHOW INDEX FROM #__seoboss_keywords_items WHERE Key_name LIKE 'keyword_id'");
  if (!$db->loadResult()){
    $db->setQuery('ALTER TABLE  `#__seoboss_keywords_items` ADD INDEX  `keyword_id` (  `keyword_id` )');
    $db->query();
  }
  // seoboss_urls
    $db->setQuery("SHOW TABLES LIKE '#__seoboss_urls'");
    if (!$db->loadAssoc()){
      $db->setQuery('CREATE TABLE  IF NOT EXISTS `#__seoboss_urls` (
                      `id` INT NOT NULL AUTO_INCREMENT ,
                      `url` VARCHAR( 255 ) NOT NULL ,
                      PRIMARY KEY (  `id` )
                  )');
      $db->query();
    }
    $db->setQuery("SHOW TABLES LIKE '#__seoboss_meta_extensions'");
    if (!$db->loadAssoc()){
      $db->setQuery(
          'CREATE TABLE  IF NOT EXISTS `#__seoboss_meta_extensions` (
          `component` VARCHAR( 50 ) NOT NULL ,
          `name` VARCHAR( 50 ) NOT NULL ,
          `description` VARCHAR( 255 ) NOT NULL ,
          `enabled` TINYINT NOT NULL ,
          `available` TINYINT NOT NULL ,
          PRIMARY KEY (  `component` )
          )');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_meta_extensions` WHERE field LIKE 'description'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_meta_extensions ADD `description` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery(
        "INSERT IGNORE INTO `#__seoboss_meta_extensions` VALUES
        ('com_content-1.5','Joomla 1.5 Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0),
        ('com_content','Joomla Articles', 'Manage metadata for standard Joomla Articles and Categories',1,0),
        ('com_menu','Menu Items','Manage metadata for Menu Items. Be careful with this plugin, because it can override the metadata from other components.',0,0),
        ('com_k2','K2', 'K2 content items',1,0),
        ('com_virtuemart','VirtueMart', 'VirtueMart products and categories.',1,0),
        ('com_virtuemart2','VirtueMart2', 'VirtueMart products and categories.',1,0),
        ('com_mt','Mosets Tree','Mosets Tree Categories and Links.',1,0),
        ('com_joomsport','JoomSport','JoomSport content items.',1,0),
        ('com_cobalt','Cobalt CCK','Cobalt CCK content items.',1,0),
        ('com_hikashop','Hikashop','Hikashop items.',1,0)");
    $db->query();
//seoboss_settings check
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'frontpage_meta'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `frontpage_meta` TINYINT NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'frontpage_title'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `frontpage_title` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'frontpage_keywords'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `frontpage_keywords` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'frontpage_description'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `frontpage_description` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'frontpage_meta_title'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `frontpage_meta_title` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'sa_enable'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `sa_enable` tinyint(4) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'sa_users'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `sa_users` VARCHAR( 255 ) NOT NULL');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_settings` WHERE field LIKE 'max_description_length'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_settings ADD `max_description_length` int(11) NOT NULL default \'255\'');
      $db->query();
    }
    $db->setQuery("SHOW TABLES LIKE '#__seoboss_canonical_url'");
    if (!$db->loadAssoc()){
      $db->setQuery(
        'CREATE TABLE  IF NOT EXISTS `#__seoboss_canonical_url` (
`id` INT NOT NULL AUTO_INCREMENT ,
`url` VARCHAR( 255 ) NOT NULL ,
`canonical_url` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
');
      $db->query();
    }
    $db->setQuery("SHOW COLUMNS FROM `#__seoboss_canonical_url` WHERE field LIKE 'action'");
    if (!$db->loadResult()){
      $db->setQuery('ALTER TABLE #__seoboss_canonical_url ADD `action` TINYINT NOT NULL');
      $db->query();
    }
//Update Core version
    $db->setQuery("insert into `#__seoboss_client_features`
                    (`name`, `version`, `build`)
                    VALUES
                    ('SEOBoss', '1.4r23', '33')
                   ON DUPLICATE KEY
                    UPDATE `version`='1.4r23', `build`='33';");
    $db->query('');
//Update Host Name
$uri = JURI::getInstance();
$host = $uri->getHost();

$db->setQuery ("UPDATE #__seoboss_settings SET domain=".$db->quote($host) );
$db->query();

$db->setQuery("UPDATE #__seoboss_settings SET joomboss_registration_code='25ce8bfd8e4c2dec4effc6d92b9eb152'");$db->query();

//Commented this out due to performance issues.
/*require_once("classes/MetatagsContainerFactory.php");
$cc = MetatagsContainerFactory::getContainer("com_content:Article", false);

$db->setQuery("SELECT id, metakey from #__content ");
$rows = $db->loadObjectList();
foreach($rows as $row){
    $cc->saveKeywords($row->metakey, $row->id);
}
*/

//Enable plugins - for Joomla 2.5:
jimport("joomla.version");
$version = new JVersion();
if(version_compare($version->RELEASE, "1.7", "ge") ){
    $db->setQuery("UPDATE #__extensions set enabled='1' WHERE `element`='seoboss_render' AND `type`='plugin'");
    $db->query();
    $db->setQuery("UPDATE #__extensions set enabled='1' WHERE `element`='seoboss_content' AND `type`='plugin'");
    $db->query();
}

require_once JPATH_BASE."/components/com_seoboss/classes/MetatagsContainerFactory.php";
MetatagsContainerFactory::refreshFeatures();

?>
<div class="header"><?php echo JText::_('SEO_INSTALL_CONGRATULATION_TITLE'); ?></div>
<p>
<?php echo JText::_('SEO_INSTALL_CONGRATULATION_DESC'); ?>
</p>
<?php
return true;
}
}
if(!function_exists('com_install')){
    function com_install(){
        return jb_com_install();
    }
}
?>
