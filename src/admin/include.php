<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2024 Joomlashack.com. All rights reserved
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

use Alledia\Framework\AutoLoader;
use Alledia\Framework\Helper as FrameworkHelper;
use Alledia\OSMeta\ContainerFactory;
use Alledia\OSMeta\Free\Container\Factory as FreeFactory;
use Alledia\OSMeta\Pro\Container\Factory as ProFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;

defined('_JEXEC') or die();

try {
    $frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';
    if ((is_file($frameworkPath) && include $frameworkPath) == false) {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            $app->enqueueMessage('[OSMeta] Joomlashack framework not found', 'error');
        }

        return false;
    }

    if (defined('ALLEDIA_FRAMEWORK_LOADED') && !defined('OSMETA_LOADED')) {
        define('OSMETA_ADMIN', __DIR__);
        define('OSMETA_LIBRARY', OSMETA_ADMIN . '/library');

        AutoLoader::register('Alledia\OSMeta', OSMETA_LIBRARY);

        if (class_exists(ContentRouteHelper::class) == false) {
            JLoader::register(ContentHelperRoute::class, JPATH_SITE . '/components/com_content/helpers/route.php');
            FrameworkHelper::createDatabaseClassAliases();
            FrameworkHelper::createClassAliases([ContentHelperRoute::class => ContentRouteHelper::class]);
        }

        if (class_exists(ProFactory::class)) {
            class_alias(ProFactory::class, ContainerFactory::class);
        } else {
            class_alias(FreeFactory::class, ContainerFactory::class);
        }

        define('OSMETA_LOADED', 1);
    }

} catch (Throwable $error) {
    Factory::getApplication()->enqueueMessage('[OSMeta] Unable to initialize: ' . $error->getMessage(), 'error');

    return false;
}

return defined('ALLEDIA_FRAMEWORK_LOADED') && defined('OSMETA_LOADED');
