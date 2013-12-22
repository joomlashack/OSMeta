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

jimport('joomla.application.component.controller');
if (version_compare(JVERSION, "3.0", "ge")){
  class JBController extends JControllerLegacy{}
}else{
  class JBController extends JController{}
}
class SeobossController extends JBController{

  function redirectByURL(){
    $link = JRequest::getVar('url', '', 'get', 'string');
    $mainframe = JFactory::getApplication();
    $mainframe->redirect($link, $msg);
  }
  function saveMetadata(){
    $mainframe = JFactory::getApplication();

    $url = JRequest::getVar('url');
    $title = JRequest::getVar('seoboss_title');
    $meta_title = JRequest::getVar('seoboss_meta_title');
    $meta_keywords = JRequest::getVar('seoboss_meta_keywords');
    $meta_description = JRequest::getVar('seoboss_meta_description');

    require_once(dirname(__FILE__)."/../../administrator/components/com_seoboss/classes/MetatagsContainerFactory.php");
    $container = MetatagsContainerFactory::setMetadataByRequest($url,
          array("title_tag"=>$title,
              "metatitle"=>$meta_title,
              "metakeywords"=>$meta_keywords,
              "metadescription"=>$meta_description));

    $mainframe->redirect(JURI::base().($url?"?".$url:""));
  }
}
?>
