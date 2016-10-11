<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

use Alledia\Framework;

defined('_JEXEC') or die();

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (file_exists($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;
    } else {
        $app = JFactory::getApplication();

        if ($app->isAdmin()) {
            $app->enqueueMessage('[OSMeta] Alledia framework not found', 'error');
        }
    }
}

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    define('OSMETA_ADMIN', __DIR__);
    define('OSMETA_LIBRARY', OSMETA_ADMIN . '/library');

    Framework\AutoLoader::register('Alledia\OSMeta', OSMETA_LIBRARY);

    define('OSMETA_LOADED', 1);
}
