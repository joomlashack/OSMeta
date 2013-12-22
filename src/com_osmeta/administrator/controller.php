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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

if (version_compare(JVERSION, "3.0", ">="))
{
	class OSController extends JControllerLegacy {}
}
else
{
	class OSController extends JController {}
}

class OsmetaController extends OSController
{
	public function metatags_view()
	{
		$this->metatags_manager('metatags_view');
	}

	public function metatags_save()
	{
		$this->metatags_manager('metatags_save');
	}

	public function metatags_copy_keywords_to_title()
	{
		$this->metatags_manager('metatags_copy_keywords_to_title');
	}

	public function metatags_copy_title_to_keywords()
	{
		$this->metatags_manager('metatags_copy_title_to_keywords');
	}

	public function metatags_copy_item_title_to_keywords()
	{
		$this->metatags_manager('metatags_copy_item_title_to_keywords');
	}

	public function metatags_copy_item_title_to_title()
	{
		$this->metatags_manager('metatags_copy_item_title_to_title');
	}

	public function metatags_generare_descriptions()
	{
		$this->metatags_manager('metatags_generare_descriptions');
	}

	public function metatags_clear_browser_titles()
	{
	  $this->metatags_manager('metatags_clear_browser_titles');
	}

	private function metatags_manager($task)
	{
		$app = JFactory::getApplication();
		require_once("classes/MetatagsContainerFactory.php");

		$itemType = JRequest::getVar('type', null, '', 'string');
		if (!$itemType)
		{
		  $itemType = key(MetatagsContainerFactory::getFeatures());
		}

		$metatagsContainer = MetatagsContainerFactory::getContainerById($itemType);
		if (!is_object($metatagsContainer))
		{
			throw Exception('MetaTagsContainer is not an object');
		}

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

		$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit

		jimport('joomla.html.pagination');

		$pageNav = new JPagination($db->loadResult(), $limitstart, $limit);

		$view = $this->getView('OSMeta', 'html');
		$view->assignRef('itemType', $itemType);
		$view->assignRef('metatagsData', $tags);
		$view->assignRef('page', $page);
		$view->assignRef('itemsOnPage', $itemsOnPage);
		$view->assignRef('filter', $metatagsContainer->getFilter());
		$view->assignRef('availableTypes', MetatagsContainerFactory::getFeatures());
		$view->assignRef('pageNav', $pageNav);
		$view->assignRef('order', JRequest::getCmd("filter_order", "title"));
		$view->assignRef('order_Dir', JRequest::getCmd("filter_order_Dir", "ASC"));
		$view->display();
	}
}