<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

require_once JPATH_COMPONENT . '/controller.php';

$app = JFactory::getApplication();

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    $task = JRequest::getCmd('task');
} else {
    $task = $app->input->getCmd('task');
}

$controller = JControllerLegacy::getInstance('OSMeta');
$controller->execute($task);
$controller->redirect();
