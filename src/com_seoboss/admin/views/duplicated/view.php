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

jimport('joomla.application.component.view');

/**
 * Updates Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */

class SeobossViewDuplicated extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }

    function display($tpl=null)
    {
      switch($tpl){
        case null:
          JToolBarHelper::title(JText::_('SEO_DUPLICATED_PAGES'),'joomboss_settings.png');
          JToolBarHelper::custom('duplicated_edit', 'new', '', 'New', false);
          break;
        case "edit_form":
          JToolBarHelper::title(JText::_('SEO_DUPLAICATED_PAGES_EDIT'),'joomboss_settings.png');
          JToolBarHelper::custom('duplicated_save', 'save', '', JText::_('SEO_SAVE'), false);
          JToolBarHelper::cancel('duplicated_pages');
          break;
      }
      parent::display($tpl);
    }

}
