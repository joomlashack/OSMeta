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

$features['com_k2'] = array(
	'name' => 'K2',
	'priority' => 1,
	'file' => 'K2_MetatagsContainer.php',
	'class' => 'K2_MetaTagsContainer',
	'params' => array(array('option' => 'com_k2', 'view' => 'item'))
);

$extensions['ping'][] = array(
	'class' => 'TableK2Item',
	'file' => 'features/com_k2/ping.php',
	'function' => 'com_k2_get_url',
	'info' => 'K2 content item'
);
