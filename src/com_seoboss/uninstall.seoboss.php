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

function jb_com_uninstall()
{
	?>
	<div class="header"><?php echo JText::sprintf('SEO_REMOVED_TITLE'); ?></div>
	<p><?php echo JText::sprintf('SEO_REMOVED_DESC'); ?></p>
	<?php

	return true;
}

if (!function_exists("com_uninstall"))
{
	function com_uninstall()
	{
		return jb_com_uninstall();
	}
}