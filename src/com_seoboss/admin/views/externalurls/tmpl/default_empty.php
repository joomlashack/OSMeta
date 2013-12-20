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
<script type="text/javascript" src="/administrator/components/com_seoboss/js/url_list.js" ></script>
<form name="adminForm" >
<?php
echo '<input type="hidden" name="js_lbl_title" value="' . JText::_( 'PHPSHOP_PRODUCT_FORM_TITLE' ) . '" />
<input type="hidden" name="js_lbl_property" value="' . JText::_( 'SEO_NEW_REDIRECT_URL' ) . '" />
<input type="hidden" name="js_lbl_property_new" value="' . JText::_( 'SEO_NEW_REDIRECT' ) . '" />
<input type="hidden" name="js_lbl_attribute_new" value="' .
    JText::_( 'PHPSHOP_PRODUCT_FORM_ATTRIBUTE_NEW' ) . '" />
<input type="hidden" name="js_lbl_attribute_delete" value="' .
    JText::_( 'PHPSHOP_PRODUCT_FORM_ATTRIBUTE_DELETE' ) . '" />
<input type="hidden" name="js_lbl_price" value="' . JText::_( 'SEO_NEW_URL_TARGET' ) . '" />' ;
?>
			<a href="javascript: newProperty(0)"><?php
			echo JText::_( 'SEO_NEW_REDIRECT' ) ;
			?></a>
			<table width="100%"> <tr><td>
<table id="attributeX_table_0" cellpadding="0" cellspacing="0" border="0" class="adminform" width="30%">
	<tbody width="30%">

		<tr id="attributeX_tr_0_0">
			<td width="5%">1</td>
			<td width="10%" align="left"><?php
			echo JText::_( 'SEO_NEW_REDIRECT_URL' ) ;
			?></td>
			<td align="left" width="20%"><input type="text"
				name="attributeX[0][value][]" value="" size="40" /></td>
			<td align="left" width="5%"><?php
			echo JText::_( 'SEO_NEW_URL_TARGET' ) ;
			?></td>
			<td align="left" width="30%" >
			<select name="attributeX[0][price][]">
				<option value="0" ><?php echo JText::_("SEO_SAME_WINDOW");?></option>
				<option value="1" ><?php echo JText::_("SEO_NEW_WINDOW");?></option>
			</select>
			</td>
		</tr>
	</tbody>
</table>
<input	type="hidden" name="option" value="com_seoboss" />
<input type="hidden" name="task" value="" />
</form>
</td>
<td width="30%" valign="top">
<dl id="system-message">
<dt class="message"><?php echo JText::_("SEO_INFO");?></dt>
<dd class="message message fade">
	<ul>
		<li><?php echo JText::_("SEO_REDIRECT_TITLE");?></li>
	</ul>

</dd>
</dl>
<ul>
	<li>	<?php echo JText::_("SEO_REDIRECT_DESC");?>
	</li>
</ul>
</td>
</tr>
</table>
