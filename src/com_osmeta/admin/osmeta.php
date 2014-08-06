<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

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
