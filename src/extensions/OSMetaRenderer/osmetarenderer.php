<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2020 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSMeta.
 *
 * OSMeta is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSMeta is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMeta.  If not, see <http://www.gnu.org/licenses/>.
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
         * @return bool
         * @throws Exception
         */
        public function onAfterRender()
        {
            $app = JFactory::getApplication();

            if ($app->isClient('site')) {
                $queryData = $_REQUEST;
                ksort($queryData);
                $url = http_build_query($queryData);

                $buffer = $app->getBody();

                // Metatags processing on the response body
                $factory = null;
                if (class_exists('Alledia\OSMeta\Pro\Container\Factory')) {
                    $factory = OSMeta\Pro\Container\Factory::getInstance();
                } else {
                    $factory = OSMeta\Free\Container\Factory::getInstance();
                }

                $buffer = $factory->processBody($buffer, $url);

                $app->setBody($buffer);
            }

            return true;
        }
    }
}
