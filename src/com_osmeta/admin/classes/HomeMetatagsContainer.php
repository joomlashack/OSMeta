<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Homepage Metatags Container
 *
 * @since  1.0.0
 */
abstract class HomeMetatagsContainer
{
    /**
     * Params
     *
     * @var    Object
     * @since  1.0.0
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
            require_once realpath(dirname(__FILE__) . '/../models/model.php');

            $model = OSModel::getInstance("featured", "contentModel", array());
            $featuredItems = $model->getItems();

            if (!empty($featuredItems)) {
                $firstItem = $featuredItems[0];

                if ($firstItem) {
                    $metadata = json_decode($firstItem->metadata);

                    $data->titleTag = @$metadata->title_tag;
                    $data->metaTitle = @$metadata->metatitle;
                    $data->metaDesc = $firstItem->metadesc;
                    $data->metaKey = $firstItem->metakey;
                }
            }
        } else {
            // Get custom metadata
            $data->titleTag = static::$params->get('home_titletag');
            $data->metaTitle = static::$params->get('home_metatitle');
            $data->metaDesc = static::$params->get('home_metadesc');
            $data->metaKey = static::$params->get('home_metakey');
        }

        return $data;
    }

    /**
     * Save meta tags
     *
     * @param string $source          Source (default, custom, featured)
     * @param string $metaTitle       Meta title
     * @param string $metaDescription Meta Description
     * @param string $metaKey         Meta Key
     * @param string $titleTag        Title tag
     *
     * @access  public
     *
     * @return void
     */
    public static function saveMetatags($source, $metaTitle, $metaDescription, $metaKey, $titleTag)
    {
        if (!static::$params) {
            static::setParams();
        }

        static::$params->set('home_metadata_source', $source);
        static::$params->set('home_metatitle', $metaTitle);
        static::$params->set('home_metadesc', $metaDescription);
        static::$params->set('home_metakey', $metaKey);
        static::$params->set('home_titletag', $titleTag);

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
}
