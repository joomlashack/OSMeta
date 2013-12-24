<?php
/**
 * @category   Joomla Component
 * @package    Osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
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
}
