<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2022 Joomlashack.com. All rights reserved
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

use Alledia\OSMeta\Pro\Fields;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

HTMLHelper::_('script', 'com_osmeta/jquery.osmetacharcount.min.js', ['relative' => true]);
HTMLHelper::_('behavior.core');
HTMLHelper::_('formbehavior.chosen', 'select');

$colspan = $this->extension->isPro() ? 5 : 4;
?>

<form action="index.php?option=com_osmeta&type=<?php echo $this->itemType; ?>"
      method="post"
      name="adminForm"
      id="adminForm">

    <div class="row">
        <div class="col-md-12">
        <table style="width: 100%;">
            <tr>
                <td class="ost-filters">
                    <?php echo $this->filter; ?>
                </td>
            </tr>
        </table>

        <?php if (count($this->metatagsData) == 0) : ?>
            <div class="alert alert-warning">
                <h4 class="alert-heading"><?php echo Text::_('MESSAGE') ?></h4>
                <div class="alert-message"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS') ?></div>
            </div>

        <?php else : ?>
            <table class="table table-striped adminlist" id="articleList">
                <thead class="hidden-phone">
                <tr>
                    <th style="width: 2%;">
                        <input type="checkbox"
                               name="checkall-toggle"
                               value=""
                               title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
                               onclick="Joomla.checkAll(this)"/>
                    </th>

                    <th class="title title-column"
                        style="width: <?php echo $this->extension->isPro() ? '20%;' : '25%;'; ?>">
                        <?php echo HTMLHelper::_(
                            'grid.sort',
                            Text::_('COM_OSMETA_TITLE_LABEL'),
                            'title',
                            $this->order_Dir,
                            $this->order,
                            'view'
                        ); ?>
                    </th>

                    <?php if ($this->extension->isPro()) : ?>
                        <?php Fields::additionalFieldsHeader($this->order_Dir, $this->order); ?>
                    <?php endif; ?>

                    <th class="title" style="width: <?php echo $this->extension->isPro() ? '24%;' : '35%;'; ?>">
                        <?php echo HTMLHelper::_(
                            'grid.sort',
                            Text::_('COM_OSMETA_SEARCH_ENGINE_TITLE_LABEL'),
                            'meta_title',
                            $this->order_Dir,
                            $this->order,
                            'view'
                        ); ?>
                    </th>

                    <th class="title" style="width: <?php echo $this->extension->isPro() ? '24%;' : '35%;'; ?>">
                        <?php echo HTMLHelper::_(
                            'grid.sort',
                            Text::_('COM_OSMETA_DESCRIPTION_LABEL'),
                            'meta_desc',
                            $this->order_Dir,
                            $this->order,
                            'view'
                        ); ?>
                    </th>
                </tr>

                </thead>
                <tr class="hidden-phone">
                    <td style="width: 20px;"></td>
                    <td class="title">
                        <?php echo Text::_('COM_OSMETA_TITLE_DESC') ?>
                    </td>

                    <?php if ($this->extension->isPro()) : ?>
                        <td>
                            <?php echo Text::_('COM_OSMETA_ALIAS_DESC') ?>
                        </td>
                    <?php endif; ?>

                    <td style="vertical-align: top;">
                        <?php echo Text::sprintf(
                            'COM_OSMETA_SEARCH_ENGINE_TITLE_DESC',
                            $this->extension->params->get('meta_title_limit', 70)
                        ); ?>
                    </td>
                    <td style="vertical-align: top;">
                        <?php echo Text::sprintf(
                            'COM_OSMETA_DESCRIPTION_DESC',
                            $this->extension->params->get('meta_description_limit', 160)
                        ); ?>
                    </td>
                </tr>

                <?php
                $k = 1;
                for ($i = 0, $n = count($this->metatagsData); $i < $n; $i++) {
                    $row     = $this->metatagsData[$i];
                    $checked = HTMLHelper::_('grid.id', $i, $row->id);
                    ?>
                    <tr class="<?php echo 'row' . $k; ?>">
                        <td class="hidden-phone text-center"><?php echo $checked; ?>
                            <input type="hidden" name="ids[]" value="<?php echo $row->id ?>"/>
                        </td>
                        <td>
                            <a id="title_<?php echo $row->id ?>" href="<?php echo $row->edit_url; ?>">
                                <?php echo $row->title; ?>
                            </a>
                            <a class="external-link" href="<?php echo $row->view_url; ?>" target="_blank">

                            </a>
                        </td>

                        <?php if ($this->extension->isPro()) : ?>
                            <?php Fields::additionalFields($row); ?>
                        <?php endif; ?>

                        <td class="field-column">
                            <textarea name="metatitle[]"
                                      class="char-count metatitle"><?php echo $row->metatitle; ?></textarea>
                        </td>
                        <td class="field-column">
                            <textarea name="metadesc[]"
                                      class="char-count metadesc"><?php echo $row->metadesc; ?></textarea>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
                <tfoot>
                <tr>
                    <td colspan="<?php echo $colspan; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
                </tr>
                </tfoot>
            </table>
        <?php endif; ?>

        <input type="hidden" name="option" value="com_osmeta"/>
        <input type="hidden" name="task" value="view"/>
        <input type="hidden" name="type" value="<?php echo $this->itemType; ?>"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $this->order ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_Dir ?>"/>
    </div>
    </div>

</form>

<script>
    let hashCode = function(s) {
        return s.split('').reduce(function(a, b) {
            a = ((a << 5) - a) + b.charCodeAt(0);
            return a & a
        }, 0);
    }

    let hashedInitialValues = '',
        getHashedValues;

    (function($) {
        // Get a hash from the value of all fields, concatenated
        getHashedValues = function() {
            let str = ''

            $('#articleList input, #articleList textarea').each(function() {
                str += $(this).val();
            });

            return hashCode(str);
        };

        $('#articleList textarea.char-count.metatitle').osmetaCharCount({
            limit        : <?php echo $this->extension->params->get('meta_title_limit', 70); ?>,
            message      : '<?php echo Text::_('COM_OSMETA_TITLE_TOO_LONG'); ?>',
            charStr      : '<?php echo Text::_('COM_OSMETA_CHAR'); ?>',
            charPluralStr: '<?php echo Text::_('COM_OSMETA_CHARS'); ?>'
        });

        $('#articleList textarea.char-count.metadesc').osmetaCharCount({
            limit        : <?php echo $this->extension->params->get('meta_description_limit', 160); ?>,
            message      : '<?php echo Text::_('COM_OSMETA_DESCR_TOO_LONG'); ?>',
            charStr      : '<?php echo Text::_('COM_OSMETA_CHAR'); ?>',
            charPluralStr: '<?php echo Text::_('COM_OSMETA_CHARS'); ?>'
        });
    })(jQuery);

    // Store the initial hash
    hashedInitialValues = getHashedValues();

    // Overwrite the native submit action, to catch the cancel task
    let nativeSubmitButton = Joomla.submitbutton;
    Joomla.submitbutton    = function(pressbutton) {
        if (pressbutton !== 'save') {
            let hashedValues = getHashedValues();

            // Do we have any modified field?
            if (hashedInitialValues !== hashedValues) {
                if (!confirm('<?php echo Text::_('COM_OSMETA_CONFIRM_CANCEL'); ?>')) {
                    return;
                }
            }
        }

        nativeSubmitButton(pressbutton);
    };
</script>
