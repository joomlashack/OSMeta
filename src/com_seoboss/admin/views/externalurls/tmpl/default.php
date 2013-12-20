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
<script type="text/javascript" src="components/com_seoboss/js/url_list.js" ></script>
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
		<table id="attributeX_table_0" cellpadding="0" cellspacing="0" border="0" class="adminform" width="30%">
	<tbody width="30%">
		
		<?php
		$i = 1;
		foreach ($this->urls as $url){
		
			
			
			 
			// ... and the properties and prices in the second
			
					?>
			  	  <tr id="attributeX_tr_<?php echo "0_" . $i ; ?>">
			<td width="5%">&nbsp;<?php echo $i;?></td>
			<td width="10%" align="left"><?php
					echo JText::_( 'SEO_NEW_REDIRECT_URL' ) ;
					?></td>
			<td align="left" width="20%"><input type="text"
				name="attributeX[0][value][]"
				value="<?php
					echo $url->url ;
					?>" size="40" /></td>
			<td align="left" width="5%"><?php
					echo JText::_( 'SEO_NEW_URL_TARGET' ) ;
					?></td>
			<td align="left" >
				<select name="attributeX[0][price][]">
					<option value="0" <?php if($url->target == '0') echo "selected='selected'";?> ><?php echo JText::_("SEO_SAME_WINDOW");?></option>
					<option value="1" <?php if($url->target == '1') echo "selected='selected'";?> ><?php echo JText::_("SEO_NEW_WINDOW");?></option>
				</select>
				<a href="javascript:deleteProperty(<?php
					echo "0" ;
					?>,'<?php
					echo "0_" . $i ;
					?>');">X</a></td>
		</tr>
			  	  <?php
				  $i++;
				  }
				
			?>
			  </tbody>
</table>

<input	type="hidden" name="option" value="com_seoboss" /> 
<input type="hidden" name="task" value="" />
</form>