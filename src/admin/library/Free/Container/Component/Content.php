<?php

/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2026 Joomlashack.com. All rights reserved
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
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

class Content extends AbstractContainer
{
    /**
     * @inheritdoc
     */
    protected $code = 1;

    /**
     * @inheritdoc
     */
    protected $context = 'content.article';

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
                $db->quoteName('m.title') . ' AS ' . $db->quoteName('metatitle'),
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

        $filters = $this->getFilters();

        if ($search = $filters->get('search')) {
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

        if ($categoryId = $filters->get('category.id')) {
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

        $level = $filters->get('category.level');
        if ($level > 0) {
            $query->where(
                sprintf(
                    '%s <= %s',
                    $db->quoteName('category.level'),
                    $level + $baseLevel - 1
                )
            );
        }

        if ($authorId = $filters->get('author.id')) {
            $query->where($db->quoteName('c.created_by') . ' = ' . $authorId);
        }

        $state = $filters->get('state');
        if ($state != '') {
            $query->where($db->quoteName('c.state') . ' = ' . (int)$state);
        }

        if ($access = $filters->get('access')) {
            $query->where($db->quoteName('c.access') . ' = ' . $access);
        }

        if ($filters->get('show.empty')) {
            $query->where(sprintf('IFNULL(%1$s, %2$s) = %2$s', $db->quoteName('c.metadesc'), $db->quote('')));
        }

        $ordering  = str_replace('_', '', $filters->get('list.ordering'));
        $direction = $filters->get('list.direction');
        $query->order($ordering . ' ' . $direction);

        $articles = $db->setQuery($query, $limitStart, $limit)->loadObjectList();

        $query->clear('select')
            ->clear('order')
            ->select('COUNT(*)')
            ->setLimit();
        $total = $db->setQuery($query)->loadResult();

        foreach ($articles as $article) {
            $editQuery         = [
                'option' => 'com_content',
                'task'   => 'article.edit',
                'id'     => $article->id,
                'return' => base64_encode(Uri::getInstance()),
            ];
            $article->edit_url = 'index.php?' . http_build_query($editQuery);

            $article->view_url = Route::link(
                'site',
                RouteHelper::getArticleRoute($article->id . ':' . urlencode($article->alias), $article->catid),
                true,
                Route::TLS_IGNORE,
                true
            );
        }

        return [
            'rows'  => $articles,
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
                            'attribs',
                            'metadesc',
                            'metadata',
                            'alias',
                        ]
                    )
                )
                ->from('#__content')
                ->where($db->quoteName('id') . ' = ' . $id);
            $article = $db->setQuery($query)->loadObject();

            $article->metadata = json_decode($article->metadata ?: '{}') ?: (object)[];
            $article->attribs  = json_decode($article->attribs ?: '{}') ?: (object)[];

            $article->metadata->metatitle         = $metatitles[$i] ?? '';
            $article->attribs->article_page_title = $metatitles[$i] ?? '';
            $article->metadesc                    = $metadescriptions[$i] ?? '';

            $article->metadata = json_encode($article->metadata);
            $article->attribs  = json_encode($article->attribs);

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
        $osMetadata = $db->setQuery($query)->loadAssocList('item_id');

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

            $metaTitle                    = HTMLHelper::_('alledia.truncate', $article->title, $maxLength, '');
            $article->metadata->metatitle = $metaTitle;

            $article->metadata = json_encode($article->metadata);
            $db->updateObject('#__content', $article, ['id']);

            $metadata = (object)array_merge(
                $osMetadata[$article->id] ?? [],
                [
                    'item_id'   => $article->id,
                    'item_type' => $this->code,
                    'title'     => $metaTitle,
                ]
            );

            if (empty($metadata->id)) {
                $metadata->description = '';
                $db->insertObject('#__osmeta_metadata', $metadata);

            } else {
                $db->updateObject('#__osmeta_metadata', $metadata, ['id']);
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
        $osMetadata = $db->setQuery($query)->loadAssocList('item_id');

        foreach ($articles as $article) {
            $introtext = HTMLHelper::_('alledia.truncate', strip_tags($article->introtext), $maxLength, '');

            $article->metadesc = $introtext;
            $db->updateObject('#__content', $article, ['id']);

            $metadata = (object)array_merge(
                $osMetadata[$article->id] ?? [],
                [
                    'item_id'     => $article->id,
                    'item_type'   => $this->code,
                    'description' => $article->metadesc,
                ]
            );

            if (empty($metadata->id)) {
                $metadata->title = '';
                $db->insertObject('#__osmeta_metadata', $metadata);

            } else {
                $db->updateObject('#__osmeta_metadata', $metadata, 'id');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getMetadataByRequest(string $query): array
    {
        $params = [];
        parse_str($query, $params);

        $metadata = $this->getDefaultMetadata();

        $itemId = $params['id'] ?? null;
        if ((int)$itemId) {
            $metadata = $this->getMetadata((int)$itemId);
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

        return $db->setQuery($query)->loadResult() == 0;
    }
}
