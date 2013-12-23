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

$features['com_joomsport:Team'] = array(
	    'name'=>'JoomSport team page',
	    'priority'=>1,
	    'file'=>'JS_Team_MetatagsContainer.php',
	    'class'=>'JS_team_MetaTagsContainer',
        'params'=>array(array('option'=>'com_joomsport', 'view'=>'team')));

$features['com_joomsport:Player']=array(
		'name'=>'JoomSport player page',
		'priority'=>1,
	    'file'=>'JS_Player_MetatagsContainer.php',
	    'class'=>'JS_Player_MetaTagsContainer',
	    'params'=>array(array('option'=>'com_joomsport', 'view'=>'player'))
	);

$extensions['ping'][] = array(
    'class'=>'TableJSItem',
    'file'=>'features/com_js/ping.php',
    'function'=>'com_js_get_url',
    'info'=>'JoomSport content item'
);
?>
