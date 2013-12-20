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
<script>

  function updateKeywords(){
	  var text = window.parent.document.getElementById('<?php echo $this->textareaElement?>').value;
	  var keywords = document.getElementById('calc_keywords').value;
      var req = new Ajax('index3.php?option=com_seoboss&task=get_density',
              {method:'post',
          data:{
      text:text,
      keywords:keywords

          },
      onComplete: function() {
              document.getElementById('density_results').innerHTML = this.response.text;
          }
      }).request();
      }
  </script>
    <table class="adminform">
  <tr>
    <td valign="top" width="100"><label for="title"> <?php echo JText::_( 'SEO_KEYWORDS' )?> </label></td>
    <td><input style="width:100%" class="inputbox" type="text" name="calc_keywords" id="calc_keywords" value="" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
        <button onclick="updateKeywords();"><?php echo JText::_( 'SEO_REFRESH' )?></button>
    </td>
  </tr>
  </table>
  <div id="density_results"></div>

  <script>

    var keywords = "";
    if(window.parent.document.getElementById('metakeywords')){
    	keywords = window.parent.document.getElementById('metakeywords').value;
    }
    document.getElementById('calc_keywords').value = keywords;

    if(keywords!=''){
    	updateKeywords();

    }
</script>
