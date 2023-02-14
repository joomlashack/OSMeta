<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2023 Joomlashack.com. All rights reserved
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die();

class OSMetaController extends JControllerLegacy
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
    public function view()
    {
        $this->actionManager('view');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function save()
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
    public function copyItemTitleToSearchEngineTitle()
    {
        $this->actionManager('copyItemTitleToSearchEngineTitle');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generateDescriptions()
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
    protected function actionManager(string $task)
    {
        $app = Factory::getApplication();

        $itemType = $app->input->getString('type');
        if (empty($itemType)) {
            $itemType = 'com_content:Article';
            $app->input->set('type', $itemType);
        }

        $factory = ContainerFactory::getInstance();

        if (!$itemType) {
            $itemType = key($factory->getFeatures());
        }

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
                    $metatagsContainer->GenerateDescriptions($cid);
                }

                break;
        }

        $limit      = $app->input->getInt('limit', $app->get('list_limit'));
        $limitstart = $app->input->getInt('limitstart', 0);

        $result = $metatagsContainer->getMetatags($limitstart, $limit);

        $pageNav = new Pagination($result['total'], $limitstart, $limit);

        $filter   = $metatagsContainer->getFilter();
        $features = $factory->getFeatures();
        $order    = $app->input->getCmd('filter_order', 'title');
        $orderDir = $app->input->getCmd('filter_order_Dir', 'ASC');

        // Add a warning message if the plugins are disabled
        if (!PluginHelper::isEnabled('content', 'osmetacontent')) {
            $app->enqueueMessage(Text::_('COM_OSMETA_DISABLED_CONTENT_PLUGIN'), 'warning');
        }

        if (!PluginHelper::isEnabled('system', 'osmetarenderer')) {
            $app->enqueueMessage(Text::_('COM_OSMETA_DISABLED_SYSTEM_PLUGIN'), 'warning');
        }

        $itemTypeShort = 'COM_OSMETA_TITLE_' . strtoupper(str_replace(':', '_', $itemType));

        $this->addSubmenu($features, $itemType);

        /** @var OSMetaViewOSMeta $view */
        $view                    = $this->getView('OSMeta', 'html');
        $view->itemType          = $itemType;
        $view->metatagsData      = $result['rows'];
        $view->filter            = $filter;
        $view->availableTypes    = $features;
        $view->pageNav           = $pageNav;
        $view->order             = $order;
        $view->order_Dir         = $orderDir;
        $view->itemTypeShort     = $itemTypeShort;
        $view->metatagsContainer = $metatagsContainer;

        $view->display();
    }

    /**
     * Insert the submenu items
     *
     * @param array[]  $contentTypes
     * @param string $itemType
     */
    protected function addSubmenu(array $contentTypes, string $itemType)
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
