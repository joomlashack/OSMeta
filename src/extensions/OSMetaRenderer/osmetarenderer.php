<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\AbstractPlugin;

require_once __DIR__ . '/include.php';

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    /**
     * OSMeta System Plugin - Renderer
     *
     * @since  1.0
     */
    class PlgSystemOSMetaRenderer extends AbstractPlugin
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
                $buffer = Alledia\OSMeta\Free\Container\Factory::processBody($buffer, $url);

                JResponse::setBody($buffer);
            }

            return true;
        }
    }
}
