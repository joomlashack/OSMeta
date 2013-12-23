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

$features['com_content-1.5:Article'] = array(
        'name'=>'Article',
        'priority'=>1,
        'file'=>'ArticleMetatagsContainer.php',
        'class'=>'ArticleMetatagsContainer',
        'params'=>array(array('option'=>'com_content', 'view'=>'article'),
            array('option'=>'com_content', 'view'=>'frontpage')));
$features['com_content-1.5:ArticleCategory'] = array(
	'priority'=>1,
        'name'=>'Article Category',
        'file'=>'ArticleCategoryMetatagsContainer.php',
        'class'=>'ArticleCategoryMetatagsContainer',
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
