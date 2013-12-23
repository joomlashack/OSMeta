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
$features['com_mt:Category'] = array(
        'name'=>'Mosets Tree Category',
        'priority'=>1,
        'file'=>'MosetsTree_CategoryMetatagsContainer.php',
        'class'=>'MosetsTree_CategoryMetatagsContainer',
        'params'=>array(array('option'=>'com_mt', 'task'=>'viewcategory'))
   );

$features['com_mt:Link'] =
    array(
        'name'=>'Mosets Tree Link',
        'priority'=>1,
        'file'=>'MosetsTree_LinkMetatagsContainer.php',
        'class'=>'MosetsTree_LinkMetatagsContainer',
        'params'=>array(array('option'=>'com_mt', 'task'=>'viewlink'))
   );
?>
