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
$features['com_cobalt'] = array(
    'name'=>'Cobalt records',
    'priority'=>1,
    'file'=>'Cobalt_MetatagsContainer.php',
    'class'=>'Cobalt_MetatagsContainer',
    'params'=>array(array('option'=>'com_cobalt'))
);
?>
