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


class SeobossModelBackups extends JBModel
{
  /**
  * Overridden constructor
  * @access	protected
  */
  function __construct() {
    parent::__construct();
  }

  public function getDump(){
    $dump = "";
    $db = JFactory::getDBO();
    
    $db->setQuery("SELECT domain, 
        `google_server`, 
        `hilight_keywords`, 
        `hilight_tag`, 
        `hilight_class`, 
        `hilight_skip`,
        `joomboss_registration_code`,
        `enable_google_ping`,
        `frontpage_meta`,
        `frontpage_title`,
        `frontpage_keywords`,
        `frontpage_description`,
        `frontpage_meta_title`
         FROM #__seoboss_settings");
    $row = $db->loadObject();
      $dump .= "UPDATE #__seoboss_settings SET
      domain=".$db->quote($row->domain) . ",
      google_server=".$db->quote($row->google_server) . ",
      hilight_keywords=".$db->quote($row->hilight_keywords) . ",
      hilight_tag=".$db->quote($row->hilight_tag) . ",
      hilight_class=".$db->quote($row->hilight_class) . ",
      hilight_skip=".$db->quote($row->hilight_skip) . ",
      joomboss_registration_code=".$db->quote($row->joomboss_registration_code) . ",
      enable_google_ping=".$db->quote($row->enable_google_ping) . ",
      frontpage_meta=".$db->quote($row->frontpage_meta) . ",
      frontpage_title=".$db->quote($row->frontpage_title) . ",
      frontpage_keywords=".$db->quote($row->frontpage_keywords) . ",
      frontpage_description=".$db->quote($row->frontpage_description) . ",
      frontpage_meta_title=".$db->quote($row->frontpage_meta_title) . ";\n";
    
    $db->setQuery("SELECT id, url, target, ext FROM #__seoboss_redirects");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_redirects
      (id, url, target, ext) VALUES (".$db->quote($row->id).", ".$db->quote($row->url).", ".$db->quote($row->target).", ".$db->quote($row->ext) ." ) ;\n";
    }
    $db->setQuery("SELECT id,
        item_id,
        item_type,
        title,
        title_tag,
        description
        FROM #__seoboss_metadata");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_metadata
      (id, item_id, item_type,
      title,
      title_tag,
      description)
      VALUES
      (".$db->quote($row->id).", ".
      $db->quote($row->item_id).", ".
      $db->quote($row->item_type).", ".
      $db->quote($row->title) .", ".
      $db->quote($row->title_tag) .", ".
      $db->quote($row->description) .");\n";
    }
    $db->setQuery("SELECT id, name, published, url, google_rank, google_rank_change, google_rank_change_date, sticky FROM #__seoboss_keywords");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_keywords
      (id, name, published, url, google_rank,
      google_rank_change, google_rank_change_date, sticky)
      VALUES
      (".$db->quote($row->id).",
      ".$db->quote($row->name).",
      ".$db->quote($row->published).",
      ".$db->quote($row->url) .",
      ".$db->quote($row->google_rank) .",
      ".$db->quote($row->google_rank_change) .",
      ".$db->quote($row->google_rank_change_date) .",
      ".$db->quote($row->sticky) .");\n";
    }
    $db->setQuery("SELECT item_id, item_type_id, keyword_id FROM #__seoboss_keywords_items");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_keywords_items
      (item_id, item_type_id, keyword_id)
      VALUES
      (".$db->quote($row->item_id).",
      ".$db->quote($row->item_type_id).",
      ".$db->quote($row->keyword_id) .");\n";
    }

    $db->setQuery("SELECT id, url FROM #__seoboss_urls");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_urls
      (id, url)
      VALUES
      (".$db->quote($row->id).",
      ".$db->quote($row->url) .");\n";
    }

    $db->setQuery("SELECT id, name, value FROM #__seoboss_default_tags");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_default_tags
      (id, name, value)
      VALUES
      (".$db->quote($row->id).",
       ".$db->quote($row->name).",
       ".$db->quote($row->value) .");\n";
    }

    $db->setQuery("SELECT id, url, canonical_url, action FROM #__seoboss_canonical_url");
    $data = $db->loadObjectList();
    foreach($data as $row){
      $dump .= "INSERT IGNORE INTO #__seoboss_canonical_url
      (id, url, canonical_url, action)
      VALUES
      (".$db->quote($row->id).",
       ".$db->quote($row->url).",
       ".$db->quote($row->canonical_url).",
       ".$db->quote($row->action) .");\n";
    }

    return $dump;
  }
  
  public function applyDump($dump){
    jimport("joomla.installer.helper");
    jimport("joomla.version");
    $version = new JVersion();
    if($version->RELEASE == "1.5"){
        $helper = new JInstallerHelper();
        $sql_statements = $helper->splitSql($dump);
        
    }else{
        //$helper = new JInstallerHelper();
        $sql_statements = JInstallerHelper::splitSql($dump);
    }
    $db = JFactory::getDBO();
    $count = 0;
    foreach($sql_statements as $statement){
      if(trim($statement)){
        $db->setQuery($statement);
        $db->query();
        $count++;
      }
    }
    return $count;
  }
}
