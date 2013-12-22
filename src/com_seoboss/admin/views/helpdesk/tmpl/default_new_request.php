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
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="admintable">
    <tr>
        <td widrh="20%" nowrap="nowrap"><label for="subject"><?php echo JText::_('SEO_REQUEST_TITLE')?>:</label></td>
        <td><input class="inputbox" type="text" name="subject" id="subject"/></td>
    </tr>
    <tr>
        <td widrh="20%" nowrap="nowrap"><label for="body"><?php echo JText::_('SEO_REQUEST_TEXT')?>:</label></td>
        <td><textarea class="inputbox" name="body" id="body" cols="40" rows="10" style="width:90%"></textarea></td>
    </tr>

</table>
<input type="hidden" name="option" value="com_seoboss"/>
<input type="hidden" name="task" value="helpdesk_submit_request"/>
</form>
