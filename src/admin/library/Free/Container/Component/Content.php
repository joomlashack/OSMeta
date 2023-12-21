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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

class Content extends AbstractContainer
{
    /**
     * @inheritdoc
     */
    public $code = 1;

    /**
     * @inheritDoc
     */
    public function getMetatags(int $limitStart, int $limit): array
    {
        $db = $this->dbo;

        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('c.id'),
                $db->quoteName('c.title'),
                $db->quoteName('c.metadesc'),
                sprintf('%s AS %s', $db->quoteName('m.title'), $db->quoteName('metatitle')),
                $db->quoteName('c.alias'),
                $db->quoteName('c.catid'),
            ])
            ->from('#__content AS c')
            ->leftJoin(
                sprintf(
                    '%s AS %s ON %s = %s',
                    $db->quoteName('#__categories'),
                    $db->quoteName('category'),
                    $db->quoteName('category.id'),
                    $db->quoteName('c.catid')
                )
            )
            ->leftJoin(
                sprintf(
                    '%s AS %s ON %s = %s AND %s = %s',
                    $db->quoteName('#__osmeta_metadata'),
                    $db->quoteName('m'),
                    $db->quoteName('m.item_id'),
                    $db->quoteName('c.id'),
                    $db->quoteName('m.item_type'),
                    $this->code
                )
            );

        if ($search = $this->app->input->getString('com_content_filter_search', '')) {
            if (preg_match('/^\s*id:\s*(\d+)/', $search, $match)) {
                $query->where($db->quoteName('c.id') . ' = ' . (int)$match[1]);

            } else {
                $searchString = $db->quote('%' . $search . '%');

                $ors = [
                    sprintf('%s LIKE %s', $db->quoteName('c.title'), $searchString),
                    sprintf('%s LIKE %s', $db->quoteName('m.title'), $searchString),
                    sprintf('%s LIKE %s', $db->quoteName('c.metadesc'), $searchString),
                    sprintf('%s LIKE %s', $db->quoteName('c.alias'), $searchString),
                ];
                $query->where(sprintf('(%s)', join(' OR ', $ors)));
            }
        }

        $baseLevel = 1;
        $level     = $this->app->input->getInt('com_content_filter_level', 0);

        if ($categoryId = $this->app->input->getInt('com_content_filter_catid')) {
            $categoryQuery = $db->getQuery(true)
                ->select(
                    $db->quoteName(
                        [
                            'lft',
                            'rgt',
                            'level',
                        ]
                    )
                )
                ->from('#__categories')
                ->where('id = ' . $categoryId);

            if ($category = $db->setQuery($categoryQuery)->loadObject()) {
                $query->where([
                    $db->quoteName('category.lft') . ' >= ' . $category->lft,
                    $db->quoteName('category.rgt') . ' <= ' . $category->rgt,
                ]);
                $baseLevel = (int)$category->level;
            }
        }

        if ($level > 0) {
            $query->where(
                sprintf(
                    '%s <= %s',
                    $db->quoteName('category.level'),
                    $level + $baseLevel - 1
                )
            );
        }

        if ($authorId = $this->app->input->getInt('com_content_filter_authorid')) {
            $query->where($db->quoteName('c.created_by = ' . $authorId));
        }

        if ($state = $this->app->input->getString('com_content_filter_state', '')) {
            $state = stripos('DAUP', $state);
            if ($state !== false) {
                $state -= 2;
                $query->where($db->quoteName('c.state') . ' = ' . $state);
            }
        }

        if ($access = $this->app->input->getInt('com_content_filter_access')) {
            $query->where($db->quoteName('c.access') . ' = ' . $access);
        }

        $showEmptyDescriptions = $this->app->input->getBool('com_content_filter_show_empty_descriptions');
        if ($showEmptyDescriptions) {
            $query->where(sprintf('IFNULL(%1$s, %2$s = %2$s', $db->quoteName('c.metadesc'), $db->quote('')));
        }

        $ordering = str_replace('_', '', $this->app->input->getCmd('filter_order', 'title'));
        $orderDir = $this->app->input->getCmd('filter_order_Dir', 'ASC');
        $query->order($ordering . ' ' . $orderDir);

        $rows = $db->setQuery($query, $limitStart, $limit)->loadObjectList();

        $query->clear('select')
            ->clear('order')
            ->select('COUNT(*)')
            ->setLimit();
        $total = $db->setQuery($query)->loadResult();

        foreach ($rows as $row) {
            $editQuery     = [
                'option' => 'com_content',
                'task'   => 'article.edit',
                'id'     => $row->id,
            ];
            $row->edit_url = 'index.php?' . http_build_query($editQuery);

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
     * @inheritDoc
     */
    public function saveMetatags(
        array $ids,
        array $metatitles = [],
        array $metadescriptions = [],
        array $aliases = []
    ): void {
        $db  = $this->dbo;
        $ids = array_filter(array_map('intval', $ids));

        if ($ids) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__osmeta_metadata')
                ->where([
                    $db->quoteName('item_type') . ' = ' . $this->code,
                    sprintf('%s IN (%s)', $db->quoteName('item_id'), join(',', $ids)),
                ]);

            $osMetadata = $db->setQuery($query)->loadAssocList('item_id');
        }

        foreach ($ids as $i => $id) {
            $query   = $db->getQuery(true)
                ->select(
                    $db->quoteName(
                        [
                            'id',
                            'metadesc',
                            'metadata',
                            'alias',
                        ]
                    )
                )
                ->from('#__content')
                ->where($db->quoteName('id') . ' = ' . $id);
            $article = $db->setQuery($query)->loadObject();

            $article->metadata            = json_decode($article->metadata ?: '') ?: (object)[];
            $article->metadata->metatitle = $metatitles[$i] ?? '';

            $article->metadata = json_encode($article->metadata);
            $article->metadesc = $metadescriptions[$i] ?? '';

            if ($aliases) {
                if (empty($aliases[$i])) {
                    $this->app->enqueueMessage(
                        Text::_('COM_OSMETA_WARNING_EMPTY_ALIAS'),
                        'warning'
                    );

                } else {
                    $alias = $this->stringURLSafe($aliases[$i]);

                    if ($article->alias !== $alias) {
                        if ($this->isUniqueAlias($alias)) {
                            $article->alias = $alias;

                        } else {
                            $this->app->enqueueMessage(
                                Text::sprintf('COM_OSMETA_WARNING_DUPLICATED_ALIAS', $alias),
                                'warning'
                            );
                        }
                    }
                }
            }

            $updatedMetadata = (object)array_merge(
                $osMetadata[$article->id] ?? [],
                [
                    'item_id'     => $article->id,
                    'item_type'   => $this->code,
                    'title'       => $metatitles[$i] ?? '',
                    'description' => $metadescriptions[$i] ?? '',
                ]
            );

            $db->updateObject('#__content', $article, ['id']);
            if (empty($updatedMetadata->id)) {
                $db->insertObject('#__osmeta_metadata', $updatedMetadata, ['id']);
            } else {
                $db->updateObject('#__osmeta_metadata', $updatedMetadata, ['id']);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function copyItemTitleToSearchEngineTitle(array $ids): void
    {
        $ids = array_filter(array_map('intval', $ids));
        if ($ids == false) {
            return;
        }

        $db        = $this->dbo;
        $params    = ComponentHelper::getParams('com_osmeta');
        $maxLength = $params->get('meta_title_limit', 70);

        $query      = $db->getQuery(true)
            ->select([
                'id',
                'item_id',
            ])
            ->from('#__osmeta_metadata')
            ->where([
                $db->quoteName('item_type') . ' = ' . $this->code,
                sprintf('%s IN (%s)', $db->quoteName('item_id'), join(',', $ids)),
            ]);
        $osMetadata = $db->setQuery($query)->loadObjectList('item_id');

        $query = $db->getQuery(true)
            ->select(
                $db->quoteName(
                    [
                        'id',
                        'title',
                        'metadata',
                    ]
                )
            )
            ->from('#__content')
            ->where([
                sprintf('IFNULL(%1$s, %2$s) != %2$s', $db->quoteName('title'), $db->quote('')),
                sprintf('%s IN (%s)', $db->quoteName('id'), join(',', $ids)),
            ]);

        $articles = $db->setQuery($query)->loadObjectList();
        foreach ($articles as $article) {
            $article->metadata = json_decode((string)$article->metadata) ?: (object)[];

            $article->metadata->metatitle = HTMLHelper::_('alledia.truncate', $article->title, $maxLength, '');

            $metadata = (object)[
                'item_id'   => $article->id,
                'item_type' => $this->code,
                'title'     => $article->metadata->metatitle,
            ];

            $article->metadata = json_encode($article->metadata);
            $db->updateObject('#__content', $article, ['id']);

            $id = $osMetadata[$article->id]->id ?? null;
            if ($id) {
                $metadata->id = $id;
                $db->updateObject('#__osmeta_metadata', $metadata, ['id']);

            } else {
                $db->insertObject('#__osmeta_metadata', $metadata);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function generateDescriptions(array $ids): void
    {
        $ids = array_filter(array_map('intval', $ids));
        if ($ids == false) {
            return;
        }

        $db        = $this->dbo;
        $params    = ComponentHelper::getParams('com_osmeta');
        $maxLength = $params->get('meta_description_limit', 160);

        $query    = $db->getQuery(true)
            ->select(
                $db->quoteName(
                    [
                        'id',
                        'introtext',
                    ]
                )
            )
            ->from('#__content')
            ->where([
                sprintf('id IN (%s)', join(',', $ids)),
                sprintf('IFNULL(%1$s, %2$s) != %2$s', $db->quoteName('introtext'), $db->quote('')),
            ]);
        $articles = $db->setQuery($query)->loadObjectList();

        $query      = $db->getQuery(true)
            ->select('*')
            ->from('#__osmeta_metadata')
            ->where([
                $db->quoteName('item_type') . ' = ' . $this->code,
                sprintf('%s IN (%s)', $db->quoteName('item_id'), join(',', $ids)),
            ]);
        $osMetadata = $db->setQuery($query)->loadObjectList('item_id');

        foreach ($articles as $article) {
            $introtext = HTMLHelper::_('alledia.truncate', strip_tags($article->introtext), $maxLength, '');

            $article->metadesc = $introtext;
            $db->updateObject('#__content', $article, ['id']);

            $metadata = (object)[
                'item_id'     => $article->id,
                'item_type'   => $this->code,
                'description' => $article->metadesc,
            ];

            if ($id = $osMetadata[$article->id]->id) {
                $metadata->id = $id;
                $db->updateObject('#__osmeta_metadata', $metadata, 'id');

            } else {
                $db->insertObject('#__osmeta_metadata', $metadata);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getFilter(): string
    {
        $app = $this->app;

        $search = $this->app->input->getString('com_content_filter_search', '');
        $catId  = $this->app->input->getString('com_content_filter_catid', '0');
        $level  = $this->app->input->getString('com_content_filter_level', '0');
        $access = $this->app->input->getString('com_content_filter_access', '');

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

        $state                 = $this->app->input->getString('com_content_filter_state', '');
        $showEmptyDescriptions = $this->app->input->getString(
            'com_content_filter_show_empty_descriptions',
            '-1'
        );

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

        $descriptionChecked = $showEmptyDescriptions != '-1' ? 'checked="yes" ' : '';

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
                   value="1"
                   onchange="document.adminForm.submit();"
                   name="com_content_filter_show_empty_descriptions"
                   ' . $descriptionChecked . '/>';

        $result .= '</div>';

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataByRequest(string $query): array
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
     * @inheritDoc
     */
    protected function isUniqueAlias(string $alias): bool
    {
        $db = $this->dbo;

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__content')
            ->where('alias = ' . $db->quote($alias));

        return (bool)(int)$db->setQuery($query)->loadResult();
    }
}
