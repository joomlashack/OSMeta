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

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\OSMeta\ContainerFactory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

$includePath = JPATH_ADMINISTRATOR . '/components/com_osmeta/include.php';
if ((is_file($includePath) && (include $includePath)) == false) {
    class_alias(CMSPlugin::class, AbstractPlugin::class);
}

// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class PlgSystemOSMetaRenderer extends AbstractPlugin
{
    /**
     * @inheritdoc
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @return void
     * @throws Exception
     */
    public function onAfterRender()
    {
        if (
            $this->isEnabled()
            && $this->app->isClient('site')
        ) {
            $queryData = $_REQUEST;
            if (empty($queryData['id']) || empty($queryData['option'])) {
                if ($menu = $this->app->getMenu()) {
                    if ($menu = $menu->getActive()) {
                        $queryData += $menu->query;
                    }
                }
            }

            if ($id = ($queryData['id'] ?? null)) {
                $queryData['id'] = is_numeric($id) ? (int)$id : (string)$id;
            }

            $buffer = ContainerFactory::getInstance()->processBody(
                $this->app->getBody(),
                http_build_query($queryData)
            );

            $this->app->setBody($buffer);
        }
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return class_exists(ContainerFactory::class);
    }
}
