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

jimport('joomla.application.component.model');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, "3.0", "<"))
{
	/**
	 * Alias Class for JModel in Joomla! < 3.0
	 *
	 * @since  1.0.0
	 */
	class OSModel extends JModel {}
}
else
{
	/**
	 * Alias Class for JModelLegacy in Joomla! >= 3.0
	 *
	 * @since  1.0.0
	 */
	class OSModel extends JModelLegacy {}
}
