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
    abstract public function getFilters(): Registry;
}
