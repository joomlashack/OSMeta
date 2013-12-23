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
