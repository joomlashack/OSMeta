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

use Alledia\OSMeta\ContainerFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Plugin\PluginHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class OSMetaController extends BaseController
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function display($cachable = false, $urlparams = [])
    {
        $this->view();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function view(): void
    {
        $this->actionManager('view');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function save(): void
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_OSMETA_SUCCESSFULLY_SAVED'));
        $this->actionManager('save');
    }

    /**
     * Method to the Copy Item Title to Title action for Meta Tags Manager
     *
     * @return void
     * @throws Exception
     */
    public function copyItemTitleToSearchEngineTitle(): void
    {
        $this->actionManager('copyItemTitleToSearchEngineTitle');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generateDescriptions(): void
    {
        $this->actionManager('generateDescriptions');
    }

    /**
     * @param string $task Task name
     *
     * @return void
     * @throws Exception
     *
     */
    protected function actionManager(string $task): void
    {
        $app = Factory::getApplication();

        $itemType = $app->input->getString('type');
        if ($itemType == false) {
            $itemType = 'com_content:Article';
            $app->input->set('type', $itemType);
        }

        $factory = ContainerFactory::getInstance();

        $metatagsContainer = $factory->getContainerById($itemType);

        $cid = Factory::getApplication()->input->get('cid', [], 'array');

        // Execute the actions
        switch ($task) {
            case 'save':
                // Content
                $ids              = $app->input->get('ids', [], 'array');
                $metatitles       = $app->input->get('metatitle', [], 'array');
                $metadescriptions = $app->input->get('metadesc', [], 'array');
                $aliases          = $app->input->get('alias', [], 'array');
                $metatagsContainer->saveMetatags($ids, $metatitles, $metadescriptions, $aliases);
                break;

            case 'copyItemTitleToSearchEngineTitle':
                if ($metatagsContainer->supportGenerateTitle) {
                    $metatagsContainer->copyItemTitleToSearchEngineTitle($cid);
                }
                break;

            case 'generateDescriptions':
                if ($metatagsContainer->supportGenerateDescription) {
                    $metatagsContainer->generateDescriptions($cid);
                }

                break;
        }

        // Add a warning message if the plugins are disabled
        if (PluginHelper::isEnabled('content', 'osmetacontent') == false) {
            $app->enqueueMessage(Text::_('COM_OSMETA_DISABLED_CONTENT_PLUGIN'), 'warning');
        }

        if (PluginHelper::isEnabled('system', 'osmetarenderer') == false) {
            $app->enqueueMessage(Text::_('COM_OSMETA_DISABLED_SYSTEM_PLUGIN'), 'warning');
        }

        $itemTypeShort = 'COM_OSMETA_TITLE_' . strtoupper(str_replace(':', '_', $itemType));

        $features = $factory->getFeatures();
        $this->addSubmenu($features, $itemType);

        $limit      = $app->input->getInt('limit', $app->get('list_limit'));
        $limitStart = $app->input->getInt('limitstart', 0);
        $result     = $metatagsContainer->getMetatags($limitStart, $limit);

        /** @var OSMetaViewOSMeta $view */
        $view                    = $this->getView('OSMeta', 'html');
        $view->itemType          = $itemType;
        $view->metatagsData      = $result['rows'];
        $view->filters           = $metatagsContainer->getFilters();
        $view->availableTypes    = $features;
        $view->pageNav           = new Pagination($result['total'], $limitStart, $limit);
        $view->order             = $app->input->getCmd('filter_order', 'title');
        $view->order_Dir         = $app->input->getCmd('filter_order_Dir', 'ASC');
        $view->itemTypeShort     = $itemTypeShort;
        $view->metatagsContainer = $metatagsContainer;

        $view->display();
    }

    /**
     * Insert the submenu items
     *
     * @param array[] $contentTypes
     * @param string  $itemType
     *
     * @return void
     */
    protected function addSubmenu(array $contentTypes, string $itemType): void
    {
        foreach ($contentTypes as $type => $data) {
            Sidebar::addEntry(
                Text::_($data['name']),
                'index.php?option=com_osmeta&type=' . urlencode($type),
                $itemType === $type
            );
        }
    }
}
