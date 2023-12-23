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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * @var \OSMetaViewOSMeta             $this
 * @var string                        $template
 * @var string                        $layout
 * @var string                        $layoutTemplate
 * @var \Joomla\CMS\Language\Language $lang
 * @var string                        $filetofind
 */
?>
<div class="row-fluid">
    <?php
    $stateOptions = [
        HTMLHelper::_('select.option', '', Text::_('COM_OSMETA_SELECT_STATE')),
        HTMLHelper::_('select.option', 1, Text::_('COM_OSMETA_PUBLISHED')),
        HTMLHelper::_('select.option', 0, Text::_('COM_OSMETA_UNPUBLISHED')),
        HTMLHelper::_('select.option', -1, Text::_('COM_OSMETA_ARCHIVED')),
        HTMLHelper::_('select.option', -2, Text::_('COM_OSMETA_TRASHED')),
    ];

    echo HTMLHelper::_(
        'select.genericlist',
        $stateOptions,
        'com_content_filter_state',
        [
            'onchange' => 'this.form.submit();',
        ],
        'value',
        'text',
        $this->filters->get('state')
    );

    echo HTMLHelper::_(
        'access.level',
        'com_content_filter_access',
        $this->filters->get('access'),
        [
            'onchange' => 'this.form.submit();',
        ]
    );
    ?>
</div>

<input type="hidden" name="com_content_filter_show_empty_descriptions"/>
<input type="checkbox"
    <?php echo $this->filters->get('show.empty') ? 'checked="checked"' : ''; ?>
       name="com_content_filter_show_empty_descriptions"
       id="com_content_filter_show_empty_descriptions"
       value="1"
       onchange="this.form.submit();"/>
<label for="com_content_filter_show_empty_descriptions">
    <?php echo Text::_('COM_OSMETA_SHOW_ONLY_EMPTY_DESCRIPTIONS'); ?>
</label>
<script>
    jQuery(function($) {
        let clear = document.getElementById('clearForm');

        clear.addEventListener('click', function(event) {
            event.preventDefault();

            this.form.search.value = '';

            this.form.com_content_filter_state.value  = '';
            this.form.com_content_filter_access.value = '';

            document.getElementById('com_content_filter_show_empty_descriptions').checked = false;

            this.form.submit();
        })
    });
</script>
