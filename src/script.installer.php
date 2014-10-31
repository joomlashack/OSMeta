<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library/installer/include.php';
if (file_exists($includePath)) {
    require_once $includePath;
} else {
    require_once __DIR__ . '/library/installer/include.php';
}

/**
 * OSMeta Installer Script
 *
 * @since  1.0
 */
class Com_OSMetaInstallerScript extends AllediaInstallerAbstract
{
    /**
     * Method to run after an install/update method
     *
     * @return void
     */
    public function postFlight($type, $parent)
    {
        parent::postFlight($type, $parent);

        $db = JFactory::getDBO();

        // Remove the old pkg_osmeta, if existent
        $query = 'DELETE FROM `#__extensions` WHERE `type`="package" AND `element`="pkg_osmeta"';
        $db->setQuery($query);
        $db->execute();

        return true;
    }
}
