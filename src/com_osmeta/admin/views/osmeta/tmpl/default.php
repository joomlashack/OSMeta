<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="filter_order" value="<?php echo $this->order ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_Dir ?>" />
    <table width="100%">
        <tr>
            <td align="right">
                <label><?php echo JText::_('COM_OSMETA_SELECT_CONTENT_TYPE') ?>:&nbsp;</label>
                <select name="type" onchange="document.adminForm.submit();">
                    <?php
                    foreach ($this->availableTypes as $typeId => $typeName) :
                        $selected = $typeId == $this->itemType ? 'selected="true"' : '';
                        ?>
                        <option value="<?php echo $typeId?>" <?php echo $selected; ?>>
                            <?php echo $typeName["name"]?>
                        </option>
                        <?php
                    endforeach;
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">
                <?php echo $this->filter; ?>
            </td>
        </tr>
    </table>
    <table class="table table-striped adminlist" id="articleList">
        <thead>
            <tr>
                <?php if (version_compare(JVERSION, '3.0', 'le')) : ?>
                    <th width="20"><input type="checkbox" name="toggle" value=""
                        onclick="checkAll(<?php echo count($this->metatagsData); ?>);" />
                    </th>
                <?php else : ?>
                    <th width="20"><input type="checkbox" name="checkall-toggle" value=""
                        title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                <?php endif; ?>
                <th class="title title-column">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_TITLE_LABEL'), 'title', $this->order_Dir,
                        $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_LABEL'), 'meta_title',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>

                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_DESCRIPTION_LABEL'), 'meta_desc',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_KEYWORDS_LABEL'), 'meta_key',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>
            </tr>

        </thead>
        <tr>
            <td width="20"></td>
            <td class="title"></td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_DESC') ?>
            </td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_DESCRIPTION_DESC') ?>
            </td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_KEYWORDS_DESC') ?>
            </td>
        </tr>

        <tr class="subheader">
            <td colspan="6"><?php echo JText::_('COM_OSMETA_HOMEPAGE_METADATA'); ?></td>
        </tr>
        <tr id="homeMetaDataRow" class="row0">
            <td></td>
            <td>
                <input type="radio" name="home_metadata_source" id="home_metadata_source_default" value="default"
                    <?php echo $this->homeMetatagsData->source === 'default' ? 'checked="checked"' : ''; ?> />
                <label for="home_metadata_source_default" title="<?php echo JText::_('COM_OSMETA_FEATURED_DEFAULT_VALUES'); ?>">
                    <?php echo JText::_('COM_OSMETA_DEFAULT_VALUES'); ?>
                </label>

                <br />
                <input type="radio" name="home_metadata_source" id="home_metadata_source_custom" value="custom"
                    <?php echo $this->homeMetatagsData->source === 'custom' ? 'checked="checked"' : ''; ?> />
                <label for="home_metadata_source_custom" title="<?php echo JText::_('COM_OSMETA_FEATURED_CUSTOM_VALUES'); ?>">
                    <?php echo JText::_('COM_OSMETA_CUSTOM_VALUES'); ?>
                </label>

                <br />
                <input type="radio" name="home_metadata_source" id="home_metadata_source_featured" value="featured"
                    <?php echo $this->homeMetatagsData->source === 'featured' ? 'checked="checked"' : ''; ?> />
                <label for="home_metadata_source_featured" title="<?php echo JText::_('COM_OSMETA_FEATURED_VALUES_TITLE'); ?>">
                    <?php echo JText::_('COM_OSMETA_FEATURED_VALUES'); ?>
                </label>
            </td>
            <td>
                <textarea cols="20" rows="3" name="home_metatitle" <?php echo $this->homeFieldsDisabledAttribute; ?>><?php echo $this->homeMetatagsData->metaTitle; ?></textarea>
            </td>
            <td>
                <textarea cols="20" rows="3" name="home_metadesc" <?php echo $this->homeFieldsDisabledAttribute; ?>><?php echo $this->homeMetatagsData->metaDesc; ?></textarea>
            </td>
            <td>
                <textarea cols="20" rows="3" name="home_metakey" <?php echo $this->homeFieldsDisabledAttribute; ?>><?php echo $this->homeMetatagsData->metaKey; ?></textarea>
            </td>
        </tr>

        <tr class="subheader">
            <td colspan="6" rowspan="" headers=""><?php echo JText::_(ucfirst($this->itemTypeShort)); ?> <?php echo JText::_('COM_OSMETA_METADATA'); ?></td>
        </tr>

        <?php
        jimport('joomla.filter.output');

        $k = 0;
        for ($i = 0, $n = count($this->metatagsData); $i < $n; $i++) {
            $row = $this->metatagsData[$i];
            $checked = JHTML::_('grid.id', $i, $row->id);
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td><?php echo $checked; ?>
                    <input type="hidden" name="ids[]" value="<?php echo $row->id ?>"/>
                </td>
                <td>
                    <a id="title_<?php echo $row->id ?>" href="<?php echo $row->edit_url; ?>">
                        <?php echo $row->title; ?>
                    </a>
                </td>
                <td>
                    <textarea cols="20" rows="3" name="metatitle[]"><?php echo $row->metatitle; ?></textarea>
                </td>
                <td>
                    <textarea cols="20" rows="3" name="metadesc[]"><?php echo $row->metadesc; ?></textarea>
                </td>
                <td>
                    <textarea cols="20" rows="3" name="metakey[]"><?php echo $row->metakey; ?></textarea>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        <tfoot>
            <tr>
                <td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="option" value="com_osmeta" />
    <input type="hidden" name="task" value="view" />
    <input type="hidden" name="boxchecked" value="0" />
</form>

<div id="footer">
    <div>
        <a href="http://www.ostraining.com">
            <img src="../media/com_osmeta/admin/images/ostraining_logo_250x50.png" />
        </a>
    </div>
    <br />
    <div>
        OSMeta is built by&nbsp;
        <a href="http://www.ostraining.com">OSTraining</a>
    </div>
    <div>
        OSMeta is a simplified version of&nbsp;
        <a href="http://extensions.joomla.org/extensions/site-management/seo-a-metadata/meta-data/16440">SEOBoss</a>
    </div>
</div>

<script>
    <?php if (version_compare(JVERSION, '3.0', 'le')) : ?>
        (function($) {
            var homeMetadataSourceChange = function() {
                var $this = $(this);
                var fields = $$('#homeMetaDataRow textarea');
                var value = $this.value;

                fields.each(function(el) {
                    el.readOnly = !(value === 'custom');
                });
            };

            $$('#home_metadata_source_default, #home_metadata_source_custom, #home_metadata_source_featured').addEvent(
                'change',
                homeMetadataSourceChange
            );
        })($);
    <?php else: ?>
        (function($) {
            var homeMetadataSourceChange = function() {
                var $this = $(this);
                var fields = $('#homeMetaDataRow textarea');
                var value = $this.val();

                fields.attr('readonly', !(value === 'custom'));

            };

            $('#home_metadata_source_default, #home_metadata_source_custom, #home_metadata_source_featured').on(
                'change',
                homeMetadataSourceChange
            );
        })(jQuery);
    <?php endif; ?>
</script>
