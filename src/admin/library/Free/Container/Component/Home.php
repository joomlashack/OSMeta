<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace Alledia\OSMeta\Free\Container\Component;

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
class Home extends AbstractContainer
{
    /**
     * Code
     *
     * @var    int
     * @since  1.0
     */
    public $code = 6;

    /**
     * Params
     *
     * @var    Object
     * @since  1.0
     */
    public $params = null;

    /**
     * True, if this content allow to automatically generate
     * title from the content
     *
     * @var boolean
     */
    public $supportGenerateTitle = false;

    /**
     * True, if this content allow to automatically generate
     * description from the content
     *
     * @var boolean
     */
    public $supportGenerateDescription = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        jimport('joomla.application.component.helper');

        $this->params = JComponentHelper::getParams('com_osmeta');
    }

    /**
     * Get Meta Tags
     *
     * @access  public
     *
     * @return object
     */
    public function getMetatags()
    {
        $app = JFactory::getApplication();

        $data = new stdClass;
        $data->source = $this->params->get('home_metadata_source', 'default');

        if ($data->source === 'featured' && !$app->isAdmin()) {
            // Get meta data from the first featured article
            $firstItem = $this->getFirstFeaturedArticle();

            if ($firstItem) {
                $metadata = json_decode($firstItem->metadata);

                $data->metaTitle = @$metadata->metatitle;
                $data->metaDesc = $firstItem->metadesc;
            }
        } else {
            // Get custom metadata
            $data->metaTitle = $this->params->get('home_metatitle');
            $data->metaDesc = $this->params->get('home_metadesc');
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
    public function saveMetatags($source, $metaTitle, $metaDescription)
    {
        $this->params->set('home_metadata_source', $source);
        $this->params->set('home_metatitle', $metaTitle);
        $this->params->set('home_metadesc', $metaDescription);

        $json = $this->params->toString();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . '=' . $db->quote($json))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_osmeta'));
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Get the first featured article
     *
     * @access  public
     *
     * @return stdClass
     */
    public function getFirstFeaturedArticle()
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

    /**
     * Method to get Filter
     *
     * @access  public
     *
     * @return string
     */
    public function getFilter()
    {
        return '';
    }

    /**
     * Method to get Metadata
     *
     * @param string $query Query
     *
     * @access  public
     *
     * @return array
     */
    public function getMetadataByRequest($query)
    {
        $metadata = $this->getMetadata();

        return $metadata;
    }

    /**
     * Method to set Metadata by request
     *
     * @param string $url  URL
     * @param array  $data Data
     *
     * @access  public
     *
     * @return void
     */
    public function setMetadataByRequest($url, $data)
    {
        $homeSource = JRequest::getVar('home_metadata_source', 'default', '', 'string');
        $homeMetaTitle = JRequest::getVar('home_metatitle', '', '', 'string');
        $homeMetaDescription = JRequest::getVar('home_metadesc', '', '', 'string');

        $this->saveMetatags($homeSource, $homeMetaTitle, $homeMetaDescription);
    }

    /**
     * Method to check if an alias already exists
     *
     * @param  string $alias The original alias
     * @return string        The new alias, incremented, if needed
     */
    public function isUniqueAlias($alias)
    {
        return $alias;
    }

    /**
     * Check if the component is available
     *
     * @return boolean
     */
    public static function isAvailable()
    {
        return true;
    }
}
