<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2016 Alledia.com, All rights reserved
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
