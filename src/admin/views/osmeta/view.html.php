<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2024 Joomlashack.com. All rights reserved
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
    protected function getFilterForm(): string
    {
        return $this->metatagsContainer->getFormFilter();
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function loadFilterTemplate(): string
    {
        return $this->loadTemplate('filter') . $this->loadTemplate('filter_fields');
    }
}
