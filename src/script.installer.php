<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library/Installer/include.php';
if (! file_exists($includePath)) {
    $includePath = __DIR__ . '/library/Installer/include.php';
}

require_once $includePath;

use Alledia\Installer\AbstractScript;

/**
 * OSMeta Installer Script
 *
 * @since  1.0
 */
class Com_OSMetaInstallerScript extends AbstractScript
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

        // Remove the old tables, if existent
        $tables = array(
            '#__osmeta_extensions',
            '#__osmeta_meta_extensions',
            '#__osmeta_keywords',
            '#__osmeta_keywords_items'
        );
        foreach ($tables as $table) {
            $query = "DROP TABLE IF EXISTS `{$table}`";
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}
