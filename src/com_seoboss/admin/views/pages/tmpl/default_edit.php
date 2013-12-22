<?php
/*------------------------------------------------------------------------
# SEO Boss Pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();
?>
<script>
var modalsubmit= function () {
    var form = document.adminModalForm;
    $('adminModalForm').send({
        onRequest: function(){
            // Show loading div.
            //fx.loading.start(0,1);
        },
        onSuccess: function(){
            // Hide loading and show success for 3 seconds.
            window.top.setTimeout(
                    'window.parent.document.getElementById(\'sbox-window\').close()', 300);
        },
        onFailure: function(){
            // Hide loading and show fail for 3 seconds.
            //showHide('fail');
        }
    });

 }
</script>
<form id="adminModalForm" name="adminModalForm" method="post">
<input type="hidden" name="id" value="<?php echo $this->data["id"]?>"/>
<input type="hidden" name="item_type" value="<?php echo $this->itemType; ?>"/>
<input type="hidden" name="option" value="com_seoboss"/>
<input type="hidden" name="task" value="pages_save_text"/>
<input type="hidden" name="format" value="raw"/>
<table class="adminform" style="margin:5px;width:590px;">
  <tr>
    <td valign="top" width="100"><label for="title"><?php echo JText::_('SEO_TITLE');?></label></td>
    <td>
    <textarea style="width:99%" class="inputbox" id="title" name="title"><?php echo $this->data["title"];?></textarea>
  </tr>
  <tr>
    <td valign="top"><label for="metatitle"><?php echo JText::_('SEO_META_TITLE');?></label></td>
    <td>
    <textarea style="width:99%" class="inputbox" id="metatitle" name="metatitle"><?php echo $this->data["metatitle"];?></textarea>
  </tr>
  <tr>
    <td valign="top"><label for="metadescription"><?php echo JText::_('SEO_META_DESCRIPTION');?></label></td>
    <td><textarea style="width:99%" class="inputbox" id="metadescription" name="metadescription"><?php echo $this->data["metadescription"];?></textarea></td>
  </tr>
  <tr>
    <td valign="top"><label for="metakeywords"><?php echo JText::_('SEO_META_KEYWORDS');?></label></td>
    <td><textarea style="width:99%" class="inputbox" id="metakeywords" name="metakeywords"><?php echo $this->data["metakeywords"];?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
         <input type="submit" value="<?php echo JText::_('SEO_SAVE');?>" />
         <button type="button" onclick="window.parent.SqueezeBox.close()">
            <?php echo JText::_('SEO_CANCEL');?></button>
    </td>
  </tr>
</table>
</form>
