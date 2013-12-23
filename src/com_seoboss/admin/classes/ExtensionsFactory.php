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
// No direct access
defined('_JEXEC') or die('Restricted access');

class ExtensionsFactory{

    static function getExtensions(){
        if (ExtensionsFactory::$extensions == null){
            $extensions = array();
            $directoryName = dirname(dirname(__FILE__)).'/features';
            $handle = opendir($directoryName);
            while (false !== ($file = readdir($handle))) {
                if (substr($file, strlen($file) - 4) == ".php"){
                    include $directoryName.'/'.$file;
                }
            }
            ExtensionsFactory::$extensions = $extensions;
        }
        return ExtensionsFactory::$extensions;
    }
    static $extensions = null;
}
