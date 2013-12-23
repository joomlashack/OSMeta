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
// No direct access
defined('_JEXEC') or die('Restricted access');


class SeobossModelOptions extends JBModel
{
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

	}

	public function getRegistrationCode(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT joomboss_registration_code FROM #__seoboss_settings");
	    $code = $db->loadResult();
	    return $code;
	}

	public function setRegistrationCode($code){
	    $db = JFactory::getDBO();
	    $db->setQuery("UPDATE #__seoboss_settings SET joomboss_registration_code=".$db->quote($code));
	    $db->query();

	    $db->setQuery("SELECT update_site_id
	                   FROM `#__update_sites`
	                   WHERE `location` LIKE 'http://joomboss.com/index.php?option=com_seobossupdater&task=update_server&product=SEOBoss&%'");
	    $seoboss_update_site_id = $db->loadResult();
	    if ($seoboss_update_site_id){
	        $db->setQuery("UPDATE `#__update_sites`
	                        SET `location`=".
	                        $db->quote("http://joomboss.com/index.php?option=com_seobossupdater&task=update_server&product=SEOBoss&code=$code&format=extension.xml")."
	                        WHERE update_site_id=".$db->quote($seoboss_update_site_id));
	        $db->query();
	    }
	}
	/* Default tags feature */
	public function getDefaultTags(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` from #__seoboss_default_tags");
	    $tags = $db->loadObjectList();
	    return $tags;
	}
	public function deleteDefaultTag($tagId){
	    $db = JFactory::getDBO();
	    $db->setQuery("DELETE FROM #__seoboss_default_tags WHERE id=".$db->quote($tagId));
	    $db->query();
	}

	public function getDefaultTag($tagId){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` FROM #__seoboss_default_tags WHERE id=".$db->quote($tagId));
	    return $db->loadObject();
	}
	public function addDefaultTag($name, $value){
	    $db = JFactory::getDBO();
	    $db->setQuery("INSERT INTO #__seoboss_default_tags
	    				(`name`, `value`)
	    				VALUES
	    				(".$db->quote($name).",".$db->quote($value).")");
	    $db->query();
	}
	public function updateDefaultTag($id, $name, $value){
	    $db = JFactory::getDBO();
	    $db->setQuery("UPDATE #__seoboss_default_tags SET
		    				`name`=".$db->quote($name).", `value`=".$db->quote($value)." WHERE id=".$db->quote($id));
	    $db->query();
	}

	public function getOptions(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT * FROM #__seoboss_settings");
	    return $db->loadObject();
	}

    public function getPingStatus(){
        $db = JFactory::getDBO();
        $db->setQuery("SELECT `id`, `date`, `title`, `url`, `response_code`, `response_text`
          FROM #__seoboss_ping_status
          ORDER BY `date` DESC LIMIT 0,10");
        return $db->loadObjectList();
    }

    public function saveOptions($data){
      $db = JFactory::getDBO();
      $query = "UPDATE #__seoboss_settings SET ";
      $updates = array();
      foreach($data as $key=>$value){
        $updates[] = "$key=".$db->quote($value);
      }
      $query = $query . implode(",", $updates);
      $db->setQuery($query);
      $db->query();
    }
}
