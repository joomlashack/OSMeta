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
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
  <fieldset class="adminform"><legend><?php echo JText::_("SEO_BACKUP_CREATE");?></legend>
  <p>
	<?php echo JText::_("SEO_BACKUP_DESC");?>
  </p>
  <a href="index.php?option=com_seoboss&task=backup_download&format=raw" ><img src="<?php echo JURI::base(); ?>components/com_seoboss/images/download_snapshot.png" title="Download Snapshot" alt="Download Snapshot"/></a>
  </fieldset>
  <fieldset class="adminform"><legend><?php echo JText::_("SEO_BACKUP_RESTORE");?></legend>
  <p>
		<?php echo JText::_("SEO_BACKUP_UPLOAD_DESC");?>
  </p>
		<strong><?php echo JText::_("SEO_BACKUP_FILE");?>:</strong><br/>
		<input type="file" name="backup" size="49" style="margin-left:5px"/><br/>
        <input type="image" src="<?php echo JURI::base(); ?>components/com_seoboss/images/upload_snapshot.png" value="Upload Snapshot" title="Upload Snapshot" alt="Upload Shanpshot" style="border: none;outline: none;"/>
  </fieldset>
  <input type="hidden" name="option" value="com_seoboss"/>
  <input type="hidden" name="task" value="backup_upload"/>
</form>


