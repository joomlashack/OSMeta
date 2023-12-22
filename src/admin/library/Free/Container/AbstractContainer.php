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

namespace Alledia\OSMeta\Free\Container;

use Alledia\Framework\Factory as AllediaFactory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects

/**
 * Abstract Metatags Container Class
 *
 * @since  1.0
 */
abstract class AbstractContainer
{
    /**
     * Allow automatically generating title from the content
     *
     * @var bool
     */
    public $supportGenerateTitle = true;

    /**
     * Allow automatically generating description from the content
     *
     * @var bool
     */
    public $supportGenerateDescription = true;

    /**
     * @var int
     */
    protected $code = null;

    /**
     * @var string
     */
    protected $context = null;

    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var DatabaseDriver
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
     * Method to get the Metadata
     *
     * @param int $id
     *
     * @return array
     */
    public function getMetadata(int $id): array
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
                'metatitle'       => '',
            ];
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->code;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function stringURLSafe(string $string): string
    {
        return ApplicationHelper::stringURLSafe($string);
    }

    /**
     * Return an array with blank metadata
     */
    public function getDefaultMetadata(): array
    {
        return [
            'metatitle'       => '',
            'metadescription' => '',
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
     * Method to check if an alias already exists.
     *
     * @param string $alias
     *
     * @return bool
     */
    abstract protected function isUniqueAlias(string $alias): bool;

    /**
     * @param int[] $ids
     *
     * @return void
     */
    abstract public function copyItemTitleToSearchEngineTitle(array $ids): void;

    /**
     * @param int[] $ids
     *
     * @return void
     */
    abstract public function generateDescriptions(array $ids): void;

    /**
     * @param int[]     $ids
     * @param ?string[] $metatitles
     * @param ?string[] $metadescriptions
     * @param ?string[] $aliases
     *
     * @return void
     */
    abstract public function saveMetatags(
        array $ids,
        array $metatitles = [],
        array $metadescriptions = [],
        array $aliases = []
    ): void;

    /**
     * @param int $limitStart
     * @param int $limit
     *
     * @return array
     */
    abstract public function getMetatags(int $limitStart, int $limit): array;

    /**
     * Method to get the Metadata By Request
     *
     * @param string $query
     *
     * @return array
     */
    abstract public function getMetadataByRequest(string $query): array;

    /**
     * Method to get filter values
     *
     * @return Registry
     */
    public function getFilters(): Registry
    {
        $search = $this->app->getUserStateFromRequest(
            $this->context . '.filter.search',
            'com_content_filter_search',
            '',
            'string'
        );

        $categoryId = $this->app->getUserStateFromRequest(
            $this->context . '.filter.catid',
            'com_content_filter_catid',
            null,
            'int'
        );

        $level = $this->app->getUserStateFromRequest(
            $this->context . '.filter.level',
            'com_content_filter_level',
            0,
            'int'
        );

        $access = $this->app->getUserStateFromRequest(
            $this->context . '.filter.access',
            'com_content_filter_access',
            null,
            'string'
        );

        $state = $this->app->getUserStateFromRequest(
            $this->context . '.filter.state',
            'com_content_filter_state',
            null,
            'string'
        );

        $showEmptyDescriptions = $this->app->getUserStateFromRequest(
            $this->context . '.show.empty.descriptions',
            'com_content_filter_show_empty_descriptions',
            false,
            'bool'
        );

        $ordering  = $this->app->getUserStateFromRequest(
            $this->context . '.list.order',
            'filter_order',
            'title',
            'cmd'
        );
        $direction = $this->app->getUserStateFromRequest(
            $this->context . '.list.direction',
            'filter_order_Dir',
            'ASC',
            'cmd'
        );

        return new Registry([
            'search'   => $search,
            'category' => [
                'id'    => $categoryId,
                'level' => $level,
            ],
            'access'   => $access,
            'state'    => $state,
            'show'     => [
                'empty' => $showEmptyDescriptions,
            ],
            'list'     => [
                'ordering'  => $ordering,
                'direction' => $direction,
            ],
        ]);
    }
}
