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

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\View\Admin\AbstractBase;
use Alledia\OSMeta\Free\Container\Component\Content;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class OSMetaViewOSMeta extends AbstractBase
{
    /**
     * @var string
     */
    public $itemType = null;

    /**
     * @var object[]
     */
    public $metatagsData = null;

    /**
     * @var Registry
     */
    public $filters = null;

    /**
     * @var array[]
     */
    public $availableTypes = null;

    /**
     * @var Pagination
     */
    public $pageNav = null;

    /**
     * @var string
     */
    public $order = null;

    /**
     * @var string
     */
    public $order_Dir = null;

    /**
     * @var string
     */
    public $itemTypeShort = null;

    /**
     * @var Content
     */
    public $metatagsContainer = null;

    /**
     * @inheritDoc
     */
    public function display($tpl = null)
    {
        ToolbarHelper::title(Text::_('COM_OSMETA_META_TAGS_MANAGER'), 'logo');

        ToolbarHelper::apply('save');

        if ($this->metatagsContainer->supportGenerateTitle) {
            ToolbarHelper::custom(
                'copyItemTitleToSearchEngineTitle',
                'shuffle',
                '',
                Text::_('COM_OSMETA_COPY_ITEM_TITLE_TO_TITLE'),
                true
            );
        }

        if ($this->metatagsContainer->supportGenerateDescription) {
            ToolbarHelper::custom(
                'generateDescriptions',
                'pencil-2',
                '',
                Text::_('COM_OSMETA_GENERATE_DESCRIPTIONS'),
                true
            );
        }

        ToolbarHelper::cancel();
        ToolbarHelper::preferences('com_osmeta');

        $app = Factory::getApplication();

        $this->itemType  = $app->input->getString('type');
        $this->submenu   = Sidebar::render();
        $this->extension = Factory::getExtension('OSMeta', 'component');

        HTMLHelper::_('stylesheet', 'com_osmeta/admin.css', ['relative' => true]);

        Text::script('COM_OSMETA_CHARS');
        Text::script('COM_OSMETA_CHARS_1');
        Text::script('COM_OSMETA_CONFIRM_CANCEL');

        HTMLHelper::_('script', 'com_osmeta/admin.min.js', ['relative' => true]);

        $titleLimit       = $this->extension->params->get('meta_title_limit', 70);
        $titleLong        = Text::_('COM_OSMETA_TITLE_TOO_LONG');
        $descriptionLimit = $this->extension->params->get('meta_description_limit', 160);
        $descriptionLong  = Text::_('COM_OSMETA_DESCR_TOO_LONG');

        $this->app->getDocument()->addScriptDeclaration(<<<JSCRIPT
jQuery(function($) {
    $('#articleList textarea.char-count.metatitle')
        .osmetaCharCount({limit  : {$titleLimit}, message: '{$titleLong}'});
    $('#articleList textarea.char-count.metadesc')
        .osmetaCharCount({limit  : {$descriptionLimit}, message: '{$descriptionLong}'});
    });
JSCRIPT
        );

        parent::display($tpl);
    }

    /**
     * @return string
     */
    protected function getFilters(): string
    {
        $filters = [];

        $stateOptions = [
            HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_STATE')),
            HTMLHelper::_('select.option', 1, Text::_('COM_OSMETA_PUBLISHED')),
            HTMLHelper::_('select.option', 0, Text::_('COM_OSMETA_UNPUBLISHED')),
            HTMLHelper::_('select.option', -1, Text::_('COM_OSMETA_ARCHIVED')),
            HTMLHelper::_('select.option', -2, Text::_('COM_OSMETA_TRASHED')),
        ];
        $filters[]    = HTMLHelper::_(
            'select.genericlist',
            $stateOptions,
            'com_content_filter_state',
            [
                'onchange' => 'this.form.submit();',
                'class'    => 'form-select',
            ],
            'value',
            'text',
            $this->filters->get('state')
        );

        $categories = HTMLHelper::_('category.options', 'com_content');
        array_unshift($categories, HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_CATEGORY')));

        $filters[] = HTMLHelper::_(
            'select.genericlist',
            $categories,
            'com_content_filter_catid',
            [
                'onchange' => 'this.form.submit();',
                'class'    => 'form-select',
            ],
            'value',
            'text',
            $this->filters->get('category.id')
        );

        $levelOptions = array_map(
            function ($value) {
                return HTMLHelper::_('select.option', $value, Text::_('J' . $value));
            },
            range(1, 10)
        );
        array_unshift($levelOptions, HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_MAX_LEVELS')));

        $filters[] = HTMLHelper::_(
            'select.genericlist',
            $levelOptions,
            'com_content_filter_level',
            [
                'onchange' => 'this.form.submit();',
                'class'    => 'form-select',
            ],
            'value',
            'text',
            $this->filters->get('category.level')
        );

        $filters[] = HTMLHelper::_(
            'access.level',
            'com_content_filter_access',
            $this->filters->get('access'),
            [
                'onchange' => 'this.form.submit();',
                'class'    => 'form-select',
            ]
        );

        $this->app->getDocument()->addScriptDeclaration(<<<JSCRIPT
jQuery(function($) {
    let clear = document.getElementById('clearForm');

    clear.addEventListener('click', function(event) {
        event.preventDefault();

        this.form.search.value = '';

        this.form.com_content_filter_catid.value  = '';
        this.form.com_content_filter_state.value  = '';
        this.form.com_content_filter_level.value  = '';
        this.form.com_content_filter_access.value = '';

        let emptyFilter = document.getElementById('com_content_filter_show_empty_descriptions');
        if (emptyFilter) {
            emptyFilter.checked = false;
        }

        this.form.submit();
    })
});
JSCRIPT
        );

        if (\Joomla\CMS\Version::MAJOR_VERSION < 4) {
            return join("\n", $filters);
        }

        $filterDiv = '<div class="js-stools-field-filter">';

        return $filterDiv . join("</div>\n" . $filterDiv, $filters);
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function loadFilterTemplate(): string
    {
        $template = explode(':', $this->itemType);
        $template = strtolower(array_pop($template));

        $filter = '';
        try {
            $filter = $this->loadTemplate('filter');

            return $filter . $this->loadTemplate('filter_' . $template);

        } catch (Throwable $error) {
            if ($this->app->get('debug')) {
                $this->app->enqueueMessage($error->getMessage(), 'warning');
            }
        }

        return $filter;
    }
}
