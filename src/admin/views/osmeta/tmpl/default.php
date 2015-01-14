<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

$colspan = $this->extension->isPro() ? 5 : 4;
?>

<form action="index.php?option=com_osmeta&type=<?php echo $this->itemType; ?>" method="post" name="adminForm" id="adminForm">

    <?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->submenu; ?>
        </div>
    <?php endif; ?>

    <div id="j-main-container" class="span10">
        <table width="100%">
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
                        <th width="2%"><input type="checkbox" name="toggle" value=""
                            onclick="checkAll(<?php echo count($this->metatagsData); ?>);" />
                        </th>
                    <?php else : ?>
                        <th width="2%"><input type="checkbox" name="checkall-toggle" value=""
                            title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                    <?php endif; ?>

                    <th class="title title-column" width="<?php echo $this->extension->isPro() ? '20%' : '25%'; ?>">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_TITLE_LABEL'), 'title', $this->order_Dir,
                            $this->order, "view"); ?>
                    </th>

                    <?php if ($this->extension->isPro()) : ?>
                        <?php echo Alledia\OSMeta\Pro\Fields::additionalFieldsHeader($this->order_Dir, $this->order); ?>
                    <?php endif; ?>

                    <th class="title" width="<?php echo $this->extension->isPro() ? '24%' : '35%'; ?>">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_LABEL'), 'meta_title',
                            $this->order_Dir, $this->order, "view"); ?>
                    </th>

                    <th class="title" width="<?php echo $this->extension->isPro() ? '24%' : '35%'; ?>">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_DESCRIPTION_LABEL'), 'meta_desc',
                            $this->order_Dir, $this->order, "view"); ?>
                    </th>
                </tr>

            </thead>
            <tr>
                <td width="20"></td>
                <td class="title">
                    <?php echo JText::_('COM_OSMETA_TITLE_DESC') ?>
                </td>

                <?php if ($this->extension->isPro()) : ?>
                    <td>
                        <?php echo JText::_('COM_OSMETA_ALIAS_DESC') ?>
                    </td>
                <?php endif; ?>

                <td valign="top">
                    <?php echo JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_DESC') ?>
                </td>
                <td valign="top">
                    <?php echo JText::_('COM_OSMETA_DESCRIPTION_DESC') ?>
                </td>
            </tr>


            <?php
            $k = 1;
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
                        <a class="external-link" href="<?php echo $row->view_url; ?>" target="_blank">
                            <?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
                                <img src="../media/com_osmeta/images/external-link.png" width="14" height="14" />
                            <?php else : ?>
                                <span class="icon-out-2"></span>
                            <?php endif; ?>
                        </a>
                    </td>

                    <?php if ($this->extension->isPro()) : ?>
                        <?php echo Alledia\OSMeta\Pro\Fields::additionalFields($row); ?>
                    <?php endif; ?>

                    <td class="field-column">
                        <textarea name="metatitle[]" class="char-count metatitle"><?php echo $row->metatitle; ?></textarea>
                    </td>
                    <td class="field-column">
                        <textarea name="metadesc[]" class="char-count metadesc"><?php echo $row->metadesc; ?></textarea>
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
        <input type="hidden" name="option" value="com_osmeta" />
        <input type="hidden" name="task" value="view" />
        <input type="hidden" name="type" value="<?php echo $this->itemType; ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $this->order ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_Dir ?>" />
    </div>

</form>

<div id="footer" class="span12">
    <div>
        <a href="https://www.alledia.com">
            <img src="../media/com_osmeta/admin/images/alledia_logo_150x43.png" />
        </a>
    </div>
    <br />
    <div>
        OSMeta is built by&nbsp;
        <a href="https://www.alledia.com">Alledia</a>
    </div>
</div>

<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
    <script src="../media/com_osmeta/js/jquery.js"></script>
<?php endif; ?>

<script src="../media/com_osmeta/js/jquery.osmetacharcount.min.js"></script>

<script>
    var hashCode = function(s) {
        return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
    }

    var hashedInitialValues = '',
        getHashedValues;

    (function($) {
        // Get a hash from the value of all fields, concatenated
        getHashedValues = function() {
            var str = ''

            $('#articleList input, #articleList textarea').each(function() {
                str += $(this).val();
            });

            return hashCode(str);
        };

        $('#articleList textarea.char-count.metatitle').osmetaCharCount({
            limit: 70,
            message: '<?php echo JText::_("COM_OSMETA_TITLE_TOO_LONG"); ?>',
            charStr: '<?php echo JText::_("COM_OSMETA_CHAR"); ?>',
            charPluralStr: '<?php echo JText::_("COM_OSMETA_CHARS"); ?>'
        });

        $('#articleList textarea.char-count.metadesc').osmetaCharCount({
            limit: 160,
            message: '<?php echo JText::_("COM_OSMETA_DESCR_TOO_LONG"); ?>',
            charStr: '<?php echo JText::_("COM_OSMETA_CHAR"); ?>',
            charPluralStr: '<?php echo JText::_("COM_OSMETA_CHARS"); ?>'
        });
    })(jQuery);

    // Store the initial hash
    hashedInitialValues = getHashedValues();

    // Overwrite the native submit action, to catch the cancel task
    var nativeSubmitButton = Joomla.submitbutton;
    Joomla.submitbutton = function(pressbutton) {
        if (pressbutton !== 'save') {
            var hashedValues = getHashedValues();

            // Do we have any modified field?
            if (hashedInitialValues !== hashedValues) {
                if (!confirm('<?php echo JText::_("COM_OSMETA_CONFIRM_CANCEL"); ?>')) {
                    return;
                }
            }
        }

        nativeSubmitButton(pressbutton);
    };
</script>
