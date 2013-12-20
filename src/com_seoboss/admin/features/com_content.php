<?php
/*------------------------------------------------------------------------
# SEO Boss Pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$features['com_content:Article'] = array(
        'name'=>'Article',
        'priority'=>1,
        'file'=>'ArticleMetatagsContainer.php',
        'class'=>'ArticleMetatagsContainer',
            'params'=>array(array('option'=>'com_content', 'view'=>'article'),
                array('option'=>'com_content', 'view'=>'frontpage'),
                array('option'=>'com_content', 'view'=>'featured'))
        );

$features['com_content:ArticleCategory2']=
        array(
        'name'=>'Article Category',
        'priority'=>1,
        'file'=>'ArticleCategoryMetatagsContainer2.php',
        'class'=>'ArticleCategoryMetatagsContainer2',
            'params'=>array(array('option'=>'com_content', 'view'=>'category'))
);

//Add info for Search Engines ping feature
$extensions['ping'][] = array(
    'class'=>'JTableContent',
    'file'=>'features/com_content/ping.php',
    'function'=>'com_content_get_url',
    'rss_function'=>'com_content_get_rss_url',
    'info'=>'Standard Joomla articles'
);
?>
