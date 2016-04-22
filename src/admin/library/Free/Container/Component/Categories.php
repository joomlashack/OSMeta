<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace Alledia\OSMeta\Free\Container\Component;

use Alledia\OSMeta\Free\Container\AbstractContainer;
use JRequest;
use JFactory;
use JModelLegacy;
use stdClass;
use JRoute;
use ContentHelperRoute;
use JUri;
use JHtml;
use JText;

// No direct access
defined('_JEXEC') or die();

if (!class_exists('ContentHelperRoute')) {
    require JPATH_SITE . '/components/com_content/helpers/route.php';
}

/**
 * Article Category Metatags Container
 *
 * @since  1.0
 */
class Categories extends AbstractContainer
{
    /**
     * Code
     *
     * @var    int
     * @since  1.0
     */
    public $code = 4;

    /**
     * Get meta tags
     *
     * @param int $lim0   Offset
     * @param int $lim    Limit
     * @param int $filter Filter
     *
     * @access  public
     *
     * @return array
     */
    public function getMetatags($lim0, $lim, $filter = null)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.title,
            c.metadesc, m.title as metatitle , c.extension, c.alias
            FROM
            #__categories c
            LEFT JOIN
            #__osmeta_metadata m ON m.item_id=c.id and m.item_type='{$this->code}' WHERE c.extension='com_content'";

        $search = JRequest::getVar("com_content_filter_search", "");
        $authorId = JRequest::getVar("com_content_filter_authorid", "0");
        $state = JRequest::getVar("com_content_filter_state", "");
        $access = JRequest::getVar("com_content_filter_access", "");

        $comContentFilterShowEmptyDescriptions = JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        if ($search != "") {
            $sql .= " AND (";
            $sql .= " c.title LIKE " . $db->quote('%' . $search . '%');
            $sql .= " OR m.title LIKE " . $db->quote('%' . $search . '%');
            $sql .= " OR c.metadesc LIKE " . $db->quote('%' . $search . '%');
            $sql .= " OR c.alias LIKE " . $db->quote('%' . $search . '%');
            $sql .= " OR c.id = " . $db->quote($search);
            $sql .= ")";
        }

        if ($authorId > 0) {
            $sql .= " AND c.created_user_id=" . $db->quote($authorId);
        }

        switch ($state) {
            case 'P':
                $sql .= " AND c.published=1";
                break;
            case 'U':
                $sql .= " AND c.published=0";
                break;
        }

        if ($comContentFilterShowEmptyDescriptions != "-1") {
            $sql .= " AND (ISNULL(c.metadesc) OR c.metadesc='') ";
        }

        if (!empty($access)) {
            $sql .= " AND c.access = " . $db->quote($access);
        }

        // Sorting
        $order = JRequest::getCmd("filter_order", "title");
        $order_dir = JRequest::getCmd("filter_order_Dir", "ASC");

        switch ($order) {
            case "meta_title":
                $sql .= " ORDER BY metatitle ";
                break;

            case "meta_desc":
                $sql .= " ORDER BY metadesc ";
                break;

            default:
                $sql .= " ORDER BY title ";
                break;
        }

        $order_dir = strtoupper($order_dir);

        if ($order_dir === "ASC") {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        $db->setQuery($sql, $lim0, $lim);
        $rows = $db->loadObjectList();

        if ($db->getErrorNum()) {
            echo $db->stderr();

            return false;
        }

        // Get the total
        $db->setQuery('SELECT FOUND_ROWS();');
        $total = $db->loadResult();

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];

            $row->edit_url = "index.php?option=com_categories&view=category&layout=edit&id={$row->id}"
                . "&extension={$row->extension}";

            // Get the category view url
            $url    = ContentHelperRoute::getCategoryRoute($row->id);
            $url    = JRoute::_($url);
            $uri    = JUri::getInstance();
            $url    = $uri->toString(array('scheme', 'host', 'port')) . $url;
            $url    = str_replace('/administrator/', '/', $url);

