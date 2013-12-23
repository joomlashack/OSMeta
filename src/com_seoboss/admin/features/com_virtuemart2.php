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
$features['com_virtuemart2:Product'] = array(
    'name'=>'Virtuemart Products',
    'priority'=>1,
    'file'=>'VM2_ProductMetatagsContainer.php',
    'class'=>'VM2_ProductMetatagsContainer',
    'params'=>array(array('option'=>'com_virtuemart', 'view'=>'product'))
);
$features['com_virtuemart2:Category'] = array(
    'name'=>'Virtuemart Categories',
    'priority'=>1,
    'file'=>'VM2_CategoryMetatagsContainer.php',
    'class'=>'VM2_CategoryMetatagsContainer',
    'params'=>array(array('option'=>'com_virtuemart', 'view'=>'category'))
);

?>
