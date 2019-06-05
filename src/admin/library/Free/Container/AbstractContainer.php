<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2019 Joomlashack.com. All rights reserved
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

namespace Alledia\OSMeta\Free\Container;

use JFactory;

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
        $db         = JFactory::getDBO();

        // Save metatitles and metadata
        $sql = "INSERT INTO #__osmeta_metadata
            (title,
             description,
             item_id,
             item_type)
            VALUES (
              " . $db->quote($data["metatitle"]) . " ,
              " . $db->quote($data["metadescription"]) . ",
              " . $db->quote((int)$itemId) . ",
              " . $db->quote($itemTypeId) . ")
            ON DUPLICATE KEY UPDATE title=" . $db->quote($data["metatitle"]) . ",
            description = " . $db->quote($data["metadescription"]);
        $db->setQuery($sql);
        $db->execute();

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

        $sql = "SELECT m.item_id as id,
                m.item_id,
                m.description as metadescription,
                m.description,
                m.title as metatitle,
                m.title
                FROM #__osmeta_metadata m
                WHERE m.item_id=" . $db->quote($id) . "
                    AND m.item_type=" . $db->quote($this->getTypeId());
        $db->setQuery($sql);

        $data = $db->loadAssoc();

        if (empty($data)) {
            $data = array(
                'id'              => 0,
                'item_id'         => 0,
                'metadescription' => '',
                'description'     => '',
                'title'           => '',
                'metatitle'       => ''
            );
        }

        return $data;
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
     * @param string $string The original string
     *
     * @return string         The alias
     */
    public function stringURLSafe($string)
    {
        $string = \JApplicationHelper::stringURLSafe($string);

        return $string;
    }

    /**
     * Return an array with blank metadata
     */
    public function getDefaultMetadata()
    {
        return array(
            'metatitle'       => '',
            'metadescription' => ''
        );
    }

    /**
     * Make sure we have the required indexes.
     */
    public function verifyMetadata($metadata)
    {
        if (!isset($metadata['metatitle'])) {
            $metadata['metatitle'] = '';
        }

        if (!isset($metadata['metadescription'])) {
            $metadata['metadescription'] = '';
        }

        return $metadata;
    }

    /**
     * Method to check if an alias already exists
     *
     * @param string $alias The original alias
     *
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
