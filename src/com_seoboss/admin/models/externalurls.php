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

class SeobossModelExternalUrls extends JBModel
{
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

	}
	
	public function getUrls(){
	    $db = JFactory::getDBO();
        $query = "SELECT * FROM #__seoboss_redirects";
        $db->setQuery( $query );
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        return $rows;
	}
	
	public function saveUrls($array){
	    $db = JFactory::getDBO();
	    if($array){
	        $db->setQuery("TRUNCATE #__seoboss_redirects");
	        $db->query();
	        if ($db->getErrorNum()) {
	            echo $db->stderr();
	            return false;
	        }
	        for($i=0; $n = count($array['attributeX']['0']['value']), $i < $n+1; $i++){
	            echo "<br>".(isset($array['attributeX']['0']['value'][$i])?$array['attributeX']['0']['value'][$i]:"")."<br>";
	            if(isset($array['attributeX']['0']['value'][$i]) && $array['attributeX']['0']['value'][$i] != ''){
	                $array['attributeX']['0']['value'][$i] = str_replace("http://", '' , $array['attributeX']['0']['value'][$i]);
	                $array['attributeX']['0']['value'][$i] = str_replace("www.", "", $array['attributeX']['0']['value'][$i]);
	                $query = "INSERT INTO #__seoboss_redirects VALUES ('".$i."', '".$array['attributeX']['0']['value'][$i]."', '".$array['attributeX']['0']['price'][$i]."', '')";
	                $db->setQuery($query);
	                $db->query();
	            }
	        }
	    
	    }
	}
	
}