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
function jb_com_uninstall()
{
?>
<div class="header"><?php echo JText::sprintf('SEO_REMOVED_TITLE'); ?></div>
<p>
<?php echo JText::sprintf('SEO_REMOVED_DESC'); ?>
</p>
<?php
return true;
}
if(!function_exists("com_uninstall")){
    function com_uninstall(){
        return jb_com_uninstall();
    }
}
?>
