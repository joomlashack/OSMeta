<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

use Alledia\Framework\Joomla\Extension;
use Alledia\OSMeta;

defined('_JEXEC') or die();

include_once JPATH_ADMINISTRATOR . '/components/com_osmeta/include.php';

if (defined('OSMETA_LOADED')) {
    /**
     * OSMeta System Plugin - Renderer
     *
     * @since  1.0
     */
    class PlgSystemOSMetaRenderer extends Extension\AbstractPlugin
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
                $factory = null;
                if (class_exists('Alledia\OSMeta\Pro\Container\Factory')) {
                    $factory = OSMeta\Pro\Container\Factory::getInstance();
                } else {
                    $factory = OSMeta\Free\Container\Factory::getInstance();
                }

                $buffer = $factory->processBody($buffer, $url);

                JResponse::setBody($buffer);
            }

            return true;
        }
    }
}
