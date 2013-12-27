<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
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
                    foreach ($this->availableTypes as $typeId => $typeName) {
                        ?>
                        <option value="<?php echo $typeId?>"
                            <?php if ($typeId == $this->itemType) {?>selected="true"<?php }?>>
                            <?php echo $typeName["name"]?></option>
                        <?php
                    }
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
    <script>
        function createTitleTag(id)
        {
            $('title_tag_' + id).value=$('title_' + id).innerHTML.trim();
        }
    </script>
    <table class="adminlist">
        <thead>
            <tr>
                <th width="20"><input type="checkbox" name="toggle" value=""
                    onclick="checkAll(<?php echo count($this->metatagsData); ?>);" />
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_TITLE_LABEL'), 'title', $this->order_Dir,
                        $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_BROWSER_TITLE_LABEL'), 'title_tag', $this->order_Dir,
                        $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_LABEL'), 'meta_title',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_KEYWORDS_LABEL'), 'meta_key',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_OSMETA_DESCRIPTION_LABEL'), 'meta_desc',
                        $this->order_Dir, $this->order, "view"); ?>
                </th>
            </tr>

        </thead>

        <tr>
            <td width="20"></td>
            <td class="title"></td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_BROWSER_TITLE_DESC');
                ?>
            </td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_SEARCH_ENGINE_TITLE_DESC')?>
            </td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_KEYWORDS_DESC')?>
            </td>
            <td valign="top">
                <?php echo JText::_('COM_OSMETA_DESCRIPTION_DESC')?>
            </td>
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
                    <a id="title_<?php echo $row->id ?>" href="<?php echo $row->edit_url; ?>"><?php echo $row->title; ?></a>
                </td>
                <td valign="top">
                    <?php
                    ?>
                    <a title="Copy contents from item Title" style="float:left" href="#" onclick="createTitleTag('<?php echo $row->id ?>');return false;"><img src="../media/com_osmeta/admin/images/rightarrow.png"/></a>
                    <textarea id="title_tag_<?php echo $row->id ?>" cols=20 rows="3" name="title_tag[]"><?php echo $row->title_tag; ?></textarea>
                    <?php
                    ?>
                </td>
                <td>
                    <textarea cols=20 rows="3" name="metatitle[]"><?php echo $row->metatitle; ?></textarea>
                </td>
                <td>
                    <textarea cols=20 rows="3" name="metakey[]"><?php echo $row->metakey; ?></textarea>
                </td>
                <td>
                    <textarea cols=20 rows="3" name="metadesc[]"><?php echo $row->metadesc; ?></textarea>
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
    <div>OSMeta is built by <a href="http://www.ostraining.com">OSTraining</a></div>
    <div>OSMeta is a simplified version of <a href="http://extensions.joomla.org/extensions/site-management/seo-a-metadata/meta-data/16440">SEOBoss</a></div>
</div>
