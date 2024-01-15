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

use Alledia\Installer\AbstractScript;
use Joomla\CMS\Installer\InstallerAdapter;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

$installPath = __DIR__ . (is_dir(__DIR__ . '/admin') ? '/admin' : '');
require_once $installPath . '/library/Installer/include.php';

// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

class Com_osmetainstallerScript extends AbstractScript
{
    /**
     * @var string[]
     */
    protected $plugins = [
        'plugin.content.osmetacontent',
        'plugin.system.osmetarenderer',
    ];

    /**
     * @inheritDoc
     */
    protected function customPreFlight(string $type, InstallerAdapter $parent): bool
    {
        // Disable plugins to prevent class definition clashes
        $this->setExtensionState($this->plugins);

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function customPostFlight(string $type, InstallerAdapter $parent): void
    {
        $this->setExtensionState($this->plugins, 1);
    }
}
