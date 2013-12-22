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

jimport('joomla.application.component.model');

class SeobossModelHelpdesk extends JBModel{

    public $UPDATE_SITE = "joomboss.com";

    function getRequests($code){
        require_once(dirname(__FILE__)."/../lib/Snoopy.class.php");
        $SnoopySeoBoss = new SnoopySeoBoss;
        $SnoopySeoBoss->fetchtext("http://{$this->UPDATE_SITE}/index.php?option=com_seobossupdater&task=helpdesk&code=$code&format=raw");
        $result = json_decode($SnoopySeoBoss->results, false);
        return $result->data;
    }

    function getRequest($code, $id){
        require_once(dirname(__FILE__)."/../lib/Snoopy.class.php");
        $SnoopySeoBoss = new SnoopySeoBoss;
        $SnoopySeoBoss->fetchtext("http://{$this->UPDATE_SITE}/index.php?option=com_seobossupdater&task=helpdesk_view_request&id=$id&code=$code&format=raw");
        $result = json_decode($SnoopySeoBoss->results, false);
        return $result->data;
    }

    function submitRequest($code, $subject, $body, $id=null){
        require_once(dirname(__FILE__)."/../lib/Snoopy.class.php");
        $SnoopySeoBoss = new SnoopySeoBoss;
        $vars = array("option"=>"com_seobossupdater",
        	"task"=>"helpdesk_submit",
            "format"=>"raw",
            "code"=>$code,
            "subject"=>$subject,
            "body"=>$body);
        if ($id){
            $vars["id"] = $id;
        }
        $SnoopySeoBoss->httpmethod = "POST";
        $SnoopySeoBoss->submit("http://{$this->UPDATE_SITE}/index.php", $vars);
        return $SnoopySeoBoss->results;
    }


}
