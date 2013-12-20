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
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <fieldset class="adminform">
    <table class="admintable" width="100%">
	  <tr>
        <td width="100" align="right"><?php echo JText::_('SEO_ORIGINAL_URL'); ?>:</td>
        <td><label for="url" style="display: inline; color: #999;"><?php echo JURI::root(); ?></label>
        <input style="display:inline;margin-bottom:0px;width:60%" type="text" name="url" id="url" value="<?php echo isset($this->page->url)?$this->page->url:""?>"/></td>
      </tr>
      <tr>
        <td width="100" align="right">Actual URL:</td>
        <td><label for="canonical_url" style="display: inline; color: #999;"><?php echo JURI::root();?></label>
        <input type="text" name="canonical_url" id="canonical_url" style="display:inline;margin-bottom:0px;width:60%" value="<?php echo isset($this->page->canonical_url)?$this->page->canonical_url:""?>"/></td>
      </tr>
      <tr>
        <td width="100" align="right">Action:</td>
        <td>
          <select name="action" id="action">
            <option value="<?php echo SeobossCanonicalURL::$ACTION_CANONICAL?>" 
             <?php if(isset($this->page->action)&&
                 $this->page->action==SeobossCanonicalURL::$ACTION_CANONICAL) echo "selected=\"true\"";?>
            >Use Canonical URL</option>
            <option value="<?php echo SeobossCanonicalURL::$ACTION_REDIRECT?>" 
             <?php if(isset($this->page->action)&&
                 $this->page->action==SeobossCanonicalURL::$ACTION_REDIRECT) echo "selected=\"true\"";?>
            >Use 301 Redirect</option>
            <option value="<?php echo SeobossCanonicalURL::$ACTION_NOINDEX?>" 
             <?php if(isset($this->page->action)&&
                 $this->page->action==SeobossCanonicalURL::$ACTION_NOINDEX) echo "selected=\"true\"";?>
            >Use Noindex</option>
          </select>
        </td>
      </tr>
      
      <tr>
      	<td colspan="2" align="center">
      	</td>
      </tr>
    </table>
  </fieldset>
  <input type="hidden" name="id" value="<?php echo isset($this->page->id)?$this->page->id:""?>" id="id"/>
  <input type="hidden" name="option" value="com_seoboss"/>
   <input type="hidden" name="task" value="duplicated"/>
</form>
<p>
Duplicate content issues occur when the same content is accessible from multiple URLs.
</p>
<p>
The canonical link element, which can be inserted into the &lt;head&gt; section of a web page, to allow webmasters to prevent these issues.[4] The canonical link element helps webmasters make clear to the search engines which page should be credited as the original.
</p>
<p>
According to Google, the canonical link element is not considered to be a directive, but a hint that the web crawler will "honor strongly".
</p>
<p>
While the canonical link element has its benefits, sometimes the search engine prefers the use of 301 redirects. 
</p>