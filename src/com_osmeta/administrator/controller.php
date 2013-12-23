<?php
/**
 * @category  Joomla Component
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Extend the JController for J3.0 compatibility
 *
 */
if (version_compare(JVERSION, "3.0", "<"))
{
	/**
	 * Alias Class for JController in Joomla! < 3.0
	 *
	 * @since  1.0.0
	 */
	class OSController extends JController {}
}
else
{
	/**
	 * Alias Class for JControllerLegacy in Joomla! >= 3.0
	 *
	 * @since  1.0.0
	 */
	class OSController extends JControllerLegacy {}
}

/**
 * OSMetaController component Controller
 *
 * @since  1.0.0
 */
class OSMetaController extends OSController
{
	/**
	 * Method to display the controller's view
	 *
	 * @param   bool   $cachable   Cachable
	 * @param   array  $urlparams  URL Params
	 *
	 * @access	public
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$this->metatags_view();
	}

	/**
	 * Method to display the Meta Tags Manager's view
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_view()
	{
		$this->metatags_manager('metatags_view');
	}

	/**
	 * Method to the Save action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_save()
	{
		$this->metatags_manager('metatags_save');
	}

	/**
	 * Method to the Copy Keywords to Title action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_copy_keywords_to_title()
	{
		$this->metatags_manager('metatags_copy_keywords_to_title');
	}

	/**
	 * Method to the Copy Title to Keywords action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_copy_title_to_keywords()
	{
		$this->metatags_manager('metatags_copy_title_to_keywords');
	}

	/**
	 * Method to the Copy Item Title to Keywords action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_copy_item_title_to_keywords()
	{
		$this->metatags_manager('metatags_copy_item_title_to_keywords');
	}

	/**
	 * Method to the Copy Item Title to Title action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_copy_item_title_to_title()
	{
		$this->metatags_manager('metatags_copy_item_title_to_title');
	}

	/**
	 * Method to the Generate Descriptions action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_generare_descriptions()
	{
		$this->metatags_manager('metatags_generare_descriptions');
	}

	/**
	 * Method to the Clear Browser Titles action for Meta Tags Manager
	 *
	 * @access	public
	 * @since  1.0.0
	 */
	public function metatags_clear_browser_titles()
	{
	  $this->metatags_manager('metatags_clear_browser_titles');
	}

	/**
	 * Method to the execute actions
	 *
	 * @access	private
	 * @since  1.0.0
	 */
	private function metatags_manager($task)
	{
		$app = JFactory::getApplication();
		require_once "classes/MetatagsContainerFactory.php";

		$itemType = JRequest::getVar('type', null, '', 'string');
		if (!$itemType)
		{
		  $itemType = key(MetatagsContainerFactory::getFeatures());
		}

		$metatagsContainer = MetatagsContainerFactory::getContainerById($itemType);

		if (!is_object($metatagsContainer))
		{
			//TODO: throw error here.
		}

		// Execute the actions
		switch($task)
		{
			case "metatags_save":
				$ids = JRequest::getVar('ids', array(), '', 'array');
				$metatitles = JRequest::getVar('metatitle', array(), '', 'array');
				$metadescriptions = JRequest::getVar('metadesc', array(), '', 'array');
				$metakeys = JRequest::getVar('metakey', array(), '', 'array');
				$title_tags = JRequest::getVar('title_tag', array(), '', 'array');
				$metatagsContainer->saveMetatags($ids, $metatitles, $metadescriptions, $metakeys, $title_tags);
				break;

			case "metatags_copy_keywords_to_title":
				$metatagsContainer->copyKeywordsToTitle(JRequest::getVar('cid', array(), '', 'array'));
				break;

			case "metatags_copy_title_to_keywords":
				$metatagsContainer->copyTitleToKeywords(JRequest::getVar('cid', array(), '', 'array'));
				break;

			case "metatags_copy_item_title_to_keywords":
				$metatagsContainer->copyItemTitleToKeywords(JRequest::getVar('cid', array(), '', 'array'));
				break;

			case "metatags_copy_item_title_to_title":
				$metatagsContainer->copyItemTitleToTitle(JRequest::getVar('cid', array(), '', 'array'));
				break;

			case "metatags_generare_descriptions":
				$metatagsContainer->GenerateDescriptions(JRequest::getVar('cid', array(), '', 'array'));
				break;

			case "metatags_clear_browser_titles":
				$metatagsContainer->clearBrowserTitles(JRequest::getVar('cid', array(), '', 'array'));
				break;
		}

		$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		$limitstart = JRequest::getVar('limitstart', 0);

		$db = JFactory::getDBO();
		$tags = $metatagsContainer->getMetatags($limitstart, $limit);

		// No reloading the query! Just asking for total without limit
		$db->setQuery('SELECT FOUND_ROWS();');

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($db->loadResult(), $limitstart, $limit);

		$filter = $metatagsContainer->getFilter();
		$features = MetatagsContainerFactory::getFeatures();
		$order = JRequest::getCmd("filter_order", "title");
		$orderDir = JRequest::getCmd("filter_order_Dir", "ASC");

		$view = $this->getView('OSMeta', 'html');
		$view->assignRef('itemType', $itemType);
		$view->assignRef('metatagsData', $tags);
		$view->assignRef('page', $page);
		$view->assignRef('itemsOnPage', $itemsOnPage);
		$view->assignRef('filter', $filter);
		$view->assignRef('availableTypes', $features);
		$view->assignRef('pageNav', $pageNav);
		$view->assignRef('order', $order);
		$view->assignRef('order_Dir', $orderDir);
		$view->display();
	}
}