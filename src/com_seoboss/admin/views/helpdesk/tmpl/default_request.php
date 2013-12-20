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
<form name="adminForm" id="adminForm">
<h1><?php echo $this->request->subject?></h1>
<strong><?php echo JText::_("SEO_STATUS");?>: </strong><?php echo $this->request->status; ?><br/>
<strong><?php echo JText::_("SEO_CREATED");?>: </strong><?php echo $this->request->created; ?><br/>
<strong><?php echo JText::_("SEO_LAST_MODIFIED");?>: </strong><?php echo $this->request->modified; ?><br/>
<?php echo $this->request->body?>
<input type="hidden" name="option" value="com_seoboss" />
<input type="hidden" name="task" value="helpdesk" />
</form>
