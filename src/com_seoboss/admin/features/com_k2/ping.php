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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
function com_k2_get_url(&$article, $isNew){
    $url=null;
    $helperPath = dirname(__FILE__)."/../../../../../components/com_k2/helpers/route.php";
    if(is_file($helperPath)){
        require_once $helperPath;
        $helper = new K2HelperRoute();
        $url = $helper->getItemRoute($article->id, $article->catid);
        $app    = JApplication::getInstance('site');
        $router = $app->getRouter();
        $uri = $router->build($url);
        $url = $uri->toString();
        if(strpos($url, "/administrator") === 0){
            $url = substr($url, strlen("/administrator"));
        }
    }
    return $url;
}
function com_k2_get_rss_url(){
    return "/index.php?option=com_k2&view=itemlist&format=feed&type=rss";
}
