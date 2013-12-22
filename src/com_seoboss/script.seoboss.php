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

class com_seobossInstallerScript
{
	function install($extension)
	{
		require_once dirname(__FILE__) . "/install.seoboss.php";

		return jb_com_install();
	}

	function update($extension)
	{
		return $this->install();
	}
}