<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Metatags Container Factory Class
 *
 * @since  1.0
 */
class com_OSMetaInstallerScript
{
    /**
     * method to run before an install/update/uninstall method
     *
     * @param  string  $type    type of change (install, update or discover_install)
     * @param  string  $parent  the class calling this method
     *
     * @access  public
     * @since   1.0
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
        $jversion = new JVersion;
        $jversion = $jversion->getShortVersion();

        $manifest = $parent->get("manifest");

        $this->release = (string) $manifest->version;
        $this->minJoomlaRelease = (string) $manifest->attributes()->version;

        // abort if the current Joomla release is older
        if (version_compare($jversion, $this->minJoomlaRelease, 'lt')) {
            $message = 'Cannot install com_osmeta in a Joomla release prior to ' . $this->minJoomlaRelease;
            Jerror::raiseWarning(null, $message);

            return false;
        }

        return true;
    }
}
