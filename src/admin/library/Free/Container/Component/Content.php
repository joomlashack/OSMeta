<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\OSMeta\Free\Container\Component;

use Alledia\OSMeta\Free\Container\AbstractContainer;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

defined('_JEXEC') or die();

/**
 * Article Metatags Container
 *
 * @since  1.0
 */
class Content extends AbstractContainer
{
    /**
     * @inheritdoc
     */
    public $code = 1;

    /**
     * @inheritDoc
     */
    public function getMetatags($limitStart, $limit)
    {
        $app = $this->app;
        $db  = $this->dbo;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS c.id, c.title,
            c.metadesc, m.title as metatitle, c.alias, c.catid
            FROM `#__content` c
            LEFT JOIN `#__categories` cc ON cc.id=c.catid
            LEFT JOIN `#__osmeta_metadata` m ON m.item_id=c.id and m.item_type=1 WHERE 1';

        $search   = $app->input->getString('com_content_filter_search', '');
        $catId    = $app->input->getString('com_content_filter_catid', '0');
        $level    = $app->input->getString('com_content_filter_level', '0');
        $authorId = $app->input->getString('com_content_filter_authorid', '0');
        $state    = $app->input->getString('com_content_filter_state', '');
        $access   = $app->input->getString('com_content_filter_access', '');

        $comContentFilterShowEmptyDescriptions = $app->input->getString(
            'com_content_filter_show_empty_descriptions',
            '-1'
        );

        if ($search != '') {
            $sql .= ' AND (';
            $sql .= ' c.title LIKE ' . $db->quote('%' . $search . '%');
            $sql .= ' OR m.title LIKE ' . $db->quote('%' . $search . '%');
            $sql .= ' OR c.metadesc LIKE ' . $db->quote('%' . $search . '%');
            $sql .= ' OR c.alias LIKE ' . $db->quote('%' . $search . '%');
            $sql .= ' OR c.id = ' . $db->quote($search);
            $sql .= ')';
        }

        $baselevel = 1;

        if ($catId > 0) {
            $db->setQuery('SELECT * from #__categories where id=' . $db->quote($catId));
            $cat_tbl   = $db->loadObject();
            $rgt       = $cat_tbl->rgt;
            $lft       = $cat_tbl->lft;
            $baselevel = (int)$cat_tbl->level;
            $sql       .= ' AND cc.lft >= ' . (int)$lft;
            $sql       .= ' AND cc.rgt <= ' . (int)$rgt;
        }

        if ($level > 0) {
            $sql .= ' AND cc.level <=' . ((int)$level + $baselevel - 1);
        }

        if ($authorId > 0) {
            $sql .= ' AND c.created_by=' . $db->quote($authorId);
        }

        switch ($state) {
            case 'U':
                $sql .= ' AND c.state=0';
                break;

            case 'A':
                $sql .= ' AND c.state=-1';
                break;

            case 'D':
                $sql .= ' AND c.state=-2';
                break;

            case 'All':
                break;

            case 'P':
            default:
                $sql .= ' AND c.state=1';
                break;
        }

        if ($comContentFilterShowEmptyDescriptions != '-1') {
            $sql .= " AND (ISNULL(c.metadesc) OR c.metadesc='') ";
        }

        if (!empty($access)) {
            $sql .= ' AND c.access = ' . $db->quote($access);
        }

        // Sorting
        $order     = $app->input->getCmd('filter_order', 'title');
        $order_dir = $app->input->getCmd('filter_order_Dir', 'ASC');

        switch ($order) {
            case 'meta_title':
                $sql .= ' ORDER BY metatitle ';
                break;

            case 'meta_desc':
                $sql .= ' ORDER BY metadesc ';
                break;

            default:
                $sql .= ' ORDER BY title ';
                break;

        }

        $order_dir = strtoupper($order_dir);

        if ($order_dir === 'ASC') {
            $sql .= ' ASC';
        } else {
            $sql .= ' DESC';
        }

        $db->setQuery($sql, $limitStart, $limit);
        $rows = $db->loadObjectList();

        // Get the total
        $db->setQuery('SELECT FOUND_ROWS();');
        $total = $db->loadResult();

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];

            $row->edit_url = "index.php?option=com_content&task=article.edit&id={$row->id}";

