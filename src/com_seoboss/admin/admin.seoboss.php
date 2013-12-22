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
defined('_JEXEC') or die('Restricted access');

JLoader::register('JBView', JPATH_COMPONENT."/views/view.php");
JLoader::register('JBModel', JPATH_COMPONENT."/models/model.php");
JTable::addIncludePath(JPATH_COMPONENT.'/tables');

if (!defined('DS')){
   define('DS', '/');
}

if (!function_exists('json_decode')) {
  function json_decode($content, $assoc=false){
    require_once 'classes/JSON.php';
    if ($assoc){
      $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    } else {
      $json = new Services_JSON;
    }
    return $json->decode($content);
  }
}

require_once(JPATH_COMPONENT.DS.'controller.php');
$controller = new SeobossController(array('default_task' => 'panel'));
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

theme();

function theme(){
	$document = JFactory::getDocument();
	$document->addStyleDeclaration("
.seoboss_menu_item{
padding:10px;
text-decoration:underline;
}
a.seoboss_menu_item:hover{
background-color:#cccccc;
}
.active_seoboss_menu_item{
background-color:#cccccc;
}
.icon-48-joomboss_update_manager{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-update-manager.png);
}
.icon-48-joomboss_backup{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-backup.png);
}
.icon-48-joomboss_settings{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-settings.png);
}
.icon-48-joomboss_panel{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-settings.png);
}
.icon-48-joomboss_metatag{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-metatag.png);
}
.icon-48-joomboss_keywords{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-keywords.png);
}
.icon-16-joomboss_settings{
background-image: url(".JURI::base()."components/com_seoboss/images/16x16-settings.png);
}
.icon-48-joomboss_redirect{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-redirect.png);
}
.icon-48-joomboss_html{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-html.png);
}
.icon-48-joomboss{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-jb.png);
}
.icon-48-helpdesk{
background-image: url(".JURI::base()."components/com_seoboss/images/48x48-helpdesk.png);
}
	");
}


?>
