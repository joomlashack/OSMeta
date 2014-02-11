<?php
/**
 * @category  Joomla System Plugin
 * @package   com_osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * OSMeta System Plugin - Renderer
 *
 * @since  1.0
 */
class PlgSystemOSMetaRenderer extends JPlugin
{
    /**
     * Event method onAfterRender, to process the metadata on the front-end
     *
     * @access  public
     *
     * @return bool
     */
    public function onAfterRender()
    {
        $app = JFactory::getApplication();

        if ($app->getName() === 'site') {
            $queryData = $_REQUEST;
            ksort($queryData);
            $url = http_build_query($queryData);

            $buffer = JResponse::getBody();

            // Metatags processing on the response body
            require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/OSMetatagsContainerFactory.php";
            $buffer = OSMetatagsContainerFactory::processBody($buffer, $url);

            JResponse::setBody($buffer);
        }

        return true;
    }
}
