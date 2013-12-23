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
<form name="adminForm" action="index.php" method="post" id="adminForm">
  <input type="hidden" name="option" value="com_seoboss"/>
  <input type="hidden" name="task" value="options_update_code"/>
  <?php echo JText::_('SEO_REGISTRATION_CODE_DESC') ?>
  <ul>
  	<li><?php echo JText::sprintf('SEO_CODE_STEP1', "<a href=\"http://joomboss.com\">http://joomboss.com</a>");?></li>
  	<li><?php echo JText::sprintf('SEO_CODE_STEP2', "<a href=\"http://joomboss.com\">http://joomboss.com</a>","<a href=\"http://joomboss.com/?option=com_seobossupdater&task=register\">http://joomboss.com/?option=com_seobossupdater&task=register</a>");?>
  	</li>
  	<li><?php echo JText::_('SEO_CODE_STEP3');?></li>
  </ul>
  <fieldset class="adminform">
    <legend><?php echo JText::_('SEO_CODE_TITLE');?></legend>
    <table class="admintable">
	  <tr>
        <td width="100" align="right"><?php echo JText::_('SEO_CODE');?>:</td>
        <td><input type="text" name="code" size="50" value="<?php echo $this->code?>"/></td>
      </tr>
    </table>
  </fieldset>
</form>
