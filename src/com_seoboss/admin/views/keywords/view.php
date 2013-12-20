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

class SeobossViewKeywords extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }

    function display($tpl=null)
    {
        switch($tpl){
          case "edit":
            JToolBarHelper::save("save_keyword");
            JToolBarHelper::apply("save_keyword");
            JToolBarHelper::cancel();
            break;
          case null:
            JToolBarHelper::title( JText::_( 'SEO_KEYWORDS_MANAGER' ),
                'joomboss_keywords.png' );
            JToolBarHelper::custom('keywords_update_stat', 'apply', '', JText::_( 'SEO_UPDATE_GOOGLE_STAT' ), true);
            JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation/51-seo-boss-keywords-manager?tmpl=component');
        }
        parent::display($tpl);
    }

}
