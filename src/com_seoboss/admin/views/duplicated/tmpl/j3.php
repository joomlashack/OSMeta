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
<table  class="table table-striped" id="articleList">
	    <thead>
	    <tr>
	    	<th class="title" width="40%"><?php echo JText::_('SEO_ORIGINAL_URL'); ?></th>
	    	<th class="title"  width="40%">Actual URL</th>
	    	<th class="title"  width="10%">Action</th>
	    	<th class="title" width="60"><?php echo JText::_('SEO_EDIT'); ?></th>
	    	<th class="title" width="60"><?php echo JText::_('SEO_DELETE'); ?></th>
	    </tr>
	    </thead>
	    <tbody>
		<?php

		foreach($this->duplicated_pages as $page){?>
		<tr class="row0">
			<td><?php echo $page->url; ?></td>
			<td><?php echo $page->canonical_url;?></td>
			<td><?php switch($page->action){
			  case SeobossCanonicalURL::$ACTION_CANONICAL:
			    echo "Canonical URL";
			    break;
			  case SeobossCanonicalURL::$ACTION_REDIRECT:
			    echo "301 Redirect";
			    break;
			  case SeobossCanonicalURL::$ACTION_NOINDEX:
			    echo "Noindex";
			    break;
			} ?></td>
			<td><a href="index.php?option=com_seoboss&task=duplicated_edit&id=<?php echo $page->id?>"><?php echo JText::_('SEO_EDIT'); ?></a></td>
			<td><a href="index.php?option=com_seoboss&task=duplicated_delete&id=<?php echo $page->id?>"><?php echo JText::_('SEO_DELETE'); ?></a></td>
		</tr>
		<?php } ?>
	    </tbody>
</table>
<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_seoboss"/>
	<input type="hidden" name="task" value="duplicated_pages"/>
</form>