            $row->view_url = Route::link(
                'site',
                RouteHelper::getArticleRoute($row->id . ':' . urlencode($row->alias), $row->catid),
                true,
                Route::TLS_IGNORE,
                true
            );
        }

        return [
            'rows'  => $rows,
            'total' => $total,
        ];
    }

    /**
     * @param int $limitStart Offset
     * @param int $limit      Limit
     *
     * @return array
     * @throws \Exception
     */
    public function getPages($limitStart, $limit)
    {
        $app = $this->app;
        $db  = $this->dbo;

        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.title, c.state,
            if (c.fulltext != '', c.fulltext, c.introtext) AS content
            FROM #__content c
            LEFT JOIN #__categories cc ON cc.id=c.catid
            WHERE 1
            ";

        $search   = $app->input->getString('com_content_filter_search', '');
        $catId    = $app->input->getString('com_content_filter_catid', '0');
        $authorId = $app->input->getString('com_content_filter_authorid', '0');
        $level    = $app->input->getString('com_content_filter_level', '0');
        $state    = $app->input->getString('com_content_filter_state', '');
        $access   = $app->input->getString('com_content_filter_access', '');

        $comContentFilterShowEmptyDescriptions = $app->input->getString(
            'com_content_filter_show_empty_descriptions',
            '-1'
        );

        if ($search != '') {
            if (is_numeric($search)) {
                $sql .= ' AND c.id=' . $db->quote($search);
            } else {
                $sql .= ' AND c.title LIKE ' . $db->quote('%' . $search . '%');
            }
        }

        $baselevel = 1;

        if ($catId > 0) {
            $db->setQuery('SELECT * from #__categories where id=' . $db->quote($catId));
            $cat_tbl   = $db->loadObject();
            $rgt       = $cat_tbl->rgt;
            $lft       = $cat_tbl->lft;
            $baselevel = (int)$cat_tbl->level;
            $sql       .= ' AND cc.lft >= ' . (int)$lft;
            $sql       .= ' AND cc.rgt <= ' . (int)$rgt;
        }

        if ($level > 0) {
            $sql .= ' AND cc.level <=' . ((int)$level + $baselevel - 1);
        }

        if ($authorId > 0) {
            $sql .= ' AND c.created_by=' . $db->quote($authorId);
        }

        switch ($state) {
            case 'U':
                $sql .= ' AND c.state=0';
                break;

            case 'A':
                $sql .= ' AND c.state=-1';
                break;

            case 'D':
                $sql .= ' AND c.state=-2';
                break;

            case 'All':
                break;

            case 'P':
            default:
                $sql .= ' AND c.state=1';
                break;
        }

        if ($comContentFilterShowEmptyDescriptions != '-1') {
            $sql .= " AND (ISNULL(c.metadesc) OR c.metadesc='') ";
        }

        if (!empty($access)) {
            $sql .= ' AND c.access = ' . $db->quote($access);
        }

        $db->setQuery($sql, $limitStart, $limit);
        $rows = $db->loadObjectList();

        // Get outgoing links
        for ($i = 0; $i < count($rows); $i++) {
            $rows[$i]->edit_url = "index.php?option=com_content&task=article.edit&id={$rows[$i]->id}";
        }

        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function saveMetatags($ids, $metatitles, $metadescriptions, $aliases = [])
    {
        $app = $this->app;
        $db  = $this->dbo;

        for ($i = 0; $i < count($ids); $i++) {
            // Get current article metadata
            $sql = 'SELECT metadata, alias FROM #__content'
                . ' WHERE id=' . $db->quote($ids[$i]);
            $db->setQuery($sql);
            $current = $db->loadObject();

            // Update the metadata
            $metadata = json_decode($current->metadata);
            if (!is_object($metadata)) {
                $metadata = (object)[];
            }
            $metadata->metatitle = $metatitles[$i];
            $metadata            = json_encode($metadata);

            $sql = 'UPDATE #__content SET '
                . ' metadesc=' . $db->quote($metadescriptions[$i]) . ', '
                . ' metadata=' . $db->quote($metadata);

            if (isset($aliases[$i])) {
                if (!empty($aliases[$i])) {
                    $alias = $this->stringURLSafe($aliases[$i]);

                    if ($current->alias !== $alias) {
                        // Check if the alias already exists and ignore it
                        if ($this->isUniqueAlias($alias)) {
                            $sql .= ', alias=' . $db->quote($alias);
                        } else {
                            $app->enqueueMessage(
                                Text::sprintf('COM_OSMETA_WARNING_DUPLICATED_ALIAS', $alias),
                                'warning'
                            );
                        }
                    }
                } else {
                    $this->app->enqueueMessage(
                        Text::_('COM_OSMETA_WARNING_EMPTY_ALIAS'),
                        'warning'
                    );
                }
            }

            $sql .= ' WHERE id=' . $db->quote($ids[$i]);
            $db->setQuery($sql);
            $db->execute();

            // Insert/Update OS Metadata
            $sql = 'INSERT INTO #__osmeta_metadata (item_id,
                item_type, title, description)
                VALUES (
                ' . $db->quote($ids[$i]) . ',
                1,
                ' . $db->quote($metatitles[$i]) . ',
                ' . $db->quote($metadescriptions[$i]) . '
                ) ON DUPLICATE KEY UPDATE title=' . $db->quote($metatitles[$i]) . ' ,
                    description=' . $db->quote($metadescriptions[$i]);

            $db->setQuery($sql);
            $db->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public function copyItemTitleToSearchEngineTitle($ids)
    {
        $db = $this->dbo;

        foreach ($ids as $key => $value) {
            if (!is_numeric($value)) {
                unset($ids[$key]);
            }
        }

        $sql = 'SELECT id, title FROM  #__content WHERE id IN (' . implode(',', $ids) . ')';
        $db->setQuery($sql);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            if ($item->title != '') {
                $sql = 'INSERT INTO #__osmeta_metadata (item_id,
                    item_type, title, description)
                    VALUES (
                    ' . $db->quote($item->id) . ',
                    1,
                    ' . $db->quote($item->title) . ",
                    ''
                    ) ON DUPLICATE KEY UPDATE title=" . $db->quote($item->title);

                $db->setQuery($sql);
                $db->execute();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function generateDescriptions($ids)
    {
        $max_description_length = 500;
        $model                  = BaseDatabaseModel::getInstance('options', 'OSModel');
        $params                 = $model->getOptions();
        $max_description_length = $params->max_description_length ?: $max_description_length;

        $db = $this->dbo;

        foreach ($ids as $key => $value) {
            if (!is_numeric($value)) {
                unset($ids[$key]);
            }
        }

        $sql = 'SELECT id, introtext FROM  #__content WHERE id IN (' . implode(',', $ids) . ')';
        $db->setQuery($sql);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            if ($item->introtext != '') {
                $introtext = strip_tags($item->introtext);

                if (strlen($introtext) > $max_description_length) {
                    $introtext = substr($introtext, 0, $max_description_length);
                }

                $sql = 'INSERT INTO #__osmeta_metadata (item_id,
                    item_type, title, description)
                    VALUES (
                    ' . $db->quote($item->id) . ",
                    1,

                    '',
                    " . $db->quote($introtext) . '
                    ) ON DUPLICATE KEY UPDATE description=' . $db->quote($introtext);

                $db->setQuery($sql);
                $db->execute();

                $sql = 'UPDATE #__content SET metadesc=' . $db->quote($introtext) . '
                    WHERE id=' . $db->quote($item->id);

                $db->setQuery($sql);
                $db->execute();
            }
        }
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getFilter()
    {
        $app = $this->app;

        $search = $app->input->getString('com_content_filter_search', '');
        $catId  = $app->input->getString('com_content_filter_catid', '0');
        $level  = $app->input->getString('com_content_filter_level', '0');
        $access = $app->input->getString('com_content_filter_access', '');

        // Levels filter.
        $levels = [
            HTMLHelper::_('select.option', '1', Text::_('J1')),
            HTMLHelper::_('select.option', '2', Text::_('J2')),
            HTMLHelper::_('select.option', '3', Text::_('J3')),
            HTMLHelper::_('select.option', '4', Text::_('J4')),
            HTMLHelper::_('select.option', '5', Text::_('J5')),
            HTMLHelper::_('select.option', '6', Text::_('J6')),
            HTMLHelper::_('select.option', '7', Text::_('J7')),
            HTMLHelper::_('select.option', '8', Text::_('J8')),
            HTMLHelper::_('select.option', '9', Text::_('J9')),
            HTMLHelper::_('select.option', '10', Text::_('J10')),
        ];

        $state                                 = $app->input->getString('com_content_filter_state', '');
        $comContentFilterShowEmptyDescriptions = $app->input->getString('com_content_filter_show_empty_descriptions',
            '-1');

        $result = '<div class="btn-wrapper input-append">
			<input type="text"
					name="com_content_filter_search"
					id="search"
					value="' . $search . '"
					placeholder="' . Text::_('COM_OSMETA_SEARCH') . '"
					data-original-title=""
					title="' . Text::_('COM_OSMETA_FILTER_DESC') . '"
					onchange="document.adminForm.submit();">
				<button type="submit"
						class="btn hasTooltip"
						id="Go"
						title="" aria-label="' . $search . '"
						data-original-title="' . $search . '"
						onclick="this.form.submit();">
				<span class="icon-search" aria-hidden="true"></span>
			</button>
		</div>
		<div class="btn-wrapper">
            <button class="btn" onclick="document.getElementById(\'search\').value=\'\';
                this.form.getElementById(\'filter_sectionid\').value=\'-1\';
                this.form.getElementById(\'catid\').value=\'0\';
                this.form.getElementById(\'filter_authorid\').value=\'0\';
                this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">
                ' . Text::_('COM_OSMETA_RESET_LABEL') . '
            </button>

            &nbsp;&nbsp;&nbsp;
        </div>
        <div class="clearfix"></div>';

        $result .= '<div class="om-filter-container">';

        $result .= '<select name="com_content_filter_catid" class="inputbox" onchange="this.form.submit()">' .
            '<option value="">' . Text::_('COM_OSMETA_SELECT_CATEGORY') . '</option>' .
            HTMLHelper::_('select.options', HTMLHelper::_('category.options', 'com_content'), 'value', 'text', $catId) .
            '</select>';

        $result .= '<select name="com_content_filter_level" class="inputbox" onchange="this.form.submit()">' .
            '<option value="">' . Text::_('COM_OSMETA_SELECT_MAX_LEVELS') . '</option>' .
            HTMLHelper::_('select.options', $levels, 'value', 'text', $level) .
            '</select>';

        $descriptionChecked = $comContentFilterShowEmptyDescriptions != '-1' ? 'checked="yes" ' : '';

        $result .= '<select name="com_content_filter_state" id="filter_state" class="inputbox" size="1"
            onchange="this.form.submit()">
                <option value=""  >' . Text::_('COM_OSMETA_SELECT_STATE') . '</option>
                <option value="P" ' . ($state == 'P' ? 'selected="selected"' : '') . '>' . Text::_('COM_OSMETA_PUBLISHED') . '</option>
                <option value="U" ' . ($state == 'U' ? 'selected="selected"' : '') . '>' . Text::_('COM_OSMETA_UNPUBLISHED') . '</option>
                <option value="A" ' . ($state == 'A' ? 'selected="selected"' : '') . '>' . Text::_('COM_OSMETA_ARCHIVED') . '</option>
                <option value="D" ' . ($state == 'D' ? 'selected="selected"' : '') . '>' . Text::_('COM_OSMETA_TRASHED') . '</option>
                <option value="All" ' . ($state == 'All' ? 'selected="selected"' : '') . '>' . Text::_('COM_OSMETA_ALL') . '</option>
            </select>';

        $result .= HTMLHelper::_('access.level', 'com_content_filter_access', $access, 'onchange="submitform();"');

        $result .= '<label>' . Text::_('COM_OSMETA_SHOW_ONLY_EMPTY_DESCRIPTIONS') . '</label>
            <input type="checkbox"
                    onchange="document.adminForm.submit();"
                    name="com_content_filter_show_empty_descriptions"
                    ' . $descriptionChecked . '/>';

        $result .= '</div>';

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata($itemId, $data)
    {
        $db  = $this->dbo;
        $sql = 'UPDATE #__content SET ' .
            (isset($data['title']) && $data['title'] ?
                '`title` = ' . $db->quote($data['title']) . ',' : '') . '
            `metadesc` = ' . $db->quote($data['metadescription']) . '
            WHERE `id`=' . $db->quote($itemId);
        $db->setQuery($sql);
        $db->execute();

        parent::setMetadata($itemId, $data);
    }

    /**
     * @inheritDoc
     */
    public function getMetadataByRequest($query)
    {
        $params = [];
        parse_str($query, $params);

        $metadata = $this->getDefaultMetadata();

        if (isset($params['id'])) {
            $metadata = $this->getMetadata($params['id']);
        }

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
        $params = [];
        parse_str($url, $params);

        if (isset($params['id']) && $params['id']) {
            $this->setMetadata($params['id'], $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function isUniqueAlias($alias)
    {
        $db = $this->dbo;

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__content')
            ->where('alias = ' . $db->quote($alias));

        $db->setQuery($query);
        $count = (int)$db->loadResult();

        return $count === 0;
    }
}
