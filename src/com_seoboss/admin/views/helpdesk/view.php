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

jimport('joomla.application.component.view');

/**
 * Updates Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */

class SeobossViewHelpdesk extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }

    function display($tpl=null){
      switch($tpl){
        case "request":
          JToolBarHelper::title( JText::_( 'SEO_HELPDESK_REQUEST' ),'helpdesk.png' );
          JToolBarHelper::cancel('helpdesk');
          break;
        case "new_request":
          JToolBarHelper::title( JText::_( 'SEO_NEW_HELPDESK_REQUEST' ),'helpdesk.png' );
          JToolBarHelper::custom('helpdesk_submit_request', 'save', '', JText::_( 'SEO_SUBMIT_REQUEST' ), false);
          JToolBarHelper::cancel('helpdesk');
          break;
        default:
          JToolBarHelper::title( JText::_( 'SEO_HELPDESK' ),'helpdesk.png' );
          JToolBarHelper::custom('helpdesk_new_request', 'new', '', JText::_( 'SEO_NEW_REQUEST' ), false);
      }
      parent::display($tpl);
    }
}
