<?php
/**
 * @category  Joomla Component
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class OsmetaCanonicalURL{
  public static $ACTION_CANONICAL=0;
  public static $ACTION_REDIRECT=1;
  public static $ACTION_NOINDEX=2;
  public function setCanonicalURL($originalURL, $canonicalURL,
      $action=0){
    $db = JFactory::getDBO();
    $db->setQuery("
        DELETE FROM `#__osmeta_canonical_url`
        WHERE url=".$db->quote($originalURL));
    $db->query();

    $db->setQuery("INSERT INTO `#__osmeta_canonical_url`
        (url, canonical_url, action) VALUES (".
      $db->quote($originalURL). ",".
      $db->quote($canonicalURL) .",".
      $db->quote($action) . ")");
    $db->query();
  }

  public function getCanonicalURL($originalURL){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT canonical_url, action FROM `#__osmeta_canonical_url` WHERE url=".$db->quote($originalURL));
    return $db->loadObject();
  }

  public function getCanonicalURLById($id){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, url, canonical_url, action FROM `#__osmeta_canonical_url` WHERE id=".$db->quote($id));
    return $db->loadObject();
  }

  public function setCanonicalURLById($id, $url, $canonical_url, $action=0){
    $db = JFactory::getDBO();
    if ($id){
      $db->setQuery("UPDATE `#__osmeta_canonical_url`
          SET url=".$db->quote($url).",
          canonical_url=".$db->quote($canonical_url).",
          action=".$db->quote($action)."  WHERE id=".$db->quote($id));
    }else{
      $db->setQuery("INSERT INTO `#__osmeta_canonical_url` (url, canonical_url,action)
          VALUES (".$db->quote($url).", ".$db->quote($canonical_url).", ".$db->quote($action).") ");
    }
    $db->query();
  }

  public function deleteCanonicalURLById($id){
    $db = JFactory::getDBO();
    $db->setQuery("DELETE FROM `#__osmeta_canonical_url` WHERE id=".$db->quote($id));
    $db->query();
  }

  public function getCanonicalURLs(){
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, url, canonical_url, action FROM `#__osmeta_canonical_url` ");
    return $db->loadObjectList();
  }
}
