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

class SeobossViewOptions extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }

    function display($tpl=null)
    {
      switch($tpl){
        case "tags_table":
          JToolBarHelper::title( JText::_( 'SEO_SITE_DEFAULT_METATAGS' ),'joomboss_settings.png' );
          JToolBarHelper::custom('settings_edit_tag', 'new', '', 'New', false);
          break;
        case "tags_form":
          JToolBarHelper::title( JText::_( 'SEO_EDIT_SITE_DEFAULT_META_TAG' ),'joomboss_settings.png' );
          JToolBarHelper::custom('settings_save_tag', 'save', '', JText::_( 'SEO_SAVE' ), false);
          JToolBarHelper::cancel('settings_default_tags');
          break;
        case "code":
          JToolBarHelper::title( JText::_( 'SEO_MANAGE_REGISTRATION_CODE' ),'joomboss_settings.png' );
          JToolBarHelper::custom('options_update_code', 'save', '', JText::_( 'SEO_SAVE' ), false);
          JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation/53-seo-boss-settings?tmpl=component');
          break;
        case null:
          JToolBarHelper::title( JText::_( 'SEO_SETTINGS' ),'joomboss_settings.png' );
          JToolBarHelper::custom('settings_save', 'save', '', JText::_( 'SEO_SAVE' ), false);
          JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation/53-seo-boss-settings?tmpl=component');
          break;
      }
        parent::display($tpl);
    }

}
