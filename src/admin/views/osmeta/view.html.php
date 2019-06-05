<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSMeta.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

class OSMetaViewOSMeta extends JViewLegacy
{
    /**
     * Class constructor method
     *
     * @param mix $config Configuration set
     *
     * @access  public
     * @since   1.0
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->_addPath('template', $this->_basePath . '/views/osmeta/tmpl');
    }

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
        JToolBarHelper::title(JText::_('COM_OSMETA_META_TAGS_MANAGER'), 'logo');

        JToolBarHelper::apply("save");

        if (version_compare(JVERSION, '3.0', '>=')) {
            $iconShuffle = 'shuffle';
            $iconEdit = 'pencil-2';
        } else {
            $iconShuffle = 'refresh';
            $iconEdit = 'edit';
        }

        if ($this->metatagsContainer->supportGenerateTitle) {
            JToolBarHelper::custom(
                'copyItemTitleToSearchEngineTitle',
                $iconShuffle,
                '',
                JText::_('COM_OSMETA_COPY_ITEM_TITLE_TO_TITLE'),
                true
            );
        }

        if ($this->metatagsContainer->supportGenerateDescription) {
            JToolBarHelper::custom(
                'generateDescriptions',
                $iconEdit,
                '',
                JText::_('COM_OSMETA_GENERATE_DESCRIPTIONS'),
                true
            );
        }

        JToolBarHelper::cancel("cancel");

        $doc = JFactory::getDocument();
        $app = JFactory::getApplication();

        $itemType = $app->input->getString('type', null);
        $this->itemType = $itemType;

        JHtml::_('stylesheet', 'media/com_osmeta/admin/css/main-j3.css');
        JHtml::_('stylesheet', 'media/com_osmeta/admin/css/alledia.css');

        $this->submenu = JHtmlSidebar::render();

        $this->extension = Alledia\Framework\Factory::getExtension('OSMeta', 'component');

        parent::display($tpl);
    }
}
