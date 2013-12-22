<?php
/**
 * @category  Joomla Component
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// no direct access
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
