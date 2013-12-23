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
<form id="adminForm" name="adminForm" id="adminForm" method="post" action="">
<input type="hidden" name="option" id="option" value="com_seoboss"/>
<input type="hidden" name="task" id="task" value="settings"/>
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
        <?php echo JText::_('SEO_SITE_SETTINGS')?>
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">
        <table class="adminform" >
          <tr>
            <td valign="top" width="100"><label for="domain"><?php echo JText::_('SEO_SITE_DOMAIN_NAME') ?></label></td>
            <td>
            <input type="text" class="inputbox" id="domain" name="domain" value="<?php echo $this->settings->domain ?>" />
            </td>
            <td>
                <?php echo JText::_('SEO_SITE_DOMAIN_NAME_DESC') ?>
            </td>
          </tr>
          <tr>
            <td valign="top" width="100"><label for="google_server"><?php echo JText::_('SEO_GOOGLE_SERVER') ?></label></td>
            <td>
            <input type="text" class="inputbox" id="google_server" name="google_server" value="<?php echo $this->settings->google_server ?>" />
            </td>
            <td>
            <?php echo JText::_('SEO_GOOGLE_SERVER_DESC') ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
        Metadata generation
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        <table class="adminform" >
        <tr>
        <td valign="top" width="100">
          <label for="max_description_length">Meta descirption max length</label></td>
            <td>
            <input type="text" class="inputbox" id="max_description_length"
            name="max_description_length" value="<?php echo $this->settings->max_description_length ?>" />
            </td>
            <td>
                Max length of generated meta description tag.

            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
        <?php echo JText::_('SEO_TEXT_REPLACEMENTS')?>
      </a>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner">
        <table class="adminform" >
          <tr>
            <td valign="top" width="100"><label for="hilight_keywords"><?php echo JText::_('SEO_HIGHLIGHT_KEYWORDS') ?></label></td>
            <td>
            <input type="checkbox" class="inputbox" id="hilight_keywords" name="hilight_keywords"
               <?php if ($this->settings->hilight_keywords){ ?>checked <?php }?>/>
               </td>
               <td>
        <?php echo JText::_('SEO_HIGHLIGHT_KEYWORDS_DESC') ?>
               </td>
          </tr>
          <tr>
            <td valign="top" width="100"><label for="hilight_tag"><?php echo JText::_('SEO_HIGHLIGHT_BY_TAG') ?> :</label></td>
            <td>
            <select class="inputbox" id="hilight_tag" name="hilight_tag" >
            <?php foreach($this->allowed_hilight_tags as $tag){?>
            <option value="<?php echo $tag?>" <?php if ($tag == $this->settings->hilight_tag){?>selected="true"<?php }?>>&lt;<?php echo $tag?>&gt;</option>
            <?php }?>
            </select>
            </td>
            <td>
            <?php echo JText::_('SEO_HIGHLIGHT_BY_TAG_DESC') ?>
            </td>
          </tr>
          <tr>
            <td valign="top" width="100"><label for="hilight_class"><?php echo JText::_('SEO_CLASS') ?></label></td>
            <td>
            <input type="text" class="inputbox"
                id="hilight_class" name="hilight_class" value="<?php echo $this->settings->hilight_class ?>" />
                </td>
           <td>
           <?php echo JText::_('SEO_CLASS_DESC') ?>
           </td>
          </tr>
          <tr>
            <td valign="top" width="100"><label for="hilight_skip"><?php echo JText::_('SEO_SKIP_TAGS') ?></label></td>
            <td>
            <input type="text" class="inputbox"
                id="hilight_skip" name="hilight_skip" value="<?php echo $this->settings->hilight_skip ?>" />
                </td>
                <td>
           <?php echo JText::_('SEO_SKIP_TAGS_DESC') ?>
           </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
        <?php echo JText::_('SEO_GOOGLE_PING_SERVICE')?>
      </a>
    </div>
    <div id="collapseFour" class="accordion-body collapse">
      <div class="accordion-inner">
        <table class="adminform" >
            <tr>
                <td>
                    <input type="checkbox" class="inputbox" id="enable_google_ping" name="enable_google_ping" <?php if ($this->settings->enable_google_ping){ ?>checked <?php }?> />
                </td>
                <td>
                    <?php echo JText::_('SEO_GOOGLE_PING_SERVICE_DESC') ?>
                </td>
            </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFive">
        <?php echo JText::_('SEO_FRONTPAGE_SETTINGS')?>
      </a>
    </div>
    <div id="collapseFive" class="accordion-body collapse">
      <div class="accordion-inner">
        <table class="adminform" >
            <tr>
                <td>
                    <input type="radio" name="frontpage_meta" value="0" <?php if ($this->settings->frontpage_meta==0){ ?>
                    checked="true"
                    <?php }?>
                    />
                    <?php echo JText::_('SEO_FRONTPAGE_SPECIFY_METATAGS'); ?>
                </td>
                <td align="left">
                    <label for="frontpage_title"><?php echo JText::_('SEO_FRONTPAGE_TITLE'); ?>:</label>
                    <br/>
                    <input type="text" class="inputbox" id="frontpage_title" size="50"
                        name="frontpage_title" value="<?php echo htmlspecialchars($this->settings->frontpage_title) ; ?>"/>
                     <br/>
                     <label for="frontpage_meta_title"><?php echo JText::_('SEO_FRONTPAGE_META_TITLE'); ?>:</label>
                    <br/>
                    <input type="text" class="inputbox" id="frontpage_meta_title" size="50"
                        name="frontpage_meta_title" value="<?php echo htmlspecialchars($this->settings->frontpage_meta_title) ; ?>"/>
                     <br/>
                    <label for="frontpage_keywords"><?php echo JText::_('SEO_FRONTPAGE_KEYWORDS'); ?>:</label>
                    <br/>
                    <input type="text" class="inputbox" id="frontpage_keywords" size="50"
                        name="frontpage_keywords" value="<?php echo htmlspecialchars($this->settings->frontpage_keywords); ?>"/>
                     <br/>
                    <label for="frontpage_description"><?php echo JText::_('SEO_FRONTPAGE_DESCRIPTION'); ?>:</label>
                    <br/>
                    <input type="text" class="inputbox" id="frontpage_description" size="50"
                        name="frontpage_description" value="<?php echo htmlspecialchars($this->settings->frontpage_description); ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="frontpage_meta" value="1" <?php if ($this->settings->frontpage_meta==1){?>
                    checked="true"
                    <?php }?>
                    />
                    <?php echo JText::_('SEO_FRONTPAGE_AUTO_METATAGS'); ?>
                </td>
                <td>
                </td>
            </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseSix">
        SEO Boss Anywhere
      </a>
    </div>
    <div id="collapseSix" class="accordion-body collapse">
      <div class="accordion-inner">
        <table class="adminform">
          <tr>
            <td><label for="sa_enable">Enable SEO Boss Anywhere</label></td>
            <td><input type="checkbox" name="sa_enable" value="1" id="sa_enable" <?php if ($this->settings->sa_enable){?>checked="true" <?php }?>/></td>
          </tr>
          <tr>
            <td><label for="sa_users">Authorized Frontend users who can edit metadata</label></td>
            <td><input type="text" name="sa_users" id="sa_users" value="<?php echo $this->settings->sa_users?>"/></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseSeven">
        SEO Boss Extensions
      </a>
    </div>
    <div id="collapseSeven" class="accordion-body collapse">
      <div class="accordion-inner">
       <table class="adminform" >
       <tr>
         <th>Name</th>
         <th>Description</th>
         <th width="10%">Enabled</th>
       </tr>
       <?php
       foreach($this->features as $feature){
         $img="components/com_seoboss/images/";
         if ($feature["enabled"]){
           $img.="enabled.png";
           $alt="Disable extension";
         }else{
           $img.="disabled.png";
           $alt="Enable extension";
         }
         ?>
         <tr>
           <td><?php echo $feature["name"]?></td>
           <td><?php echo $feature["description"]?></td>
           <td><a href="index.php?option=com_seoboss&task=<?php echo $feature["enabled"]?"disablefeature":"enablefeature";?>&id=<?php echo $feature["component"]?>" ><img src="<?php echo $img?>" alt="<?php echo $alt?>"/></a></td>
         </tr>
         <?php
       }
       ?>
       </table>
      </div>
    </div>
  </div>
</div>
</form>
