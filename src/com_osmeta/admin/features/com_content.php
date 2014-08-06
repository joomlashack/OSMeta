<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
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
