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

// no direct access
defined('_JEXEC') or die('Restricted access');

class OSModelOptions extends OSModel
{
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

	}

	/* Default tags feature */
	public function getDefaultTags(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` from #__osmeta_default_tags");
	    $tags = $db->loadObjectList();
	    return $tags;
	}
	public function deleteDefaultTag($tagId){
	    $db = JFactory::getDBO();
	    $db->setQuery("DELETE FROM #__osmeta_default_tags WHERE id=".$db->quote($tagId));
	    $db->query();
	}

	public function getDefaultTag($tagId){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` FROM #__osmeta_default_tags WHERE id=".$db->quote($tagId));
	    return $db->loadObject();
	}
	public function addDefaultTag($name, $value){
	    $db = JFactory::getDBO();
	    $db->setQuery("INSERT INTO #__osmeta_default_tags
	    				(`name`, `value`)
	    				VALUES
	    				(".$db->quote($name).",".$db->quote($value).")");
	    $db->query();
	}
	public function updateDefaultTag($id, $name, $value){
	    $db = JFactory::getDBO();
	    $db->setQuery("UPDATE #__osmeta_default_tags SET
		    				`name`=".$db->quote($name).", `value`=".$db->quote($value)." WHERE id=".$db->quote($id));
	    $db->query();
	}

	public function getOptions(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT * FROM #__osmeta_settings");
	    return $db->loadObject();
	}

    public function getPingStatus(){
        $db = JFactory::getDBO();
        $db->setQuery("SELECT `id`, `date`, `title`, `url`, `response_code`, `response_text`
          FROM #__osmeta_ping_status
          ORDER BY `date` DESC LIMIT 0,10");
        return $db->loadObjectList();
    }

    public function saveOptions($data){
      $db = JFactory::getDBO();
      $query = "UPDATE #__osmeta_settings SET ";
      $updates = array();
      foreach($data as $key=>$value){
        $updates[] = "$key=".$db->quote($value);
      }
      $query = $query . implode(",", $updates);
      $db->setQuery($query);
      $db->query();
    }
}
