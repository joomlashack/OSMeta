<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2020 Joomlashack.com. All rights reserved
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

defined('_JEXEC') or die();

class OSMetaController extends JControllerLegacy
{
    /**
     * Method to display the controller's view
     *
     * @param bool  $cachable  Cachable
     * @param array $urlparams URL Params
     *
     * @access  public
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = array())
    {
        $this->view();
    }

    /**
     * Method to display the Meta Tags Manager's view
     *
     * @access  public
     * @return void
     * @since   1.0
     *
     */
    public function view()
    {
        $this->actionManager('view');
    }

    /**
     * Method to the Save action for Meta Tags Manager
     *
     * @access  public
     * @return void
     * @since   1.0
     *
     */
    public function save()
    {
        JFactory::getApplication()->enqueueMessage(JText::_('COM_OSMETA_SUCCESSFULLY_SAVED'), 'message');
        $this->actionManager('save');
    }

    /**
     * Method to the Copy Item Title to Title action for Meta Tags Manager
     *
     * @access  public
     * @return void
     * @since   1.0
     *
     */
    public function copyItemTitleToSearchEngineTitle()
    {
        $this->actionManager('copyItemTitleToSearchEngineTitle');
    }

    /**
     * Method to the Generate Descriptions action for Meta Tags Manager
     *
     * @access  public
     * @return void
     * @since   1.0
     *
     */
    public function generateDescriptions()
    {
        $this->actionManager('generateDescriptions');
    }

    /**
     * Method to the execute actions
     *
     * @param string $task Task name
     *
     * @access  private
     * @return void
     * @since   1.0
     *
     */
    private function actionManager($task)
    {
        $app = JFactory::getApplication();

        $itemType = $app->input->getString('type', null);
        if (empty($itemType)) {
            $itemType = 'com_content:Article';
            $app->input->set('type', $itemType);
        }

        if (class_exists('Alledia\OSMeta\Pro\Container\Factory')) {
            $factory = Alledia\OSMeta\Pro\Container\Factory::getInstance();
        } else {
            $factory = Alledia\OSMeta\Free\Container\Factory::getInstance();
        }

        if (!$itemType) {
            $itemType = key($factory->getFeatures());
        }

        $metatagsContainer = $factory->getContainerById($itemType);

        if (!is_object($metatagsContainer)) {
            // TODO: throw error here.
        }

        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');

        // Execute the actions
        switch ($task) {
            case "save":
                // Content
                $ids              = $app->input->get('ids', array(), 'array');
                $metatitles       = $app->input->get('metatitle', array(), 'array');
                $metadescriptions = $app->input->get('metadesc', array(), 'array');
                $aliases          = $app->input->get('alias', array(), 'array');
                $metatagsContainer->saveMetatags($ids, $metatitles, $metadescriptions, $aliases);

                break;

            case "copyItemTitleToSearchEngineTitle":
                if ($metatagsContainer->supportGenerateTitle) {
                    $metatagsContainer->copyItemTitleToSearchEngineTitle($cid);
                }

                break;

            case "generateDescriptions":
                if ($metatagsContainer->supportGenerateDescription) {
                    $metatagsContainer->GenerateDescriptions($cid);
                }

                break;
        }

        $limit      = $app->input->getInt('limit', $app->get('list_limit'));
        $limitstart = $app->input->getInt('limitstart', 0);

        $result = $metatagsContainer->getMetatags($limitstart, $limit);

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($result['total'], $limitstart, $limit);

        $filter   = $metatagsContainer->getFilter();
        $features = $factory->getFeatures();
        $order    = $app->input->getCmd("filter_order", "title");
        $orderDir = $app->input->getCmd("filter_order_Dir", "ASC");

        // Add a warning message if the plugins are disabled
        if (!JPluginHelper::isEnabled('content', 'osmetacontent')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_CONTENT_PLUGIN'), 'warning');
        }

        if (!JPluginHelper::isEnabled('system', 'osmetarenderer')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_SYSTEM_PLUGIN'), 'warning');
        }

        $itemTypeShort = 'COM_OSMETA_TITLE_' . strtoupper(str_replace(':', '_', $itemType));

        $this->addSubmenu($features, $itemType);

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
     * @param array  $contentTypes An array of the available content types
     * @param string $itemType     The current
     */
    protected function addSubmenu($contentTypes, $itemType)
    {
        foreach ($contentTypes as $type => $data) {
            JHtmlSidebar::addEntry(
                JText::_($data['name']),
                'index.php?option=com_osmeta&type=' . urlencode($type),
                $itemType === $type
            );
        }
    }
}
