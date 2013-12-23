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
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if (is_array($this->requests)){?>
<table class="adminlist">
	    <thead>
	    <tr>
	    	<th class="title"><?php echo JText::_("SEO_REQUEST");?></th>
	    	<th class="title" width="60"><?php echo JText::_("SEO_CREATED");?></th>
	    	<th class="title" width="60"><?php echo JText::_("SEO_STATUS");?></th>
	    </tr>
	    </thead>
	    <tbody>
		<?php
		foreach($this->requests as $request){?>
		<tr class="row0">
			<td><a href="index.php?option=com_seoboss&task=helpdesk_view_request&id=<?php echo $request->id;?>"><?php echo $request->subject ?></a></td>
			<td><?php echo $request->created ?></td>
			<td><?php echo $request->status ?></td>
		</tr>
		<?php } ?>
	     </tbody>
	</table>
<?php } else{?>
<?php echo $this->requests; ?>
<?php }?>
<input type="hidden" name="option" value="com_seoboss" />
<input type="hidden" name="task" value="helpdesk" />
</form>
