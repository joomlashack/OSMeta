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
jimport("joomla.version");
$version = new JVersion();
if($version->RELEASE == "1.5"){
?>
<script src="components/com_seoboss/js/Observer.js"></script>
<script src="components/com_seoboss/js/Autocompleter.js"></script>
<script>
window.addEvent('domready', function() {
  new Autocompleter.Local('tag_name', ['abstract', 
	    'author', 'classification', 'copyright', 'description', 'distribution', 'doc-class', 'doc-rights', 'doc-type','DownloadOptions', 'expires', 'generator', 'googlebot', 'keywords', 'MSSmartTagsPreventParsing', 'name', 'owner', 'progid', 'rating', 'refresh', 'reply-to', 'resource-type', 'revisit-after', 'robots', 'Template'],
		{
	                                                		'minLength': 0, // We need at least 1 character
	                                                		'selectMode': 'type-ahead', // Instant completion
	                                                		'multiple': false // Tag support, by default comma separated
	                                                	    });
});
</script>
<?php }?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <fieldset class="adminform">
    <table class="admintable">
	  <tr>
        <td width="100" align="right"><?php echo JText::_('SEO_META_TAG_NAME');?>:</td>
        <td><input type="text" name="tag_name" id="tag_name" value="<?php echo isset($this->tag_name)?$this->tag_name:""?>"/></td>
      </tr>
      <tr>
        <td width="100" align="right"><?php echo JText::_('SEO_META_TAG_VALUE');?>:</td>
        <td><input type="text" name="tag_value" id="tag_value" value="<?php echo isset($this->tag_value)?$this->tag_value:""?>"/></td>
      </tr>
      <tr>
      	<td colspan="2" align="center">
      	</td>
      </tr>
    </table>
  </fieldset>
  <input type="hidden" name="tag_id" value="<?php echo isset($this->tag_id)?$this->tag_id:""?>" id="tag_id"/>
  <input type="hidden" name="option" value="com_seoboss"/>
   <input type="hidden" name="task" value="settings_default_tags"/>
</form>
