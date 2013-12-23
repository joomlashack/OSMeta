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

// No direct access
defined('_JEXEC') or die('Restricted access');

$features['com_hikashop'] = array(
    'name'=>'Hikashop',
    'priority'=>1,
    'file'=>'Hikashop_MetatagsContainer.php',
    'class'=>'Hikashop_MetaTagsContainer',
    'params'=>array(array('option'=>'com_hikashop'))
);
?>
