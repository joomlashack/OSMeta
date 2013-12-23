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

require_once JPATH_COMPONENT . '/controller.php';

JLoader::register('OSView', JPATH_COMPONENT . "/views/view.php");
JLoader::register('OSModel', JPATH_COMPONENT . "/models/model.php");

$app = JFactory::getApplication();

if (version_compare(JVERSION, '3.0', '>='))
{
	$task = $app->input->getCmd('task');
}
else
{
	$task = JRequest::getCmd('task');
}

$controller = OSController::getInstance('OSMeta');
$controller->execute($task);
$controller->redirect();