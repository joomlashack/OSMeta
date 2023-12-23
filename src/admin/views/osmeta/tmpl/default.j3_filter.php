<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2023 Joomlashack.com. All rights reserved
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * @var \OSMetaViewOSMeta $this
 * @var string            $template
 * @var string            $layout
 * @var string            $layoutTemplate
 * @var Language          $lang
 * @var string            $filetofind
 */

HTMLHelper::_('formbehavior.chosen', 'select');

?>
<div class="js-stools-container-bar">
    <div class="row-fluid">
        <div class="input-append">
            <input type="text"
                   name="com_content_filter_search"
                   id="search"
                   class="hasTooltip"
                   value="<?php echo $this->filters->get('search'); ?>"
                   placeholder="<?php echo Text::_('COM_OSMETA_SEARCH'); ?>"
                   title="<?php echo Text::_('COM_OSMETA_FILTER_DESC'); ?>"
                   onchange="this.form.submit();">

            <button type="submit"
                    class="btn"
                    id="Go"
                    onclick="this.form.submit();">
                <span class="icon-search"></span>
            </button>

            <button id="clearForm" class="btn">
                <?php echo Text::_('COM_OSMETA_RESET_LABEL'); ?>
            </button>
        </div>
    </div>
</div>
