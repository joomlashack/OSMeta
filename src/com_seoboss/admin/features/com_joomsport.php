<?php
/*------------------------------------------------------------------------
# SEO Boss pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

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
