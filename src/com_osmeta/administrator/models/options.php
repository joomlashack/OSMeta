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

require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/model.php';

/**
 * Model Options
 *
 * @since  1.0.0
 */
class OSModelOptions extends OSModel
{
	/**
	 * Class constructor method
	 *
	 * @access	public
	 * @since   1.0.0
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get default tag list
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function getDefaultTags()
	{
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` from #__osmeta_default_tags");
	    $tags = $db->loadObjectList();

	    return $tags;
	}

	/**
	 * Delete default tag
	 *
	 * @param   int   $tagId  Tag ID
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function deleteDefaultTag($tagId = 0)
	{
	    $db = JFactory::getDBO();
	    $db->setQuery("DELETE FROM #__osmeta_default_tags WHERE id = " . $db->quote($tagId));
	    $db->query();
	}

	/**
	 * Get default tag
	 *
	 * @param   int   $tagId  Tag ID
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  Object
	 */
	public function getDefaultTag($tagId = 0)
	{
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT `id`, `name`, `value` FROM #__osmeta_default_tags WHERE id = " . $db->quote($tagId));

	    return $db->loadObject();
	}

	/**
	 * Add default tag
	 *
	 * @param   string   $name   Tag Name
	 * @param   string   $value  Tag Value
	 *
	 * @access	public
	 * @since   1.0.0
	 */
	public function addDefaultTag($name, $value)
	{
	    $db = JFactory::getDBO();
	    $db->setQuery("INSERT INTO #__osmeta_default_tags
	    				(`name`, `value`)
	    				VALUES
	    				(" . $db->quote($name) . "," . $db->quote($value) . ")");
	    $db->query();
	}

	/**
	 * Update default tag
	 *
	 * @param   int      $tagId  Tag ID
	 * @param   string   $name   Tag Name
	 * @param   string   $value  Tag Value
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function updateDefaultTag($id, $name, $value)
	{
	    $db = JFactory::getDBO();
	    $db->setQuery("UPDATE #__osmeta_default_tags SET
		    				`name`=" . $db->quote($name) . ", `value`=" . $db->quote($value) . " WHERE id=" . $db->quote($id));
	    $db->query();
	}

	/**
	 * Get Options (fixed options, for now)
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  Object
	 */
	public function getOptions()
	{
	    $options = new stdClass;

	    $options->domain = '';
	    $options->google_server = 'google.com';
	    $options->hilight_keywords = 1;
	    $options->hilight_tag = 'strong';
	    $options->hilight_class = 'keyword';
	    $options->hilight_skip = 'textarea';
	    $options->joomboss_registration_code = '';
	    $options->enable_google_ping = 0;
	    $options->frontpage_meta = 0;
	    $options->frontpage_title = '';
	    $options->frontpage_keywords = '';
	    $options->frontpage_description = '';
	    $options->frontpage_meta_title = '';
	    $options->sa_enable = 0;
	    $options->sa_users = 'admin';
	    $options->max_description_length = 255;

	    return $options;
	}

	/**
	 * Ping Status
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  array
	 */
    public function getPingStatus()
    {
        $db = JFactory::getDBO();
        $db->setQuery("SELECT `id`, `date`, `title`, `url`, `response_code`, `response_text`
        	FROM #__osmeta_ping_status
        	ORDER BY `date` DESC LIMIT 0,10");

        return $db->loadObjectList();
    }
}
