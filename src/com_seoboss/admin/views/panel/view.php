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

class SeobossViewPanel extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }

    function display($tpl=null)
    {
      $document = JFactory::getDocument();
      JHTML::_('behavior.tooltip');
      $document->addStyleSheet(JURI::base(true)."/components/com_seoboss/views/panel/tmpl/css/style.css");
      JToolBarHelper::title( JText::_( 'SEO_CONTROL_PANEL' ),'joomboss.png' );
      JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation?tmpl=component');
      parent::display($tpl);
    }

}
