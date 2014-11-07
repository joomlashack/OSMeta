<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Alledia\OSMeta\Free\Container\Factory as ContainerFactory;
use Alledia\OSMeta\Free\Container\AbstractHome as AbstractHomeContainer;

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
     * @access	public
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
     * @access	public
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
     * @access	public
     * @since  1.0
     *
     * @return void
     */
    public function save()
    {
        JFactory::getApplication()->enqueueMessage(JText::_('COM_OSMETA_SAVED_WITH_SUCCESS'), 'message');
        $this->actionManager('save');
    }

    /**
     * Method to the Copy Item Title to Title action for Meta Tags Manager
     *
     * @access	public
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
     * @access	public
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
     * @access	private
     * @since  1.0
     *
     * @return void
     */
    private function actionManager($task)
    {
        $app = JFactory::getApplication();

        $itemType = $app->input->getString('type', null);

        if (!$itemType) {
            $itemType = key(ContainerFactory::getFeatures());

            if (empty($itemType)) {
                // Enable com_content
                $component = 'com_content';

                $db = JFactory::getDBO();
                $db->setQuery(
                    "UPDATE #__osmeta_meta_extensions " .
                    "SET available = 1 " .
                    "WHERE component LIKE '{$component}'"
                );
                $db->execute();

                // Get the features again
                $itemType = key(ContainerFactory::getFeatures());
            }
        }

        $metatagsContainer = ContainerFactory::getContainerById($itemType);

        if (!is_object($metatagsContainer)) {
            // TODO: throw error here.
        }

        // Execute the actions
        switch ($task) {
            case "save":
                // Content
                $ids = JRequest::getVar('ids', array(), '', 'array');
                $metatitles = JRequest::getVar('metatitle', array(), '', 'array');
                $metadescriptions = JRequest::getVar('metadesc', array(), '', 'array');
                $metakeys = JRequest::getVar('metakey', array(), '', 'array');
                $metatagsContainer->saveMetatags($ids, $metatitles, $metadescriptions, $metakeys);

                // Home data
                $homeSource = JRequest::getVar('home_metadata_source', 'default', '', 'string');
                $homeMetaTitle = JRequest::getVar('home_metatitle', '', '', 'string');
                $homeMetaDescription = JRequest::getVar('home_metadesc', '', '', 'string');
                $homeMetaKey = JRequest::getVar('home_metakey', '', '', 'string');
                AbstractHomeContainer::saveMetatags(
                    $homeSource,
                    $homeMetaTitle,
                    $homeMetaDescription,
                    $homeMetaKey
                );
                break;

            case "copyItemTitleToSearchEngineTitle":
                $metatagsContainer->copyItemTitleToSearchEngineTitle(JRequest::getVar('cid', array(), '', 'array'));
                break;

            case "generateDescriptions":
                $metatagsContainer->GenerateDescriptions(JRequest::getVar('cid', array(), '', 'array'));
                break;
        }

        $limit = JRequest::getVar('limit', $app->getCfg('list_limit'));
        $limitstart = JRequest::getVar('limitstart', 0);

        $db = JFactory::getDBO();
        $tags = $metatagsContainer->getMetatags($limitstart, $limit);

        // No reloading the query! Just asking for total without limit
        $db->setQuery('SELECT FOUND_ROWS();');

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($db->loadResult(), $limitstart, $limit);

        $filter = $metatagsContainer->getFilter();
        $features = ContainerFactory::getFeatures();
        $order = JRequest::getCmd("filter_order", "title");
        $orderDir = JRequest::getCmd("filter_order_Dir", "ASC");

        // Add a warning message if the plugins are disabled
        if (!JPluginHelper::isEnabled('content', 'osmetacontent')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_CONTENT_PLUGIN'), 'warning');
        }

        if (!JPluginHelper::isEnabled('system', 'osmetarenderer')) {
            $app->enqueueMessage(JText::_('COM_OSMETA_DISABLED_SYSTEM_PLUGIN'), 'warning');
        }

        $itemTypeShort = $itemType === 'com_content:Article' ? 'articles' : 'categories';

        // Get Homepage data
        $home = AbstractHomeContainer::getMetatags();

        $homeFieldsDisabledAttribute = $home->source === 'custom' ? '' : 'readonly';

        $view = $this->getView('OSMeta', 'html');
        $view->assignRef('itemType', $itemType);
        $view->assignRef('metatagsData', $tags);
        $view->assignRef('homeMetatagsData', $home);
        $view->assignRef('page', $page);
        $view->assignRef('itemsOnPage', $itemsOnPage);
        $view->assignRef('filter', $filter);
        $view->assignRef('availableTypes', $features);
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('order', $order);
        $view->assignRef('order_Dir', $orderDir);
        $view->assignRef('itemTypeShort', $itemTypeShort);
        $view->assignRef('homeFieldsDisabledAttribute', $homeFieldsDisabledAttribute);
        $view->display();
    }
}
