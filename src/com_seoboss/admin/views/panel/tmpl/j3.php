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

function quickiconButton($link, $image, $text, $path=null, $target='', $onclick='') {
  if ($target != '') {
    $target = 'target="' .$target. '"';
  }
  if ($onclick != '') {
    $onclick = 'onclick="' .$onclick. '"';
  }
  if ($path === null || $path === '') {
    $path = '/components/com_seoboss/images/';
  }
  ?>
		<div class="icon-wrapper">
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target;?>  <?php echo $onclick;?>>
				    <img src="<?php echo JURI::base(true).$path.$image; ?>" alt="<?php echo $text; ?>"/>
					<span><?php echo $text;?></span>
				</a>
			</div>
		</div>
		<?php
	 }


		?>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminform">
				<tr>
					<td width="55%" valign="top">
						<div id="k2QuickIcons">
						<?php
						$link = 'index.php?option=com_seoboss&amp;task=panel';
						quickiconButton($link, '48x48-jb.png', JText::_('SEO_CONTROL_PANEL'));

						$link = 'index.php?option=com_seoboss&amp;task=metatags_view';
						quickiconButton($link, '48x48-metatag.png', JText::_('SEO_META_TAGS_MANAGER_TITLE'));

						$link = 'index.php?option=com_seoboss&task=keywords_view';
						quickiconButton($link, '48x48-keywords.png', JText::_('SEO_KEYWORDS_MANAGER_TITLE'));

						$link = 'index.php?option=com_seoboss&task=pages_manager';
						quickiconButton($link, '48x48-html.png', JText::_('SEO_PAGES_MANAGER'));

						$link = 'index.php?option=com_seoboss&task=url_list';
						quickiconButton($link, '48x48-redirect.png', JText::_('SEO_EXTERNAL_LINK'));

						$link = 'index.php?option=com_seoboss&task=backup_manager';
						quickiconButton($link, '48x48-backup.png', JText::_('SEO_BACKUP_MANAGER'));

                        $link = 'index.php?option=com_seoboss&amp;task=settings';
                        quickiconButton($link, '48x48-settings.png', JText::_('SEO_SETTINGS'));

                        $link = 'index.php?option=com_seoboss&amp;task=helpdesk';
                        quickiconButton($link, '48x48-helpdesk.png', JText::_('SEO_HELPDESK'));
						?>
					</div>
					</td>
					<td width="45%" valign="top">
					<div style="width: 100%">
					<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
						<?php echo JText::_('SEO_WELCOME');?>
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">
						<?php echo JText::_('SEO_WELCOME_DESC');?>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
	      <?php echo JText::_('SEO_SYSTEM_INFORMATION'); ?>
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        <?php echo JText::sprintf('SEO_SYSTEM_INFORMATION_DESC',$this->system['keywords'],$this->system['pages'],$this->system['links']);?>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
        <?php echo JText::_('SEO_HELP_AND_SUPPORT');?>
      </a>
    </div>
		<div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner">
        <?php echo JText::sprintf('SEO_HELP_AND_SUPPORT_DESC',"<a href=\"http://joomboss.com/forum\">forums</a>");?>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
        <?php echo JText::_('SEO_GOOGLE_PING_STATUS');?>
      </a>
    </div>
    <div id="collapseFour" class="accordion-body collapse">
      <div class="accordion-inner">
        <?php echo JText::sprintf('SEO_GOOGLE_PING_STATUS_DESC',"<a href=\"http://support.google.com/blogsearch/bin/answer.py?hl=en&answer=91323&rd=1\" target=\"_blank\">Google Blog Search Pinging Service</a>");?>
        <?php if (count($this->pingStatus) > 0) { ?>
                            <table>
                                 <tr>
                                <th>
                                    <?php echo JText::_('SEO_PAGE'); ?>
                                </th>
                                <th>
                                    <?php echo JText::_('SEO_PINGED'); ?>
                                </th>
                                <th>
                                    <?php echo JText::_('SEO_STATUS'); ?>
                                </th>
                                </tr>
                            <?php foreach($this->pingStatus as $status) {?>
                                <tr>
                                    <td><a href="<?php echo $status->url?>" target="_blank"><?php echo $status->title?></a></td>
                                    <td><?php echo $status->date ?></td>
                                    <td>
                                         <?php if ($status->response_code == 0) { ?>
                                        <span style="color:green"><?php echo JText::_('SEO_OK'); ?></span>
                                        <?php } else { ?>
                                        <span style="color:red"><?php echo JText::_('SEO_ERROR'); ?>: <?php echo $status->response_text ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                        <?php } ?>
                        </div>
                      </div>
                    </div>
            </div>
					</div>
					</td>
				</tr>
			</table>
            <?php if (!$this->code){ ?>
           <table class="adminform">
                <tr>
                    <td>
                        <?php echo JText::_('SEO_NOT_REGISTERED_DESC'); ?>
                    </td>
                </tr>
            </table>
            <?php } ?>
            <table class="adminform">
                <tr>
                    <td width="100" align="center" style="text-align: center"><img src="components/com_seoboss/images/elephant.png"/></td>
                    <td>
                        <h3><?php echo JText::_('SEO_HELP_US'); ?></h3>
                        <?php echo JText::_('SEO_HELP_US_DESC'); ?>
                    </td>
                </tr>
            </table>
			<input type="hidden" name="option" value="com_seoboss" />
			<input type="hidden" name="task" value="cpanel.show" />
			<input type="hidden" name="boxchecked" value="0" />
			</form>

