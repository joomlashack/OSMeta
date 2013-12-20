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

class SeobossViewMetatags extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }
    
    function display($tpl=null)
    {
      JToolBarHelper::title( JText::_( 'SEO_META_TAGS_MANAGER' ),
          'joomboss_metatag.png' );
      JToolBarHelper::custom('metatags_copy_keywords_to_title', 'apply', '', JText::_( 'SEO_COPY_KEYWORDS_TO_TITLE' ), true);
      JToolBarHelper::custom('metatags_copy_title_to_keywords', 'apply', '', JText::_( 'SEO_COPY_TITLE_TO_KEYWORDS' ), true);
      JToolBarHelper::custom('metatags_copy_item_title_to_keywords', 'apply', '', JText::_( 'SEO_COPY_ITEM_TITLE_TO_KEYWORDS' ), true);
      JToolBarHelper::custom('metatags_copy_item_title_to_title', 'apply', '', JText::_( 'SEO_COPY_ITEM_TITLE_TO_TITLE' ), true);
      JToolBarHelper::custom('metatags_generare_descriptions', 'apply', '', JText::_( 'SEO_GENERATE_DESCRIPTIONS' ), true);
      JToolBarHelper::custom('metatags_clear_browser_titles', 'apply', '', JText::_( 'SEO_CLEAR_BROWSER_TITLES' ), true);
      JToolBarHelper::save("metatags_save");
      JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation/50-seo-boss-meta-tags-manager?tmpl=component');
        parent::display($tpl);
    }
}