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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
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
    protected const FILTER_STATE    = 1 << 0;
    protected const FILTER_CATEGORY = 1 << 1;
    protected const FILTER_LEVEL    = 1 << 2;
    protected const FILTER_ACCESS   = 1 << 3;

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

        $fields = $db->quoteName([
            'item_id',
            'description',
            'title',
        ]);
        $query  = $db->getQuery(true)
            ->select(
                array_merge(
                    $fields,
                    [
                        $db->quoteName('item_id') . ' AS ' . $db->quoteName('id'),
                        $db->quoteName('description') . ' AS ' . $db->quoteName('metadescription'),
                        $db->quoteName('title') . ' AS ' . $db->quoteName('metatitle'),
                    ]
                )
            )
            ->from('#__osmeta_metadata')
            ->where([
                $db->quoteName('item_id') . ' = ' . $id,
                $db->quoteName('item_type') . ' = ' . $db->quote($this->getTypeId()),
            ]);

        return array_merge(
            [
                'id'              => 0,
                'item_id'         => 0,
                'metadescription' => '',
                'description'     => '',
                'title'           => '',
                'metatitle'       => '',
            ],
            $db->setQuery($query)->loadAssoc() ?: []
        );
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

    /**
     * @param int $fieldMask
     *
     * @return string
     */
    public function getFormFilter(int $fieldMask = -1): string
    {
        $filters = $this->getFilterFields($fieldMask);

        $fields = json_encode(array_keys($filters));

        $this->app->getDocument()->addScriptDeclaration(<<<JSCRIPT
jQuery(function($) {
    let clear   = document.getElementById('clearForm'),
        empty   = document.getElementById('com_content_filter_show_empty_descriptions'),
        filters = {$fields}; 

    clear.addEventListener('click', function(event) {
        event.preventDefault();
        
        let form = this.form;

        this.form.search.value = '';

        filters.forEach(function (filter) {
            if (form[filter]) {
                form[filter].value = '';
            }
        });
        
        if (empty) {
            empty.checked = false;
        }

        this.form.submit();
    })
});
JSCRIPT
        );

        if (Version::MAJOR_VERSION < 4) {
            return join("\n", $filters);
        }

        $filterDiv = '<div class="js-stools-field-filter">';

        return $filterDiv . join("</div>\n" . $filterDiv, $filters) . '</div>';
    }

    /**
     * @param int $fieldMask
     *
     * @return string[]
     */
    protected function getFilterFields(int $fieldMask = 0): array
    {
        $filterState = $this->getFilters();

        $filters = [];

        if ($fieldMask & static::FILTER_STATE) {
            $id = 'com_content_filter_state';

            $filters[$id] = HTMLHelper::_(
                'select.genericlist',
                [
                    HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_STATE')),
                    HTMLHelper::_('select.option', 1, Text::_('COM_OSMETA_PUBLISHED')),
                    HTMLHelper::_('select.option', 0, Text::_('COM_OSMETA_UNPUBLISHED')),
                    HTMLHelper::_('select.option', -1, Text::_('COM_OSMETA_ARCHIVED')),
                    HTMLHelper::_('select.option', -2, Text::_('COM_OSMETA_TRASHED')),
                ],
                $id,
                [
                    'onchange' => 'this.form.submit();',
                    'class'    => 'form-select',
                ],
                'value',
                'text',
                $filterState->get('state')
            );
        }

        if ($fieldMask & static::FILTER_CATEGORY) {
            $categories = HTMLHelper::_('category.options', 'com_content');
            array_unshift($categories, HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_CATEGORY')));
            $id = 'com_content_filter_catid';

            $filters[$id] = HTMLHelper::_(
                'select.genericlist',
                $categories,
                $id,
                [
                    'onchange' => 'this.form.submit();',
                    'class'    => 'form-select',
                ],
                'value',
                'text',
                $filterState->get('category.id')
            );
        }

        if ($fieldMask & static::FILTER_LEVEL) {
            $levelOptions = array_map(
                function ($value) {
                    return HTMLHelper::_('select.option', $value, Text::_('J' . $value));
                },
                range(1, 10)
            );
            array_unshift($levelOptions, HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_MAX_LEVELS')));
            $id = 'com_content_filter_level';

            $filters[$id] = HTMLHelper::_(
                'select.genericlist',
                $levelOptions,
                $id,
                [
                    'onchange' => 'this.form.submit();',
                    'class'    => 'form-select',
                ],
                'value',
                'text',
                $filterState->get('category.level')
            );
        }

        if ($fieldMask & static::FILTER_ACCESS) {
            $id = 'com_content_filter_access';

            $filters[$id] = HTMLHelper::_(
                'access.level',
                $id,
                $filterState->get('access'),
                [
                    'onchange' => 'this.form.submit();',
                    'class'    => 'form-select',
                ]
            );
        }

        return $filters;
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
}
