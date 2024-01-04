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

class Categories extends AbstractContainer
{
    /**
     * @inheritdoc
     */
    protected $code = 4;

    /**
     * @inheritdoc
     */
    protected $context = 'content.category';

    public function getFormFilter(int $fieldMask = -1): string
    {
        return parent::getFormFilter(static::FILTER_STATE | static::FILTER_ACCESS);
    }

    /**
     * @inheritDoc
     */
    public function getMetatags(int $limitStart, int $limit): array
    {
        $db = $this->dbo;

        $query = $db->getQuery(true)
            ->select([
                'c.id',
                'c.title',
                'c.metadesc',
                'm.title AS ' . $db->quoteName('metatitle'),
                'c.extension',
                'c.alias',
            ])
            ->from('#__categories AS c')
            ->leftJoin(
                sprintf(
                    '%s AS m ON %s = %s AND %s = %s',
                    $db->quoteName('#__osmeta_metadata'),
                    $db->quoteName('m.item_id'),
                    $db->quoteName('c.id'),
                    $db->quoteName('m.item_type'),
                    $db->quote($this->code)
                )
            )
            ->where($db->quoteName('c.extension') . ' = ' . $db->quote('com_content'));

        $filters = $this->getFilters();
        if ($search = $filters->get('search')) {
            if (preg_match('/^\s*id:\s*(\d+)/', $search, $match)) {
                $query->where($db->quoteName('c.id') . ' = ' . (int)$match[1]);

            } else {
                $searchString = $db->quote('%' . $search . '%');

                $ors = [
                    $db->quoteName('c.title') . ' LIKE ' . $searchString,
                    $db->quoteName('m.title') . ' LIKE ' . $searchString,
                    $db->quoteName('c.metadesc') . 'LIKE ' . $searchString,
                    $db->quoteName('c.alias') . ' LIKE ' . $searchString,
                ];
                $query->where(sprintf('(%s)', join(' OR ', $ors)));
            }
        }

        $state = $filters->get('state');
        if ($state != '') {
            $query->where($db->quoteName('c.published') . ' = ' . (int)$state);
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

        $categories = $db->setQuery($query, $limitStart, $limit)->loadObjectList();

        $query->clear('select')
            ->clear('order')
            ->select('COUNT(*)')
            ->setLimit();
        $total = $db->setQuery($query)->loadResult();

        foreach ($categories as $category) {
            $editQuery          = [
                'option'    => 'com_categories',
                'task'      => 'category.edit',
                'id'        => $category->id,
                'extension' => $category->extension,
            ];
            $category->edit_url = 'index.php?' . http_build_query($editQuery);

            $url = RouteHelper::getCategoryRoute($category->id);

            $category->view_url = Route::link('site', $url, true, Route::TLS_IGNORE, true);
        }

        return [
            'rows'  => $categories,
            'total' => $total,
        ];
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
            $query = $db->getQuery(true)
                ->select($db->quoteName(['id', 'metadesc', 'metadata', 'alias']))
                ->from('#__categories')
                ->where('id = ' . (int)$id);

            $category = $db->setQuery($query)->loadObject();

            $metadata            = json_decode((string)$category->metadata) ?: (object)[];
            $metadata->metatitle = $metatitles[$i] ?? '';

            $category->metadata = json_encode($metadata);
            $category->metadesc = $metadescriptions[$i] ?: '';
            if ($aliases) {
                $alias = $aliases[$i] ?? null;
                if ($alias) {
                    $alias = $this->stringURLSafe($alias);
                    if ($category->alias != $alias) {
                        if ($this->isUniqueAlias($alias)) {
                            $category->alias = $alias;

                        } else {
                            $this->app->enqueueMessage(
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

            $updatedMetadata = (object)array_merge(
                $osMetadata[$category->id] ?? [],
                [
                    'item_id'     => $category->id,
                    'item_type'   => $this->code,
                    'title'       => $metatitles[$i] ?? '',
                    'description' => $metadescriptions[$i] ?? '',
                ]
            );

            $db->updateObject('#__categories', $category, ['id']);
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

        $query = $db->getQuery(true)
            ->select([
                'id',
                'title',
                'metadata',
            ])
            ->from('#__categories')
            ->where([
                sprintf('IFNULL(%1$s, %2$s) != %2$s', $db->quoteName('title'), $db->quote('')),
                sprintf('id IN (%s)', join(',', $ids)),
            ]);

        $categories = $db->setQuery($query)->loadObjectList();

        $query = $db->getQuery(true)
            ->select([
                'id',
                'item_id',
            ])
            ->from('#__osmeta_metadata')
            ->where([
                sprintf('item_id IN (%s)', join(',', $ids)),
                'item_type = ' . $this->code,
            ]);

        $osMetadata = $db->setQuery($query)->loadObjectList('item_id');

        foreach ($categories as $category) {
            $category->metadata = json_decode((string)$category->metadata) ?: (object)[];

            $category->metadata->metatitle = HTMLHelper::_('alledia.truncate', $category->title, $maxLength, '');

            $metadata = (object)[
                'item_id'   => $category->id,
                'item_type' => $this->code,
                'title'     => $category->metadata->metatitle,
            ];

            $category->metadata = json_encode($category->metadata);
            $db->updateObject('#__categories', $category, ['id']);

            $id = $osMetadata[$category->id]->id ?? null;
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

        $query = $db->getQuery(true)
            ->select([
                'id',
                'description',
            ])
            ->from('#__categories')
            ->where(sprintf('id IN (%s)', join(',', $ids)));

        $categories = $db->setQuery($query)->loadObjectList();

        $query = $db->getQuery(true)
            ->select([
                'id',
                'item_id',
            ])
            ->from('#__osmeta_metadata')
            ->where([
                'item_type = ' . $this->code,
                sprintf('item_id in (%s)', join(',', $ids)),
            ]);

        $osMetadata = $db->setQuery($query)->loadObjectList('item_id');

        foreach ($categories as $category) {
            if ($category->description) {
                $category->metadesc = HTMLHelper::_(
                    'alledia.truncate',
                    strip_tags($category->description),
                    $maxLength,
                    ''
                );

                $db->updateObject('#__categories', $category, 'id');

                $metadata = (object)[
                    'item_id'     => $category->id,
                    'item_type'   => $this->code,
                    'description' => $category->metadesc,
                ];
                if ($id = $osMetadata[$category->id]->id) {
                    $metadata->id = $id;
                    $db->updateObject('#__osmeta_metadata', $metadata, 'id');

                } else {
                    $db->insertObject('#__osmeta_metadata', $metadata);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function isUniqueAlias(string $alias): bool
    {
        $db = $this->dbo;

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__categories')
            ->where('extension = ' . $db->quote('com_content'))
            ->where('alias = ' . $db->quote($alias));

        return $db->setQuery($query)->loadResult() == 0;
    }
}
