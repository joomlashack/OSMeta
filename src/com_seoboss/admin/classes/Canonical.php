<?php
/*------------------------------------------------------------------------
# SEO Boss pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class SeobossCanonicalURL{
  public static $ACTION_CANONICAL=0;
  public static $ACTION_REDIRECT=1;
  public static $ACTION_NOINDEX=2;
  public function setCanonicalURL($originalURL, $canonicalURL, 
      $action=0){
    $db = JFactory::getDBO();
    $db->setQuery("
        DELETE FROM `#__seoboss_canonical_url` 
        WHERE url=".$db->quote($originalURL));
    $db->query();

    $db->setQuery("INSERT INTO `#__seoboss_canonical_url` 
        (url, canonical_url, action) VALUES (".
      $db->quote($originalURL). ",". 
      $db->quote($canonicalURL) .",". 
      $db->quote($action) . ")");
    $db->query();
  }

  public function getCanonicalURL($originalURL){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT canonical_url, action FROM `#__seoboss_canonical_url` WHERE url=".$db->quote($originalURL));
    return $db->loadObject();
  }
  
  public function getCanonicalURLById($id){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, url, canonical_url, action FROM `#__seoboss_canonical_url` WHERE id=".$db->quote($id));
    return $db->loadObject();
  }
  
  public function setCanonicalURLById($id, $url, $canonical_url, $action=0){
    $db = JFactory::getDBO();
    if($id){
      $db->setQuery("UPDATE `#__seoboss_canonical_url` 
          SET url=".$db->quote($url).", 
          canonical_url=".$db->quote($canonical_url).",
          action=".$db->quote($action)."  WHERE id=".$db->quote($id));
    }else{
      $db->setQuery("INSERT INTO `#__seoboss_canonical_url` (url, canonical_url,action) 
          VALUES (".$db->quote($url).", ".$db->quote($canonical_url).", ".$db->quote($action)." ) ");
    }
    $db->query();
  }
  
  public function deleteCanonicalURLById($id){
    $db = JFactory::getDBO();
    $db->setQuery("DELETE FROM `#__seoboss_canonical_url` WHERE id=".$db->quote($id));
    $db->query();
  }
  
  public function getCanonicalURLs(){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, url, canonical_url, action FROM `#__seoboss_canonical_url` ");
    return $db->loadObjectList();
  }
}