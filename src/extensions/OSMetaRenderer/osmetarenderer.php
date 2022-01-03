<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2022 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSMeta.  If not, see <https://www.gnu.org/licenses/>.
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\OSMeta\ContainerFactory;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

$includePath = JPATH_ADMINISTRATOR . '/components/com_osmeta/include.php';
if (is_file($includePath) && (include $includePath)) {
    class PlgSystemOSMetaRenderer extends AbstractPlugin
    {
        /**
         * @return void
         * @throws Exception
         */
        public function onAfterRender()
        {
            $app = Factory::getApplication();

            if ($app->isClient('site')) {
                $queryData = $_REQUEST ?? $app->getMenu()->getActive()->query;
                ksort($queryData);
                $url = http_build_query($queryData);

                $buffer = $app->getBody();

                $buffer = ContainerFactory::getInstance()->processBody($buffer, $url);

                $app->setBody($buffer);
            }
        }
    }
}
