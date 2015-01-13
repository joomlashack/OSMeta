<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace Alledia\OSMeta\Free\Container;

use JFactory;

// No direct access
defined('_JEXEC') or die();

/**
 * Abstract Metatags Container Class
 *
 * @since  1.0
 */
abstract class AbstractContainer
{
    /**
     * Container priority
     *
     * @var integer
     */
    public $priority = 1;

    /**
     * True, if this content allow to automatically generate
     * title from the content
     *
     * @var boolean
     */
    public $supportGenerateTitle = true;

    /**
     * True, if this content allow to automatically generate
     * description from the content
     *
     * @var boolean
     */
    public $supportGenerateDescription = true;

    /**
     * Method to set the Metadata
     *
     * @param int   $itemId Item ID
     * @param array $data   Data
     *
     * @access  public
     *
     * @return void
     */
    public function setMetadata($itemId, $data)
    {
        $itemTypeId = $this->getTypeId();
        $db = JFactory::getDBO();

        // Save metatitles and metadata
        $sql = "INSERT INTO #__osmeta_metadata
            (title,
             description,
             item_id,
             item_type)
            VALUES (
              " . $db->quote($data["metatitle"]) . " ,
              " . $db->quote($data["metadescription"]) . ",
              " . $db->quote($itemId) . ",
              " . $db->quote($itemTypeId) . ")
            ON DUPLICATE KEY UPDATE title=" . $db->quote($data["metatitle"]) . ",
            description = " . $db->quote($data["metadescription"]);
        $db->setQuery($sql);
        $db->query();

        if ($db->getErrorNum()) {
            echo $db->stderr();

            return false;
        }
    }

    /**
     * Method to get the Metadata
     *
     * @param int $id Item ID
     *
     * @access  public
     *
     * @return array
     */
    public function getMetadata($id)
    {
        $db = JFactory::getDBO();

        $sql = "SELECT m.item_id as id, m.item_id,
                m.description as metadescription,
                m.description,
                m.title as metatitle,
                m.title
                FROM #__osmeta_metadata m
                WHERE m.item_id=" . $db->quote($id) . "
                    AND m.item_type=" . $db->quote($this->getTypeId());
        $db->setQuery($sql);

        return $db->loadAssoc();
    }

    /**
     * Method to get the type id
     *
     * @access  public
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->code;
    }

    /**
     * Returns an alias, based on a string
     *
     * @param  string $string The original string
     * @return string         The alias
     */
    public function stringURLSafe($string)
    {
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $string = \JApplication::stringURLSafe($string);
        } else {
            jimport('joomla.filter.output');
            $string = \JFilterOutput::stringURLSafe($string);
        }

        return $string;
    }

    /**
     * Method to check if an alias already exists
     *
     * @param  string $alias The original alias
     * @return string        The new alias, incremented, if needed
     */
    abstract public function isUniqueAlias($alias);

    /**
     * Method to get the Metadata By Request
     *
     * @param string $query Query
     *
     * @access  public
     *
     * @return void
     */
    abstract public function getMetadataByRequest($query);

    /**
     * Stores item metadata
     *
     * $data should contain followin keys:
     * - metatitle
     * - metadescription
     *
     * @param string $url  Query string
     * @param array  $data Data array
     *
     * @access  public
     *
     * @return void
     */
    abstract public function setMetadataByRequest($url, $data);

    /**
     * Method to get Filter
     *
     * @access  public
     *
     * @return string
     */
    abstract public function getFilter();
}
