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
<br/>
    <table class="adminform">

  <tr>
    <th valign="top" width="100"> <?php echo JText::_('SEO_KEYWORD');?> </th>
    <th valign="top"> <?php echo JText::_('SEO_FREQUENCY');?></th>
    <th valign="top" > <?php echo JText::_('SEO_DENSITY');?></th>
  </tr>
  <?php foreach($this->keywords_data as $data){?>
  <tr>
    <td valign="top" width="100"> <?php echo $data["keyword"]?> </td>
    <td valign="top" > <?php echo $data["frequency"]?></td>
    <td valign="top" > <?php echo $data["density"]?> </td>
  </tr>
  <?php } ?>
  </table>
