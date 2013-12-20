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

jimport( 'joomla.application.component.model' );

class SeobossModelPanel extends JBModel
{
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

	}

	public function getSystemInfo(){
	    $system = array();
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT COUNT(*) from #__seoboss_keywords");
	    $system['keywords'] = $db->loadResult();
	    $db->setQuery("SELECT COUNT(*) from #__content");
	    $system['pages'] = $db->loadResult();
	    $db->setQuery("SELECT COUNT(*) from #__seoboss_redirects");
	    $system['links'] = $db->loadResult();
	    return $system;
	}

	public function getCode(){
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT joomboss_registration_code from #__seoboss_settings");
	    $code = $db->loadResult();
	    return $code;
	}

	public function setCode( $code ){
	    $db = JFactory::getDBO();
	    $db->setQuery("UPDATE #__seoboss_settings SET joomboss_registration_code=".$db->quote( $code ) );
	    $code = $db->query();
	}
}
