<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
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

use Alledia\Installer\AbstractScript;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;

defined('_JEXEC') or die();

$installPath = __DIR__ . (is_dir(__DIR__ . '/admin') ? '/admin' : '');
require_once $installPath . '/library/Installer/include.php';

class Com_osmetainstallerScript extends AbstractScript
{
    /**
     * @inheritDoc
     */
    protected function customPostFlight(string $type, InstallerAdapter $parent)
    {
        $db = $this->dbo;

        // Remove the old pkg_osmeta, if existent
        $query = $db->getQuery(true)
            ->delete('#__extensions')
            ->where([
                'type = ' . $db->quote('package'),
                'element = ' . $db->quote('pkg_osmeta')
            ]);

        $db->setQuery($query)->execute();

        // Remove the old tables, if existent
        $tables = [
            '#__osmeta_extensions',
            '#__osmeta_meta_extensions',
            '#__osmeta_keywords',
            '#__osmeta_keywords_items',
        ];
        foreach ($tables as $table) {
            $query = 'DROP TABLE IF EXISTS ' . $db->quoteName($table);

            $db->setQuery($query)->execute();
        }

        $oldLanguageFiles = Folder::files(JPATH_ADMINISTRATOR . '/language', '.*com_osmeta.*', true, true);
        foreach ($oldLanguageFiles as $oldLanguageFile) {
            unlink($oldLanguageFile);
        }
    }
}
