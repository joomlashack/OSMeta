<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

jimport('cms.view.legacy');

/**
 * OSMetaController component Controller
 *
 * @since  1.0
 */
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
     * @since  1.0
     *
     * @return void
     */
    public function view()
    {
        $this->actionManager('view');
    }

    /**
     * Method to the Save action for Meta Tags Manager
     *
     * @access  public
     * @since  1.0
     *
     * @return void
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
     * @since  1.0
     *
     * @return void
     */
    public function copyItemTitleToSearchEngineTitle()
    {
        $this->actionManager('copyItemTitleToSearchEngineTitle');
    }

    /**
     * Method to the Generate Descriptions action for Meta Tags Manager
     *
     * @access  public
     * @since  1.0
     *
     * @return void
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
     * @since  1.0
     *
     * @return void
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

        $cid = JRequest::getVar('cid', array(), '', 'array');

        // Execute the actions
        switch ($task) {
            case "save":
                // Content
                $ids              = JRequest::getVar('ids', array(), '', 'array');
                $metatitles       = JRequest::getVar('metatitle', array(), '', 'array');
                $metadescriptions = JRequest::getVar('metadesc', array(), '', 'array');
                $aliases          = JRequest::getVar('alias', array(), '', 'array');
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

        $limit = JRequest::getVar('limit', $app->getCfg('list_limit'));
        $limitstart = JRequest::getVar('limitstart', 0);

        $db = JFactory::getDBO();
        $result = $metatagsContainer->getMetatags($limitstart, $limit);

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($result['total'], $limitstart, $limit);

        $filter = $metatagsContainer->getFilter();
        $features = $factory->getFeatures();
        $order = JRequest::getCmd("filter_order", "title");
        $orderDir = JRequest::getCmd("filter_order_Dir", "ASC");

        // Add a warning message if the plugins are disabled
        if (!JPluginHelper::isEnabled('content', 'osmetacontent')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_CONTENT_PLUGIN'), 'warning');
        }

        if (!JPluginHelper::isEnabled('system', 'osmetarenderer')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_SYSTEM_PLUGIN'), 'warning');
        }

        $itemTypeShort = 'COM_OSMETA_TITLE_' . strtoupper(str_replace(':', '_', $itemType));

        $this->addSubmenu($features, $itemType);

        $view = $this->getView('OSMeta', 'html');
        $view->assignRef('itemType', $itemType);
        $view->assignRef('metatagsData', $result['rows']);
        $view->assignRef('page', $page);
        $view->assignRef('itemsOnPage', $itemsOnPage);
        $view->assignRef('filter', $filter);
        $view->assignRef('availableTypes', $features);
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('order', $order);
        $view->assignRef('order_Dir', $orderDir);
        $view->assignRef('itemTypeShort', $itemTypeShort);
        $view->assignRef('metatagsContainer', $metatagsContainer);

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
        if (version_compare(JVERSION, '3.0', 'lt')) {
            foreach ($contentTypes as $type => $data) {
                JSubMenuHelper::addEntry(
                    $data['name'],
                    'index.php?option=com_osmeta&type=' . urlencode($type),
                    $itemType === $type
                );
            }
        } else {
            foreach ($contentTypes as $type => $data) {
                JHtmlSidebar::addEntry(
                    $data['name'],
                    'index.php?option=com_osmeta&type=' . urlencode($type),
                    $itemType === $type
                );
            }
        }
    }
}
