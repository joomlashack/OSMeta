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
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<fieldset class="adminform">
    <legend><?php echo JText::_('SEO_DEFAULT_META_TAGS'); ?></legend>
    <?php echo JText::_('SEO_DEFAULT_META_TAGS_DESC'); ?>
</fieldset>
<table class="table table-striped" >
	    <thead>
	    <tr>
	    	<th class="title" width="90"><?php echo JText::_('SEO_NAME'); ?></th>
	    	<th class="title"><?php echo JText::_('SEO_VALUE'); ?></th>
	    	<th class="title" width="60"><?php echo JText::_('SEO_EDIT'); ?></th>
	    	<th class="title" width="60"><?php echo JText::_('SEO_DELETE'); ?></th>
	    </tr>
	    </thead>
	    <tbody>
		<?php 
		
		foreach($this->metatags as $metatag){?>
		<tr class="row0">
			<td><?php echo $metatag->name ?></td>
			<td><?php echo $metatag->value ?></td>
			<td><a href="index.php?option=com_seoboss&task=settings_edit_tag&tag_id=<?php echo $metatag->id?>"><?php echo JText::_('SEO_EDIT'); ?></a></td>
			<td><a href="index.php?option=com_seoboss&task=settings_delete_tag&tag_id=<?php echo $metatag->id?>"><?php echo JText::_('SEO_DELETE'); ?></a></td>
		</tr>
		<?php } ?>
	    </tbody>
</table>
<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_seoboss"/>
	<input type="hidden" name="task" value="settings_default_tags"/>
</form>
