<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, "3.0", "<"))
{
	/**
	 * Alias Class for JView in Joomla! < 3.0
	 *
	 * @since  1.0.0
	 */
	class OSView extends JView {}
}
else
{
	/**
	 * Alias Class for JViewLegacy in Joomla! >= 3.0
	 *
	 * @since  1.0.0
	 */
	class OSView extends JViewLegacy
	{
		/**
		 * Method to display a view with the Joomla 3 template.
		 *
		 * @param   string  $tpl  Template file
		 *
		 * @access	public
		 *
		 * @return  void
		 */
		public function display($tpl = null)
		{
			$this->setLayout("joomla3");

			parent::display($tpl);
		}
	}
}
