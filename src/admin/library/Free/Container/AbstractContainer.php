<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSMeta.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\OSMeta\Free\Container;

use Alledia\Framework\Factory as AllediaFactory;
use Joomla\CMS\Application\CMSApplication;

defined('_JEXEC') or die();

/**
 * Abstract Metatags Container Class
 *
 * @since  1.0
 */
abstract class AbstractContainer
{
    /**
     * @var int
     */
    public $code = null;

    /**
     * Container priority
     *
     * @var int
     */
    public $priority = 1;

    /**
     * True, if this content allow to automatically generate
     * title from the content
     *
     * @var bool
     */
    public $supportGenerateTitle = true;

    /**
     * True, if this content allow to automatically generate
     * description from the content
     *
     * @var bool
     */
    public $supportGenerateDescription = true;

    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var \JDatabaseDriver
     */
    protected $dbo = null;

    /**
     * @return void
     * @throws \Exception
     */
    public function __construct()
    {
        $this->app = AllediaFactory::getApplication();
        $this->dbo = AllediaFactory::getDbo();
    }

    /**
     * Method to set the Metadata
     *
     * @param int      $itemId Item ID
     * @param string[] $data   Data
     *
     * @return void
     */
    public function setMetadata($itemId, $data)
    {
        $itemTypeId = $this->getTypeId();
        $db         = AllediaFactory::getDbo();

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
        $db = AllediaFactory::getDbo();

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
            $data = [
                'id'              => 0,
                'item_id'         => 0,
                'metadescription' => '',
                'description'     => '',
                'title'           => '',
                'metatitle'       => ''
            ];
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
        return [
            'metatitle'       => '',
            'metadescription' => ''
        ];
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
     * @return bool
     */
    public static function isAvailable()
    {
        return true;
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
     * @param int[] $ids
     *
     * @return void
     */
    abstract public function copyItemTitleToSearchEngineTitle($ids);

    /**
     * @param int[] $ids
     *
     * @return void
     */
    abstract public function generateDescriptions($ids);

    /**
     * @param int[]     $ids
     * @param string[]  $metatitles
     * @param string[]  $metadescriptions
     * @param ?string[] $aliases
     *
     * @return void
     */
    abstract public function saveMetatags($ids, $metatitles, $metadescriptions, $aliases = []);

    /**
     * @param int $limitStart
     * @param int $limit
     *
     * @return void
     */
    abstract public function getMetatags($limitStart, $limit);

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
     * @param int $limitStart Offset
     * @param int $limit      Limit
     *
     * @return array
     */
    public function getPages($limitStart, $limit)
    {
        return [];
    }

    /**
     * Method to get Filter
     *
     * @access  public
     *
     * @return string
     */
    abstract public function getFilter();
}
