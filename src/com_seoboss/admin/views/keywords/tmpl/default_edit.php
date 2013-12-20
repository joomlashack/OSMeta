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
		$editor = JFactory::getEditor();
		JHTML::_('behavior.calendar');
		?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<fieldset class="adminform"><legend><?php echo JText::_( 'SEO_DETAILS' ); ?>:</legend>
<table class="admintable">
	<tr>
		<td width="100" align="right" class="key"><?php echo JText::_( 'SEO_NAME' ); ?>:</td>
		<td><input class="text_area" type="text" name="name" id="name"
			size="50" maxlength="250" value="<?php echo $this->row->name;?>" /></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key"><?php echo JText::_( 'SEO_URL' ); ?>:</td>
		<td><input class="text_area" type="text" name="url" id="url"
			size="50" maxlength="250" value="<?php echo $this->row->url;?>" /></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key"><?php echo JText::_( 'SEO_ACTIVE' ); ?>:</td>
		<td><?php
		echo JHTML::_('select.booleanlist', 'published',
'class="inputbox"', $this->row->published);
		?></td>
	</tr>

</table>
</fieldset>
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> <input
	type="hidden" name="option" value="com_seoboss" /> <input
	type="hidden" name="task" value="" /></form>
