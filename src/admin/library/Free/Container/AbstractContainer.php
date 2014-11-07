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
     * Method to set the Metadata
     *
     * @param int   $itemId Item ID
     * @param array $data   Data
     *
     * @access	public
     *
     * @return void
     */
    public function setMetadata($itemId, $data)
    {
        $itemTypeId = $this->getTypeId();
        $keywords = $data["metakeywords"];

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

        // Save keywords
        $this->saveKeywords($keywords, $itemId, $itemTypeId);
    }

    /**
     * Method to save the Keywords
     *
     * @param string $keywords   Keywords as CSV
     * @param int    $itemId     Item Id
     * @param string $itemTypeId Item Type Id
     *
     * @access	public
     *
     * @return void
     */
    public function saveKeywords($keywords, $itemId, $itemTypeId)
    {
        $db = JFactory::getDBO();

        $sql = "DELETE FROM #__osmeta_keywords_items
            WHERE item_id=" . $db->quote($itemId) . " AND item_type_id=" . $db->quote($itemTypeId);
        $db->setQuery($sql);
        $db->query();

        if ($db->getErrorNum()) {
            echo $db->stderr();

            return false;
        }

        $keywords_arr = explode(",", $keywords);

        foreach ($keywords_arr as $keyword) {
            $keyword = trim($keyword);

            if (!$keyword) {
                continue;
            }

            $sql = "SELECT id FROM #__osmeta_keywords WHERE name=" . $db->quote($keyword);
            $db->setQuery($sql);
            $id = $db->loadResult();

            if (!$id) {
                $sql = "INSERT INTO #__osmeta_keywords (name) VALUES (" . $db->quote($keyword) . ")";
                $db->setQuery($sql);
                $db->query();

                $id = $db->insertid();
            }

            $sql = "INSERT IGNORE INTO #__osmeta_keywords_items (keyword_id, item_id, item_type_id)
                VALUES (" . $db->quote($id) . ", " . $db->quote($itemId) . "," . $db->quote($itemTypeId) . ")";
            $db->setQuery($sql);
            $db->query();

            if ($db->getErrorNum()) {
                echo $db->stderr();

                return false;
            }
        }

        $sql = "DELETE FROM #__osmeta_keywords
        	WHERE NOT EXISTS
        		(SELECT 1 FROM #__osmeta_keywords_items
        			WHERE keyword_id=#__osmeta_keywords.id)";
        $db->setQuery($sql);
        $db->query();
    }

    /**
     * Method to get the Metadata
     *
     * @param int $id Item ID
     *
     * @access	public
     *
     * @return array
     */
    public function getMetadata($id)
    {
        $db = JFactory::getDBO();

        $sql = "SELECT m.item_id as id, m.item_id,
            (SELECT GROUP_CONCAT(k.name SEPARATOR ',')
                FROM #__osmeta_keywords k,
                    #__osmeta_keywords_items ki
                WHERE ki.item_id=m.item_id and ki.item_type_id=" . $db->quote($this->getTypeId()) . "
                    AND ki.keyword_id=k.id
                    ) AS metakeywords,
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
     * Method to get the Type Id
     *
     * @access	public
     *
     * @return void
     */
    abstract public function getTypeId();

    /**
     * Method to get the Metadata By Request
     *
     * @param string $query Query
     *
     * @access	public
     *
     * @return void
     */
    abstract public function getMetadataByRequest($query);

    /**
     * Stores item metadata
     *
     * $data should contain followin keys:
     * - metatitle
     * - metakeywords
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
     * Method to check if the container is available
     *
     * @access	public
     *
     * @return void
     */
    abstract public function isAvailable();
}
