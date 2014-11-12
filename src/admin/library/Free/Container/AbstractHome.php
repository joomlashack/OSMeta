<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace Alledia\OSMeta\Free\Container;

use Alledia\OSMeta\Free\Container\AbstractContainer;
use JRequest;
use JFactory;
use JComponentHelper;
use stdClass;
use JModelLegacy;
use JTable;

// No direct access
defined('_JEXEC') or die();


/**
 * Homepage Metatags Container
 *
 * @since  1.0
 */
abstract class AbstractHome
{
    /**
     * Params
     *
     * @var    Object
     * @since  1.0
     */
    public static $params = null;

    /**
     * Set Params
     *
     * @param Object $params Params
     *
     * @access protected
     *
     * @return void
     */
    protected static function setParams()
    {
        jimport('joomla.application.component.helper');

        static::$params = JComponentHelper::getParams('com_osmeta');
    }

    /**
     * Get Meta Tags
     *
     * @access  public
     *
     * @return object
     */
    public static function getMetatags()
    {
        if (!static::$params) {
            static::setParams();
        }

        $app = JFactory::getApplication();

        $data = new stdClass;
        $data->source = static::$params->get('home_metadata_source', 'default');

        if ($data->source === 'featured' && !$app->isAdmin()) {
            // Get meta data from the first featured article
            $firstItem = static::getFirstFeaturedArticle();

            if ($firstItem) {
                $metadata = json_decode($firstItem->metadata);

                $data->metaTitle = @$metadata->metatitle;
                $data->metaDesc = $firstItem->metadesc;
            }
        } else {
            // Get custom metadata
            $data->metaTitle = static::$params->get('home_metatitle');
            $data->metaDesc = static::$params->get('home_metadesc');
        }

        return $data;
    }

    /**
     * Save meta tags
     *
     * @param string $source          Source (default, custom, featured)
     * @param string $metaTitle       Meta title
     * @param string $metaDescription Meta Description
     *
     * @access  public
     *
     * @return void
     */
    public static function saveMetatags($source, $metaTitle, $metaDescription)
    {
        if (!static::$params) {
            static::setParams();
        }

        static::$params->set('home_metadata_source', $source);
        static::$params->set('home_metatitle', $metaTitle);
        static::$params->set('home_metadesc', $metaDescription);

        $json = static::$params->toString();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . '=' . $db->quote($json))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_osmeta'));
        $db->setQuery($query);
        $db->execute();

        static::setParams();
    }

    /**
     * Get the first featured article
     *
     * @access  public
     *
     * @return stdClass
     */
    public static function getFirstFeaturedArticle()
    {
        $firstItem = null;

        if (JFactory::getApplication()->isAdmin()) {
            $classPrefix = "OSContentModel";
        } else {
            $classPrefix = "ContentModel";
        }

        $model = JModelLegacy::getInstance("featured", $classPrefix, array());
        $featuredItems = $model->getItems();

        if (!empty($featuredItems)) {
            jimport('joomla.database.table');

            $count = count($featuredItems);
            for ($i = 0; $i < $count; $i++) {
                $item = $featuredItems[$i];
                $id = $item->id;
                $article = JTable::getInstance("content");
                $article->load($id);
                if ($article->get("state") == 1) {
                    $firstItem = $item;
                    break;
                }
            }
        }

        return $firstItem;
    }
}
