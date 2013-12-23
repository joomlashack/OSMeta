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
// No direct access
defined('_JEXEC') or die('Restricted access');

$features['com_k2'] = array(
    'name'=>'K2',
    'priority'=>1,
    'file'=>'K2_MetatagsContainer.php',
    'class'=>'K2_MetaTagsContainer',
    'params'=>array(array('option'=>'com_k2', 'view'=>'item'))
);
$extensions['ping'][] = array(
    'class'=>'TableK2Item',
    'file'=>'features/com_k2/ping.php',
    'function'=>'com_k2_get_url',
    'info'=>'K2 content item'
);
?>
