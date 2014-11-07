<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\Component;

require_once 'include.php';

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $component = new Component('OSMeta');
    $component->loadController();
    $component->executeTask();
}
