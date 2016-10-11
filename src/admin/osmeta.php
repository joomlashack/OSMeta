<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

use Alledia\Framework\Joomla\Extension;

defined('_JEXEC') or die();

include_once 'include.php';

if (defined('OSMETA_LOADED')) {
    $component = new Extension\Component('OSMeta');
    $component->loadController();
    $component->executeTask();
}
