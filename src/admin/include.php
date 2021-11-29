<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
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
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

try {
    $frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';
    if (!(is_file($frameworkPath) && include $frameworkPath)) {
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

        JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

        if (class_exists('\\Alledia\\OSMeta\\Pro\\Container\\Factory')) {
            class_alias('\\Alledia\\OSMeta\\Pro\\Container\\Factory', '\\Alledia\\OSMeta\\ContainerFactory');
        } else {
            class_alias('\\Alledia\\OSMeta\\Free\\Container\\Factory', '\\Alledia\\OSMeta\\ContainerFactory');
        }

        define('OSMETA_LOADED', 1);
    }

} catch (Throwable $error) {
    Factory::getApplication()->enqueueMessage('[OSMeta] Unable to initialize: ' . $error->getMessage(), 'error');

    return false;
}

return defined('ALLEDIA_FRAMEWORK_LOADED') && defined('OSMETA_LOADED');
