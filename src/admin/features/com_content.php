<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$features['com_content:Article'] = array(
    'name' => 'Article',
    'priority' => 1,
    'file' => 'OSArticleMetatagsContainer.php',
    'class' => 'OSArticleMetatagsContainer',
        'params' => array(array('option' => 'com_content', 'view' => 'article'),
            array('option' => 'com_content', 'view' => 'frontpage'),
            array('option' => 'com_content', 'view' => 'featured'))
);

$features['com_content:ArticleCategory'] = array(
    'name' => 'Article Category',
    'priority' => 1,
    'file' => 'OSArticleCategoryMetatagsContainer.php',
    'class' => 'OSArticleCategoryMetatagsContainer',
        'params' => array(array('option' => 'com_content', 'view' => 'category'))
);
