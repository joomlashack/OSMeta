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
$features['com_virtuemart'] = array(
    'name'=>'Virtuemart Products',
    'priority'=>1,
    'file'=>'VM_ProductMetatagsContainer.php',
    'class'=>'VM_ProductMetatagsContainer',
    'params'=>array(array('option'=>'com_virtuemart', 'page'=>'shop.product_details'))
);
?>