            $row->view_url = $url;
        }

        return array(
            'rows'  => $rows,
            'total' => $total
        );
    }

    /**
     * Get meta data by request
     *
     * @param string $query Query
     *
     * @access  public
     *
     * @return array
     */
    public function getMetadataByRequest($query)
    {
        $params = array();
        parse_str($query, $params);
        $metadata = null;

        if (isset($params["id"])) {
            $metadata = $this->getMetadata($params["id"]);
        }

        return $metadata;
    }

    /**
     * Get Pages
     *
     * @param int $lim0   Offset
     * @param int $lim    Limit
     * @param int $filter Filter
     *
     * @access  public
     *
     * @return array
     */
    public function getPages($lim0, $lim, $filter = null)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
            c.id, c.title, c.published,
        c.description AS content
            FROM
            #__categories c WHERE c.extension='com_content'";

        $search = JRequest::getVar("com_content_filter_search", "");
        $authorId = JRequest::getVar("com_content_filter_authorid", "0");
        $state = JRequest::getVar("com_content_filter_state", "");
        $access = JRequest::getVar("com_content_filter_access", "");

        $comContentFilterShowEmptyDescriptions = JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        if ($search != "") {
            if (is_numeric($search)) {
                $sql .= " AND c.id=" . $db->quote($search);
            } else {
                $sql .= " AND c.title LIKE " . $db->quote('%' . $search . '%');
            }
        }

        if ($authorId > 0) {
            $sql .= " AND c.created_user_id=" . $db->quote($authorId);
        }

        switch ($state) {
            case 'P':
                $sql .= " AND c.published=1";
                break;

            case 'U':
                $sql .= " AND c.published=0";
                break;
        }

        if ($comContentFilterShowEmptyDescriptions != "-1") {
            $sql .= " AND (ISNULL(c.metadesc) OR c.metadesc='') ";
        }

        if (!empty($access)) {
            $sql .= " AND c.access = " . $db->quote($access);
        }

        $db->setQuery($sql, $lim0, $lim);
        $rows = $db->loadObjectList();

        if ($db->getErrorNum()) {
            echo $db->stderr();

            return false;
        }

        // Get outgoing links
        for ($i = 0; $i < count($rows); $i++) {
            $rows[$i]->edit_url = "index.php?option=com_categories&view=category&layout=edit&id={$rows[$i]->id}&"
                . "extension={$rows[$i]->extension}";
        }

        return $rows;
    }

    /**
     * Save meta tags
     *
     * @param array $ids              IDs
     * @param array $metatitles       Meta titles
     * @param array $metadescriptions Meta Descriptions
     * @param array $aliases          Aliases
     *
     * @access  public
     *
     * @return void
     */
    public function saveMetatags($ids, $metatitles, $metadescriptions, $aliases)
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDBO();

        for ($i = 0; $i < count($ids); $i++) {
            // Get current category metadata
            $sql = "SELECT metadata, alias FROM #__categories"
                . " WHERE id=" . $db->quote($ids[$i]);
            $db->setQuery($sql);
            $current = $db->loadObject();

            // Update the metadata
            $metadata = json_decode($current->metadata);
            if (!is_object($metadata)) {
                $metadata = new stdClass;
            }
            $metadata->metatitle = $metatitles[$i];
            $metadata = json_encode($metadata);

            $sql = "UPDATE #__categories SET "
                . " metadesc=" . $db->quote($metadescriptions[$i]) . ", "
                . " metadata=" . $db->quote($metadata);

            if (isset($aliases[$i])) {
                if (!empty($aliases[$i])) {
                    $alias = $this->stringURLSafe($aliases[$i]);

                    if ($current->alias !== $alias) {
                        // Check if the alias already exists and ignore it
                        if ($this->isUniqueAlias($alias)) {
                            $sql .= ", alias=" . $db->quote($alias);
                        } else {
                            $app->enqueueMessage(
                                JText::sprintf('COM_OSMETA_WARNING_DUPLICATED_ALIAS', $alias),
                                'warning'
                            );
                        }
                    }

                } else {
                    JFactory::getApplication()->enqueueMessage(
                        JText::_('COM_OSMETA_WARNING_EMPTY_ALIAS'),
                        'warning'
                    );
                }
            }

            $sql .= " WHERE id=" . $db->quote($ids[$i]);
            $db->setQuery($sql);
            $db->query();

            // Insert/Update OS Metadata
            $sql = "INSERT INTO #__osmeta_metadata (item_id,
                item_type, title, description)
                VALUES (
                " . $db->quote($ids[$i]) . ",
                '{$this->code}',
                " . $db->quote($metatitles[$i]) . ",
                " . $db->quote($metadescriptions[$i]) . "
                ) ON DUPLICATE KEY
                    UPDATE
                    title=" . $db->quote($metatitles[$i]) . " ,
                    description=" . $db->quote($metadescriptions[$i]);

            $db->setQuery($sql);
            $db->query();
        }
    }

    /**
     * Method to copy the item title to title
     *
     * @param array $ids IDs list
     *
     * @access  public
     *
     * @return void
     */
    public function copyItemTitleToSearchEngineTitle($ids)
    {
        $db = JFactory::getDBO();

        foreach ($ids as $key => $value) {
            if (!is_numeric($value)) {
                unset($ids[$key]);
            }
        }

        $sql = "SELECT id, title
            FROM  #__categories
            WHERE id IN (" . implode(",", $ids) . ")";

        $db->setQuery($sql);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            if ($item->title != '') {
                $sql = "INSERT INTO #__osmeta_metadata (item_id,
                    item_type, title, description)
                    VALUES (
                    " . $db->quote($item->id) . ",
                    '{$this->code}',
                    " . $db->quote($item->title) . ",
                    ''
                    ) ON DUPLICATE KEY UPDATE title=" . $db->quote($item->title);

                $db->setQuery($sql);
                $db->query();
            }
        }
    }

    /**
     * Method to generate descriptions
     *
     * @param array $ids IDs list
     *
     * @access  public
     *
     * @return void
     */
    public function generateDescriptions($ids)
    {
        jimport('legacy.model.legacy');

        $max_description_length = 500;
        $model = JModelLegacy::getInstance("options", "OSModel");
        $params = $model->getOptions();
        $max_description_length = $params->max_description_length ?
            $params->max_description_length : $max_description_length;

        $db = JFactory::getDBO();

        foreach ($ids as $key => $value) {
            if (!is_numeric($value)) {
                unset($ids[$key]);
            }
        }

        $sql = "SELECT id, description as introtext FROM  #__categories WHERE id IN (" . implode(",", $ids) . ")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            if ($item->introtext != '') {
                $introtext = strip_tags($item->introtext);

                if (strlen($introtext) > $max_description_length) {
                    $introtext = substr($introtext, 0, $max_description_length);
                }

                $sql = "INSERT INTO #__osmeta_metadata (item_id,
                    item_type, title, description)
                    VALUES (
                    " . $db->quote($item->id) . ",
                    '{$this->code}',

                    '',
                    " . $db->quote($introtext) . "
                    ) ON DUPLICATE KEY UPDATE description=" . $db->quote($introtext);

                $db->setQuery($sql);
                $db->query();

                $sql = "UPDATE #__categories SET metadesc=" . $db->quote($introtext) . "
                    WHERE id=" . $db->quote($item->id);

                $db->setQuery($sql);
                $db->query();
            }
        }
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
        $db = JFactory::getDBO();

        $search = JRequest::getVar("com_content_filter_search", "");
        $authorId = JRequest::getVar("com_content_filter_authorid", "0");
        $state = JRequest::getVar("com_content_filter_state", "");
        $access = JRequest::getVar("com_content_filter_access", "");
        $comContentFilterShowEmptyDescriptions = JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        $result = JText::_('COM_OSMETA_FILTER_LABEL') . ':
            <input type="text" name="com_content_filter_search" id="search" value="' . $search
            . '" class="text_area" onchange="document.adminForm.submit();" '
            . ' title="' . JText::_('COM_OSMETA_FILTER_DESC') . '"/>
            <button id="Go" class="btn btn-small" onclick="this.form.submit();">' . JText::_('COM_OSMETA_GO_LABEL') . '</button>
            <button class="btn btn-small" onclick="document.getElementById(\'search\').value=\'\';
                this.form.getElementById(\'filter_sectionid\').value=\'-1\';
                this.form.getElementById(\'catid\').value=\'0\';
                this.form.getElementById(\'filter_authorid\').value=\'0\';
                this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">' . JText::_('COM_OSMETA_RESET_LABEL') . '</button>

            &nbsp;&nbsp;&nbsp;';

        $descriptionChecked = $comContentFilterShowEmptyDescriptions != "-1" ? 'checked="yes" ' : '';

        $result .= '
            <select name="com_content_filter_state" id="filter_state"
                class="inputbox" size="1" onchange="submitform();">
                <option value=""  >' . JText::_('COM_OSMETA_SELECT_STATE') . '</option>
                <option value="P" ' . ($state == 'P' ? 'selected="selected"' : '') . '>' . JText::_('COM_OSMETA_PUBLISHED') . '</option>
                <option value="U" ' . ($state == 'U' ? 'selected="selected"' : '') . '>' . JText::_('COM_OSMETA_UNPUBLISHED') . '</option>
            </select>
            <br/>
            <label>' . JText::_('COM_OSMETA_SHOW_ONLY_EMPTY_DESCRIPTIONS') . '</label>
            <input type="checkbox" onchange="document.adminForm.submit();"
                name="com_content_filter_show_empty_descriptions" ' . $descriptionChecked . '/>&nbsp;';

        $result .= JHtml::_('access.level', 'com_content_filter_access', $access, 'onchange="submitform();"');

        return $result;
    }

    /**
     * Method to get the item data
     *
     * @param int $id Item Id
     *
     * @access  public
     *
     * @return array
     */
    public function getItemData($id)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT c.id as id, c.title as title,
            c.metadesc as metadescription, m.title as metatitle
            FROM
            #__categories c
            LEFT JOIN
            #__osmeta_metadata m ON m.item_id=c.id and m.item_type='{$this->code}' WHERE c.id=" . $db->quote($id);
        $db->setQuery($sql);

        return $db->loadAssoc();
    }

    /**
     * Method to set the item data
     *
     * @param int   $id   Item Id
     * @param array $data Item Data
     *
     * @access  public
     *
     * @return void
     */
    public function setItemData($id, $data)
    {
        $db = JFactory::getDBO();
        $sql = "UPDATE #__categories SET
            `title` = " . $db->quote($data["title"]) . ",
            `metadesc` = " . $db->quote($data["metadescription"]) . "
            WHERE `id`=" . $db->quote($id);
        $db->setQuery($sql);
        $db->query();
        $this->saveMetadata($id, $this->code, $data);
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
        $params = array();
        parse_str($url, $params);

        if (isset($params["id"]) && $params["id"]) {
            $this->setMetadata($params["id"], $data);
        }
    }

    /**
     * Method to check if an alias already exists
     *
     * @param  string $alias The original alias
     * @return string        The new alias, incremented, if needed
     */
    public function isUniqueAlias($alias)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__categories')
            ->where('extension = ' . $db->quote('com_content'))
            ->where('alias = ' . $db->quote($alias));

        $db->setQuery($query);
        $count = (int) $db->loadResult();

        return $count === 0;
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
