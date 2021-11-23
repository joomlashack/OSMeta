<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
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

use Alledia\OSMeta\Free\Container\Component\Content;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Alledia\Framework\Joomla\View\Admin\AbstractBase;

defined('_JEXEC') or die();

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
     * @var string
     */
    public $filter = null;

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
     * Method to display the view
     *
     * @param string $tpl Template file
     *
     * @access  public
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ToolbarHelper::title(Text::_('COM_OSMETA_META_TAGS_MANAGER'), 'logo');

        ToolBarHelper::apply("save");

        if ($this->metatagsContainer->supportGenerateTitle) {
            ToolBarHelper::custom(
                'copyItemTitleToSearchEngineTitle',
                'shuffle',
                '',
                Text::_('COM_OSMETA_COPY_ITEM_TITLE_TO_TITLE'),
                true
            );
        }

        if ($this->metatagsContainer->supportGenerateDescription) {
            ToolBarHelper::custom(
                'generateDescriptions',
                'pencil-2',
                '',
                Text::_('COM_OSMETA_GENERATE_DESCRIPTIONS'),
                true
            );
        }

        ToolBarHelper::cancel("cancel");
        ToolbarHelper::preferences('com_osmeta');

        $app = Factory::getApplication();

        $itemType       = $app->input->getString('type', null);
        $this->itemType = $itemType;

        HTMLHelper::_('stylesheet', 'media/com_osmeta/admin/css/main-j3.css');
        HTMLHelper::_('stylesheet', 'media/com_osmeta/admin/css/alledia.css');

        $this->submenu = JHtmlSidebar::render();

        $this->extension = Alledia\Framework\Factory::getExtension('OSMeta', 'component');

        parent::display($tpl);
    }
}
